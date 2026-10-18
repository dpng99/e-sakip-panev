<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSchemaSyncService
{
    protected ?string $configPath;

    public function __construct(?string $configPath = null)
    {
        $this->configPath = $configPath ?: config_path('reference_schema.json');
    }

    /**
     * Get reference schema dictionary.
     */
    public function getReferenceSchema(): array
    {
        if (!file_exists($this->configPath)) {
            throw new \RuntimeException("Berkas reference_schema.json tidak ditemukan pada: {$this->configPath}");
        }

        $data = json_decode(file_get_contents($this->configPath), true);
        if (!is_array($data)) {
            throw new \RuntimeException("Format reference_schema.json tidak valid.");
        }

        return $data;
    }

    /**
     * Inspect database against reference schema and find missing tables & columns.
     */
    public function inspect(?string $filterTable = null): array
    {
        $schema = $this->getReferenceSchema();
        $results = [
            'tables_checked'   => 0,
            'columns_checked'  => 0,
            'missing_tables'   => [],
            'missing_columns'  => [],
            'existing_columns' => 0,
        ];

        foreach ($schema as $tableName => $tableData) {
            if ($filterTable && strtolower($filterTable) !== strtolower($tableName)) {
                continue;
            }

            $results['tables_checked']++;

            if (!Schema::hasTable($tableName)) {
                $results['missing_tables'][$tableName] = [
                    'create_sql' => $tableData['create_sql'],
                    'columns'    => array_keys($tableData['columns'] ?? []),
                ];
                continue;
            }

            $existingCols = Schema::getColumnListing($tableName);
            $existingColsLower = array_map('strtolower', $existingCols);

            foreach ($tableData['columns'] as $colName => $colInfo) {
                $results['columns_checked']++;

                if (!in_array(strtolower($colName), $existingColsLower)) {
                    $results['missing_columns'][$tableName][] = [
                        'column'     => $colName,
                        'definition' => $colInfo['definition'],
                        'after'      => $colInfo['after'],
                    ];
                } else {
                    $results['existing_columns']++;
                }
            }
        }

        return $results;
    }

    /**
     * Sync schema: add missing tables and columns without altering existing ones.
     */
    public function sync(?string $filterTable = null, bool $dryRun = false): array
    {
        $inspection = $this->inspect($filterTable);
        $executed = [];
        $errors = [];

        // 1. Buat tabel yang belum ada
        foreach ($inspection['missing_tables'] as $table => $info) {
            $sql = preg_replace('/^CREATE TABLE (?:IF NOT EXISTS )?/i', 'CREATE TABLE IF NOT EXISTS ', $info['create_sql']);
            if (!$dryRun) {
                try {
                    DB::statement($sql);
                    $executed[] = [
                        'type'    => 'create_table',
                        'table'   => $table,
                        'status'  => 'success',
                        'message' => "Tabel `{$table}` berhasil dibuat",
                    ];
                } catch (\Throwable $e) {
                    $errors[] = [
                        'type'    => 'create_table',
                        'table'   => $table,
                        'status'  => 'error',
                        'message' => $e->getMessage(),
                    ];
                }
            } else {
                $executed[] = [
                    'type'    => 'create_table (dry-run)',
                    'table'   => $table,
                    'sql'     => $sql,
                ];
            }
        }

        // 2. Tambah kolom yang belum ada (kolom yang sudah ada dilewati)
        foreach ($inspection['missing_columns'] as $table => $columns) {
            foreach ($columns as $col) {
                $colName = $col['column'];
                $def = $col['definition'];
                $after = $col['after'];
                $afterClause = $after ? "AFTER `{$after}`" : "FIRST";

                $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$colName}` {$def} {$afterClause}";

                if (!$dryRun) {
                    try {
                        DB::statement($sql);
                        $executed[] = [
                            'type'    => 'add_column',
                            'table'   => $table,
                            'column'  => $colName,
                            'status'  => 'success',
                            'message' => "Kolom `{$table}`.`{$colName}` berhasil ditambahkan",
                        ];
                    } catch (\Throwable $e) {
                        $errors[] = [
                            'type'    => 'add_column',
                            'table'   => $table,
                            'column'  => $colName,
                            'status'  => 'error',
                            'message' => $e->getMessage(),
                        ];
                    }
                } else {
                    $executed[] = [
                        'type'   => 'add_column (dry-run)',
                        'table'  => $table,
                        'column' => $colName,
                        'sql'    => $sql,
                    ];
                }
            }
        }

        return [
            'dry_run'          => $dryRun,
            'tables_checked'   => $inspection['tables_checked'],
            'columns_checked'  => $inspection['columns_checked'],
            'existing_columns' => $inspection['existing_columns'],
            'added_tables'     => count($inspection['missing_tables']),
            'added_columns'    => array_sum(array_map('count', $inspection['missing_columns'])),
            'executed'         => $executed,
            'errors'           => $errors,
        ];
    }
}
