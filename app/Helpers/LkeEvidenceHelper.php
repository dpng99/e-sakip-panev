<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class LkeEvidenceHelper
{
    /**
     * Cache for kriteria candidate bukti IDs.
     */
    protected static ?array $kriteriaMap = null;

    /**
     * Normalize criteria code:
     * e.g. "1.B.1.1" -> "1.B.1", "3.A.1.2" -> "3.A.1", "1.B.1" -> "1.B.1"
     */
    public static function cleanKriteriaKode(?string $kode): string
    {
        if (empty($kode)) {
            return '';
        }
        $trimmed = trim($kode);
        if (preg_match('/^(\d+\.[A-Z]\.\d+)(\.\d+)?$/', $trimmed, $matches)) {
            return $matches[1];
        }
        return $trimmed;
    }

    /**
     * Get criteria to candidate bukti IDs mapping.
     */
    public static function getKriteriaMap(): array
    {
        if (self::$kriteriaMap === null) {
            self::$kriteriaMap = DB::table('lke_kriteria')
                ->whereNotNull('kode_bukti')
                ->where('kode_bukti', '!=', '')
                ->pluck('kode_bukti', 'kode')
                ->map(function ($val) {
                    return array_map('intval', array_map('trim', explode(',', $val)));
                })
                ->toArray();
        }
        return self::$kriteriaMap;
    }

    /**
     * Check if a filename matches a specific Master Bukti Dukung ID (1 to 44).
     */
    public static function matchesBuktiId(?string $filename, int $bId): bool
    {
        if (empty($filename)) {
            return false;
        }

        $fn = strtolower(basename($filename));

        switch ($bId) {
            case 1: // Renstra Satker
                return str_contains($fn, 'renstra') && !str_contains($fn, 'renstra_lembaga');

            case 2: // Renja Satker
                return str_contains($fn, 'renja');

            case 3: // Renaksi Satker
                return str_contains($fn, 'renaksi') && !str_contains($fn, 'renaksieval');

            case 4: // RKAKL / Kebutuhan Riil
                return str_contains($fn, 'rkakl');

            case 5: // DIPA
                return str_contains($fn, 'dipa');

            case 6: // PK Satker 2025 / Current
                return (str_contains($fn, 'pk_') || str_starts_with($fn, 'pk')) && !str_contains($fn, '2024');

            case 7: // PK Satker 2024
                return (str_contains($fn, 'pk_') || str_starts_with($fn, 'pk')) && str_contains($fn, '2024');

            case 8: // IKU
            case 9: // IKU / Penetapan Target Kinerja
                return str_contains($fn, 'iku');

            case 10: // LKJiP TW 1
                return str_contains($fn, 'lkjip') && (str_contains($fn, 'tw 1') || str_contains($fn, 'tw1') || str_contains($fn, 'tw_1') || str_ends_with($fn, '_1.pdf'));

            case 11: // LKJiP TW 2
                return str_contains($fn, 'lkjip') && (str_contains($fn, 'tw 2') || str_contains($fn, 'tw2') || str_contains($fn, 'tw_2') || str_ends_with($fn, '_2.pdf'));

            case 12: // LKJiP TW 4 / 2024
                return str_contains($fn, 'lkjip') && (str_contains($fn, '2024') || str_contains($fn, 'tw 4') || str_contains($fn, 'tw4') || str_contains($fn, 'tw_4'));

            case 13: // Rapat Staf EKA TW 1
                return str_contains($fn, 'rastaff') && (str_contains($fn, 'tw 1') || str_contains($fn, 'tw1') || str_contains($fn, 'tw_1'));

            case 14: // Rapat Staf EKA TW 2
                return str_contains($fn, 'rastaff') && (str_contains($fn, 'tw 2') || str_contains($fn, 'tw2') || str_contains($fn, 'tw_2'));

            case 15: // LHE AKIP 2024
                return str_contains($fn, 'lhe') && str_contains($fn, '2024') && !str_contains($fn, 'tl_') && !str_contains($fn, 'tllhe');

            case 16: // LHE AKIP 2025
                return str_contains($fn, 'lhe') && str_contains($fn, '2025') && !str_contains($fn, 'tl_') && !str_contains($fn, 'tllhe');

            case 17: // TL LHE AKIP 2024
                return str_contains($fn, 'tl_lhe') || str_contains($fn, 'tllhe');

            case 18: // Monev Renaksi TW 1
                return (str_contains($fn, 'monev') || str_contains($fn, 'renaksieval')) && (str_contains($fn, 'tw 1') || str_contains($fn, 'tw1') || str_contains($fn, 'tw_1'));

            case 19: // Monev Renaksi TW 2
                return (str_contains($fn, 'monev') || str_contains($fn, 'renaksieval')) && (str_contains($fn, 'tw 2') || str_contains($fn, 'tw2') || str_contains($fn, 'tw_2'));

            case 20: // Pohon Kinerja
                return str_contains($fn, 'pokin') || str_contains($fn, 'pohon_kinerja');

            case 21: // Renstra Lembaga / Kejaksaan RI
                return str_contains($fn, 'renstra_lembaga');

            case 22: // LKJiP 2023
                return str_contains($fn, 'lkjip') && str_contains($fn, '2023');

            case 23: // Sampel SKP
                return str_contains($fn, 'sampel_skp') || str_contains($fn, 'sample_skp') || str_contains($fn, 'skp');

            case 24: // SK Tim PM
                return str_contains($fn, 'tim_pm') || str_contains($fn, 'sk_pm');

            case 25: // SK Tim Evaluator
                return str_contains($fn, 'tim_evaluator') || str_contains($fn, 'sk_pk') || str_contains($fn, 'sk_evaluator');

            case 26: // Daftar Hadir Tim PM / Evaluator
                return str_contains($fn, 'absen_pm') || str_contains($fn, 'daftar_hadir');

            case 27: // Notulensi Bimtek
                return str_contains($fn, 'notulensi');

            case 28: // Pedoman 4 / Nota Dinas Sosialisasi 2024
                return str_contains($fn, 'nodis_penyelenggaraan') || str_contains($fn, 'nodis_p_akip') || str_contains($fn, 'surat_atau_nota_dinas');

            case 29: // Pedoman 4 / Nota Dinas Evaluasi 2025
                return str_contains($fn, 'nodis_evaluasi') || str_contains($fn, 'nodis_eval_akip');

            case 30: // Memorandum Kepala Satker Data Kinerja
                return str_contains($fn, 'memo_data') || str_contains($fn, 'memorandum_kepala');

            case 31: // Nota Dinas Data Kinerja Antar Bidang
                return str_contains($fn, 'nodis_data') || str_contains($fn, 'nota_dinas_penyampaian_data');

            case 32: // Reward & Punishment
                return str_contains($fn, 'reward') || str_contains($fn, 'punishment') || str_contains($fn, 'penghargaan');

            case 33: // Sampel Usulan Promosi / Mutasi / SKP Clearance
                return str_contains($fn, 'sampel_rekom') || str_contains($fn, 'rekomendasi_kenaikan_pangkat');

            case 34: // SS Publikasi Perencanaan
                return str_contains($fn, 'ss_perencanaan') || str_contains($fn, 'tangkapan_layar');

            case 35: // SS Publikasi Website
                return str_contains($fn, 'ss_laporan_web') || str_contains($fn, 'ss_laporweb');

            case 36: // SS Publikasi ESR / SICANA
                return str_contains($fn, 'ss_laporan_app') || str_contains($fn, 'ss_laporapp') || str_contains($fn, 'sicana');

            case 37: // Pengantar LKJiP TW 1
                return str_contains($fn, 'tar_lkjip') && (str_contains($fn, 'tw 1') || str_contains($fn, 'tw1') || str_contains($fn, 'tw_1'));

            case 38: // Pengantar LKJiP TW 2
                return str_contains($fn, 'tar_lkjip') && (str_contains($fn, 'tw 2') || str_contains($fn, 'tw2') || str_contains($fn, 'tw_2'));

            case 39: // Memo LKJiP TW 1
                return str_contains($fn, 'memo_lkjip') && (str_contains($fn, 'tw 1') || str_contains($fn, 'tw1') || str_contains($fn, 'tw_1'));

            case 40: // Memo LKJiP TW 2
                return str_contains($fn, 'memo_lkjip') && (str_contains($fn, 'tw 2') || str_contains($fn, 'tw2') || str_contains($fn, 'tw_2'));

            case 41: // Surat Penyampaian PM
                return str_contains($fn, 'tar_pm');

            case 42: // Berita Acara Pra-Evaluasi
                return str_contains($fn, 'ba_praeval');

            case 43: // Berita Acara Rapat Pleno
                return str_contains($fn, 'ba_pleno');

            case 44: // LHE AKIP 2023
                return str_contains($fn, 'lhe') && str_contains($fn, '2023') && !str_contains($fn, 'tl_') && !str_contains($fn, 'tllhe');

            default:
                return false;
        }
    }

    /**
     * Resolve the Master Bukti Dukung ID (1 to 44) for a given filename and criteria code.
     */
    public static function resolveBuktiId(?string $filename, ?string $kriteriaKode = null): ?int
    {
        if (empty($filename)) {
            return null;
        }

        $cleanKode = self::cleanKriteriaKode($kriteriaKode);
        $kMap = self::getKriteriaMap();
        $candidateIds = $kMap[$cleanKode] ?? [];

        // 1. If candidate IDs exist for this criteria, test them in order
        if (!empty($candidateIds)) {
            foreach ($candidateIds as $cid) {
                if (self::matchesBuktiId($filename, $cid)) {
                    return $cid;
                }
            }

            // If only 1 candidate ID is expected for this criteria and any file was provided
            if (count($candidateIds) === 1) {
                return $candidateIds[0];
            }
        }

        // 2. Fallback: test across all standard 44 IDs
        for ($i = 1; $i <= 44; $i++) {
            if (self::matchesBuktiId($filename, $i)) {
                return $i;
            }
        }

        return null;
    }

    /**
     * Get unified, synchronized evidences for a given satker.
     * Combines lke_satker_bukti and legacy bukti_dukung.
     * Returns a Collection grouped by clean kriteria_id.
     */
    public static function getUnifiedSatkerEvidences(string $idSatker, ?int $tahun = null): Collection
    {
        $idSatkerClean = (string)$idSatker;
        $padded = is_numeric($idSatkerClean) ? str_pad($idSatkerClean, 6, '0', STR_PAD_LEFT) : $idSatkerClean;

        // 1. Fetch from lke_satker_bukti
        $lkeQuery = DB::table('lke_satker_bukti')
            ->where(function ($q) use ($idSatkerClean, $padded) {
                $q->where('id_satker', $idSatkerClean)
                  ->orWhere('id_satker', $padded);
            });
        if ($tahun) {
            $lkeQuery->where(function ($q) use ($tahun) {
                $q->where('tahun', $tahun)->orWhereNull('tahun');
            });
        }
        $lkeRows = $lkeQuery->orderBy('id', 'desc')->get();

        // 2. Fetch from bukti_dukung
        $legacyRows = DB::table('bukti_dukung')
            ->where(function ($q) use ($idSatkerClean, $padded) {
                $q->where('id_satker', $idSatkerClean)
                  ->orWhere('id_satker', $padded);
            })
            ->orderBy('id', 'desc')
            ->get();

        $combined = collect();
        $seenKeys = [];

        // Helper to process row
        $processRow = function ($row, $source) use (&$combined, &$seenKeys, $idSatkerClean) {
            $rawKriteria = $row->kriteria_id ?? $row->id_kriteria ?? '';
            $cleanKriteria = self::cleanKriteriaKode($rawKriteria);
            $filename = basename($row->link_bukti_dukung ?? '');
            if (empty($filename)) {
                return;
            }

            // Bukti ID resolution
            $bId = null;
            if (!empty($row->buktidukung_id) && is_numeric($row->buktidukung_id)) {
                $bId = (int)$row->buktidukung_id;
            } elseif (!empty($row->kode_bukti) && is_numeric($row->kode_bukti)) {
                $bId = (int)$row->kode_bukti;
            } else {
                $bId = self::resolveBuktiId($filename, $cleanKriteria);
            }

            // Deduplication key
            $key = $cleanKriteria . '|' . ($bId ?? 'any') . '|' . $filename;
            if (isset($seenKeys[$key])) {
                return;
            }
            $seenKeys[$key] = true;

            $item = (object)[
                'id'                => $row->id,
                'source'            => $source,
                'id_satker'         => $idSatkerClean,
                'kriteria_id'       => $cleanKriteria,
                'raw_kriteria_id'   => $rawKriteria,
                'buktidukung_id'    => $bId,
                'kode_bukti'        => $bId ? (string)$bId : null,
                'link_bukti_dukung' => $filename,
                'tgl_pengisian'     => $row->tgl_pengisian ?? null,
                'komponen_id'       => $row->komponen_id ?? $row->id_komponen ?? null,
                'sub_komponen_id'   => $row->sub_komponen_id ?? $row->id_sub_komponen ?? null,
            ];

            $combined->push($item);
        };

        foreach ($lkeRows as $r) {
            $processRow($r, 'lke_satker_bukti');
        }

        foreach ($legacyRows as $r) {
            $processRow($r, 'bukti_dukung');
        }

        return $combined->groupBy('kriteria_id');
    }

    /**
     * Synchronize a single verified or uploaded evidence to BOTH tables.
     */
    public static function syncEvidenceRecord(
        string $idSatker,
        string $idKriteria,
        string $namaFile,
        $idKomponen = null,
        $idSubKomponen = null,
        $tahun = null
    ): int {
        $cleanKode = self::cleanKriteriaKode($idKriteria);
        $bId = self::resolveBuktiId($namaFile, $cleanKode);
        $tahunVal = $tahun ?? session('tahun_terpilih', date('Y'));
        $nowStr = now()->format('d/m/Y h:i A');

        // 1. Update/Insert legacy bukti_dukung
        DB::table('bukti_dukung')->updateOrInsert(
            [
                'id_kriteria'     => $idKriteria,
                'id_satker'       => $idSatker,
                'id_komponen'     => $idKomponen,
                'id_sub_komponen' => $idSubKomponen,
            ],
            [
                'link_bukti_dukung' => $namaFile,
                'tgl_pengisian'     => $nowStr,
                'kode_bukti'        => $bId ? (string)$bId : null,
            ]
        );

        // 2. Update/Insert lke_satker_bukti
        $whereLke = [
            'id_satker'   => $idSatker,
            'kriteria_id' => $cleanKode,
        ];
        if ($bId) {
            $whereLke['buktidukung_id'] = $bId;
        } else {
            $whereLke['link_bukti_dukung'] = $namaFile;
        }

        DB::table('lke_satker_bukti')->updateOrInsert(
            $whereLke,
            [
                'komponen_id'       => $idKomponen,
                'sub_komponen_id'   => $idSubKomponen,
                'kode_bukti'        => $bId ? (string)$bId : null,
                'buktidukung_id'    => $bId,
                'link_bukti_dukung' => $namaFile,
                'tgl_pengisian'     => now()->format('Y-m-d'),
                'tahun'             => (int)$tahunVal,
                'updated_at'        => now(),
            ]
        );

        return $bId ?? 0;
    }
}
