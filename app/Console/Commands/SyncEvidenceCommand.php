<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Helpers\LkeEvidenceHelper;

class SyncEvidenceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lke:sync-evidence {--satker= : Specific Satker ID to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize legacy bukti_dukung and lke_satker_bukti tables with accurate buktidukung_id mapping';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting bidirectional evidence synchronization...');
        $satkerFilter = $this->option('satker');

        // 1. Backfill buktidukung_id in lke_satker_bukti
        $query = DB::table('lke_satker_bukti');
        if ($satkerFilter) {
            $query->where('id_satker', $satkerFilter);
        }

        $totalLke = $query->count();
        $this->info("Processing {$totalLke} records in lke_satker_bukti...");
        $bar = $this->output->createProgressBar($totalLke);
        $bar->start();

        $chunkSize = 1000;
        $updatedLke = 0;

        DB::table('lke_satker_bukti')
            ->when($satkerFilter, fn($q) => $q->where('id_satker', $satkerFilter))
            ->orderBy('id')
            ->chunk($chunkSize, function ($rows) use ($bar, &$updatedLke) {
                foreach ($rows as $row) {
                    $cleanKode = LkeEvidenceHelper::cleanKriteriaKode($row->kriteria_id);
                    $filename = basename($row->link_bukti_dukung);
                    $bId = LkeEvidenceHelper::resolveBuktiId($filename, $cleanKode);

                    if ($bId !== null && ($row->buktidukung_id != $bId || $row->kriteria_id !== $cleanKode)) {
                        DB::table('lke_satker_bukti')
                            ->where('id', $row->id)
                            ->update([
                                'kriteria_id'    => $cleanKode,
                                'buktidukung_id' => $bId,
                                'kode_bukti'     => (string)$bId,
                                'updated_at'     => now(),
                            ]);
                        $updatedLke++;
                    }
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->info("Updated {$updatedLke} records in lke_satker_bukti.");

        // 2. Synchronize from bukti_dukung to lke_satker_bukti if missing
        $queryLegacy = DB::table('bukti_dukung');
        if ($satkerFilter) {
            $queryLegacy->where('id_satker', $satkerFilter);
        }
        $totalLegacy = $queryLegacy->count();
        $this->info("Verifying {$totalLegacy} records in legacy bukti_dukung...");
        $barLegacy = $this->output->createProgressBar($totalLegacy);
        $barLegacy->start();

        $syncedLegacy = 0;
        $queryLegacy->orderBy('id')->chunk($chunkSize, function ($rows) use ($barLegacy, &$syncedLegacy) {
            foreach ($rows as $row) {
                $cleanKode = LkeEvidenceHelper::cleanKriteriaKode($row->id_kriteria);
                $filename = basename($row->link_bukti_dukung);
                $bId = LkeEvidenceHelper::resolveBuktiId($filename, $cleanKode);

                if ($bId !== null && empty($row->kode_bukti)) {
                    DB::table('bukti_dukung')
                        ->where('id', $row->id)
                        ->update(['kode_bukti' => (string)$bId]);
                }

                // Check if already in lke_satker_bukti
                $exists = DB::table('lke_satker_bukti')
                    ->where('id_satker', $row->id_satker)
                    ->where('kriteria_id', $cleanKode)
                    ->where(function ($q) use ($bId, $filename) {
                        if ($bId) {
                            $q->where('buktidukung_id', $bId);
                        } else {
                            $q->where('link_bukti_dukung', $filename);
                        }
                    })
                    ->exists();

                if (!$exists && !empty($filename)) {
                    $tahun = null;
                    if (preg_match('/(202[0-9])/', $filename, $m)) {
                        $tahun = (int)$m[1];
                    } else {
                        $tahun = 2025;
                    }

                    DB::table('lke_satker_bukti')->insert([
                        'id_satker'          => $row->id_satker,
                        'komponen_id'        => $row->id_komponen,
                        'sub_komponen_id'    => $row->id_sub_komponen,
                        'kriteria_id'        => $cleanKode,
                        'kode_bukti'         => $bId ? (string)$bId : null,
                        'buktidukung_id'     => $bId,
                        'link_bukti_dukung'  => $filename,
                        'tgl_pengisian'      => $row->tgl_pengisian ?? now()->format('Y-m-d'),
                        'tahun'              => $tahun,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                    $syncedLegacy++;
                }

                $barLegacy->advance();
            }
        });

        $barLegacy->finish();
        $this->newLine();
        $this->info("Inserted {$syncedLegacy} missing records from bukti_dukung into lke_satker_bukti.");
        $this->info('Evidence synchronization completed successfully!');

        return 0;
    }
}
