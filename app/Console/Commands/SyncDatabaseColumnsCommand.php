<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseSchemaSyncService;

class SyncDatabaseColumnsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:sync-columns 
                            {--table= : Filter nama tabel yang ingin diperiksa/disinkronkan}
                            {--dry-run : Menampilkan rencana penambahan kolom tanpa mengeksekusi perubahan ke database}
                            {--force : Menjalankan penambahan kolom secara langsung tanpa prompt konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memeriksa dan menambahkan kolom/tabel yang kurang berdasarkan skema rujukan (simemek3.sql) secara idempoten';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseSchemaSyncService $syncService)
    {
        $tableFilter = $this->option('table');
        $isDryRun = (bool) $this->option('dry-run');
        $isForce = (bool) $this->option('force');

        $this->info('=== PROSAKIP: Sinkronisasi Kolom & Skema Database ===');
        $this->line("Rujukan : config/reference_schema.json (berdasarkan simemek3.sql)");
        if ($tableFilter) {
            $this->line("Filter  : Tabel `{$tableFilter}`");
        }

        // 1. Jalankan inspeksi
        $inspection = $syncService->inspect($tableFilter);

        $totalMissingCols = array_sum(array_map('count', $inspection['missing_columns']));
        $totalMissingTables = count($inspection['missing_tables']);

        $this->newLine();
        $this->line("Hasil Pemeriksaan:");
        $this->line("- Total tabel diperiksa   : {$inspection['tables_checked']}");
        $this->line("- Total kolom diperiksa   : {$inspection['columns_checked']}");
        $this->line("- Kolom sudah ada (aman)  : {$inspection['existing_columns']}");
        $this->line("- Tabel belum ada         : {$totalMissingTables}");
        $this->line("- Kolom belum ada (kurang): {$totalMissingCols}");
        $this->newLine();

        if ($totalMissingTables === 0 && $totalMissingCols === 0) {
            $this->info("✓ SEMUA STRUKTUR LENGKAP: Seluruh tabel dan kolom sudah ada di database target. Tidak ada penambahan yang diperlukan.");
            return Command::SUCCESS;
        }

        // Tampilkan daftar tabel yang belum ada
        if ($totalMissingTables > 0) {
            $this->warn("Tabel yang belum ada di database:");
            $tableRows = [];
            foreach ($inspection['missing_tables'] as $tbl => $info) {
                $tableRows[] = [$tbl, count($info['columns']) . ' kolom'];
            }
            $this->table(['Nama Tabel', 'Jumlah Kolom'], $tableRows);
        }

        // Tampilkan daftar kolom yang belum ada
        if ($totalMissingCols > 0) {
            $this->warn("Kolom yang belum ada (akan ditambahkan):");
            $colRows = [];
            foreach ($inspection['missing_columns'] as $tbl => $cols) {
                foreach ($cols as $c) {
                    $colRows[] = [
                        $tbl,
                        $c['column'],
                        \Illuminate\Support\Str::limit($c['definition'], 40),
                        $c['after'] ? 'AFTER ' . $c['after'] : 'FIRST',
                    ];
                }
            }
            $this->table(['Tabel', 'Nama Kolom', 'Definisi', 'Posisi'], $colRows);
        }

        if ($isDryRun) {
            $this->info("[DRY-RUN] Mode simulasi aktif. Tidak ada perubahan yang dieksekusi ke database.");
            return Command::SUCCESS;
        }

        if (!$isForce && !$this->confirm("Apakah Anda ingin mengeksekusi penambahan {$totalMissingCols} kolom dan {$totalMissingTables} tabel sekarang?", true)) {
            $this->warn("Operasi dibatalkan oleh pengguna.");
            return Command::SUCCESS;
        }

        $this->info("Mengeksekusi penambahan struktur ke database...");
        $result = $syncService->sync($tableFilter, false);

        if (!empty($result['executed'])) {
            $this->newLine();
            $this->info("Perubahan Berhasil Dieksekusi:");
            foreach ($result['executed'] as $item) {
                $this->line("  ✓ " . $item['message']);
            }
        }

        if (!empty($result['errors'])) {
            $this->newLine();
            $this->error("Terdapat kegagalan eksekusi:");
            foreach ($result['errors'] as $err) {
                $this->error("  ✗ [{$err['table']}] " . $err['message']);
            }
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info("✓ SINKRONISASI SUKSES: Seluruh kolom dan tabel yang kurang telah berhasil ditambahkan ke database.");
        return Command::SUCCESS;
    }
}
