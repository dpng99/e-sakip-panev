<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

use App\Models\Renstra;
use App\Models\Renja;
use App\Models\Iku;
use App\Models\Rkakl;
use App\Models\Dipa;
use App\Models\Renaksi;
use App\Models\Pk;
use App\Models\Lkjip;
use App\Models\RapatStaffEka;
use App\Models\LheAkip;
use App\Models\TlLheAkip;
use App\Models\MonevRenaksi;
use App\Models\Kep;

use App\Models\sample_skp;
use App\Models\sk_pm;
use App\Models\sk_pk;
use App\Models\absen_pm;
use App\Models\notulensi_pm;
use App\Models\nodis_p_sakip;
use App\Models\nodis_eval_sakip;
use App\Models\memo_datakinerja;
use App\Models\nodis_datakinerja;
use App\Models\reward_punish;
use App\Models\sampel_rekom;
use App\Models\ss_perencanaan;
use App\Models\ss_laporanweb;
use App\Models\ss_laporanapp;
use App\Models\tar_lkjip;
use App\Models\memo_lkjip;
use App\Models\tar_pm;
use App\Models\ba_praeval;
use App\Models\ba_pleno;
use App\Models\lhe_2023;

class RestoreFileController extends Controller
{
    /**
     * Memastikan hanya Admin yang dapat mengakses fitur ini.
     */
    private function checkAdminAccess()
    {
        $level = session('id_sakip_level');
        $id_satker = session('id_satker');

        if ($level != 99 && !in_array($id_satker, [999999, 'admin', 'Pengawasan', 'Panev', 'menpanrb'])) {
            abort(403, 'Akses Ditolak. Fitur restore file tahunan ini hanya tersedia untuk pengguna Admin.');
        }
    }

    /**
     * Tampilkan form / dashboard restore & sync file dari E:\backup ke server.
     */
    public function index(Request $request)
    {
        $this->checkAdminAccess();

        if (!session()->has('tahun_terpilih')) {
            return redirect()->route('pilih.tahun');
        }

        $tahunSelected = $request->get('tahun_restore', session('tahun_terpilih'));
        $cakupan = $request->get('cakupan', 'semua');
        $idKejati = $request->get('id_kejati');
        $idSatkerTarget = $request->get('id_satker_target');
        $sourcePath = $request->get('source_path', 'E:\\backup');

        // Ambil daftar Kejati (id_sakip_level == 2)
        $kejatiList = DB::table('sinori_login')
            ->select('id_kejati', DB::raw("MIN(REPLACE(satkernama, '_', ' ')) as kejati_nama"))
            ->where('id_sakip_level', 2)
            ->whereNotNull('id_kejati')
            ->where('id_kejati', '!=', '')
            ->whereNotIn('id_satker', [888881, 888882, 'admin', 999999, 'Pengawasan', 'Panev', 'menpanrb'])
            ->where('id_satker', 'not like', 'was%')
            ->groupBy('id_kejati')
            ->orderBy('id_kejati', 'asc')
            ->get();

        $satkerFilterList = $this->resolveSatkerList($cakupan, $idKejati, $idSatkerTarget);
        $fileStatus = $this->analyzeFileStatusInDb($tahunSelected, $satkerFilterList, $sourcePath);

        return view('kelola.restore_tahun', [
            'tahun' => $tahunSelected,
            'tahunSelected' => $tahunSelected,
            'cakupan' => $cakupan,
            'idKejati' => $idKejati,
            'idSatkerTarget' => $idSatkerTarget,
            'sourcePath' => $sourcePath,
            'kejatiList' => $kejatiList,
            'fileStatus' => $fileStatus,
        ]);
    }

    /**
     * Preview AJAX file status (Mana yang sudah ada vs belum ada vs tersedia di E:\backup).
     */
    public function checkMissingFiles(Request $request)
    {
        $this->checkAdminAccess();

        $request->validate([
            'tahun_restore' => 'required|numeric',
            'cakupan' => 'required|in:semua,kejati,satker',
        ]);

        $tahun = $request->tahun_restore;
        $cakupan = $request->cakupan;
        $idKejati = $request->id_kejati;
        $idSatkerTarget = $request->id_satker_target;
        $sourcePath = $request->get('source_path', 'E:\\backup');

        $satkerFilterList = $this->resolveSatkerList($cakupan, $idKejati, $idSatkerTarget);
        $fileStatus = $this->analyzeFileStatusInDb($tahun, $satkerFilterList, $sourcePath);

        return response()->json([
            'success' => true,
            'tahun' => $tahun,
            'cakupan' => $cakupan,
            'source_path' => $sourcePath,
            'status' => $fileStatus,
        ]);
    }

    /**
     * API untuk mendapatkan daftar batch Satker untuk pemrosesan Sync dari E:\backup.
     */
    public function getSyncBatches(Request $request)
    {
        $this->checkAdminAccess();

        $request->validate([
            'tahun_restore' => 'required|numeric',
            'cakupan' => 'required|in:semua,kejati,satker',
        ]);

        $tahun = $request->tahun_restore;
        $cakupan = $request->cakupan;
        $idKejati = $request->id_kejati;
        $idSatkerTarget = $request->id_satker_target;
        $sourcePath = rtrim($request->get('source_path', 'E:\\backup'), '\\/');

        $satkerList = $this->resolveSatkerList($cakupan, $idKejati, $idSatkerTarget);

        if ($satkerList === null) {
            $satkerList = DB::table('sinori_login')
                ->whereNotIn('id_satker', [888881, 888882, 'admin', 999999, 'Pengawasan', 'Panev', 'menpanrb'])
                ->pluck('id_satker')
                ->map(fn($v) => (string)$v)
                ->toArray();
        }

        // Pecah list satker menjadi batch (5 satker per batch)
        $batches = array_chunk($satkerList, 5);

        return response()->json([
            'success' => true,
            'tahun' => $tahun,
            'source_path' => $sourcePath,
            'source_exists' => File::exists($sourcePath),
            'total_satkers' => count($satkerList),
            'total_batches' => count($batches),
            'batches' => $batches,
        ]);
    }

    /**
     * API Pemrosesan Sync 1 Batch Satker dari folder local (E:\backup\{id_satker}\).
     */
    public function processSyncBatch(Request $request)
    {
        $this->checkAdminAccess();

        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '1024M');

        $request->validate([
            'tahun_restore' => 'required|numeric',
            'satker_ids' => 'required|array',
        ]);

        $tahun = $request->tahun_restore;
        $satkerIds = $request->satker_ids;
        $sourcePath = rtrim($request->get('source_path', 'E:\\backup'), '\\/');

        if (!File::exists($sourcePath)) {
            return response()->json([
                'success' => false,
                'message' => "Folder sumber backup [{$sourcePath}] tidak ditemukan pada server.",
            ], 400);
        }

        $expectedDbMap = $this->getExpectedDbFileMap($tahun);
        $copiedCount = 0;

        foreach ($satkerIds as $satker) {
            $satkerDir = $sourcePath . DIRECTORY_SEPARATOR . $satker;

            if (!File::exists($satkerDir)) {
                continue;
            }

            $files = File::files($satkerDir);

            foreach ($files as $file) {
                $filename = $file->getFilename();
                $pathname = $file->getPathname();

                // Cek apakah file ini dicatat di database untuk satker & tahun ini
                $info = $expectedDbMap[$filename] ?? null;

                if ($info) {
                    $destDir = $info['is_kep'] 
                        ? public_path('uploads/KEP') 
                        : public_path('uploads/repository/' . $satker);

                    if (!File::exists($destDir)) {
                        File::makeDirectory($destDir, 0757, true, true);
                    }

                    File::copy($pathname, $destDir . DIRECTORY_SEPARATOR . $filename);

                    // Buat salinan di base_path uploads juga jika berbeda
                    $baseDest = $info['is_kep']
                        ? base_path('uploads/KEP')
                        : base_path('uploads/repository/' . $satker);
                    if (!File::exists($baseDest)) {
                        File::makeDirectory($baseDest, 0757, true, true);
                    }
                    File::copy($pathname, $baseDest . DIRECTORY_SEPARATOR . $filename);

                    $copiedCount++;
                } else {
                    // Jika file mengandung pola tahun atau PDF dokumen biasa di folder satker tersebut
                    if (preg_match('/_' . preg_quote($tahun, '/') . '(_|\.|\s)/i', $filename) || str_ends_with(strtolower($filename), '.pdf')) {
                        $destDir = public_path('uploads/repository/' . $satker);
                        if (!File::exists($destDir)) {
                            File::makeDirectory($destDir, 0757, true, true);
                        }
                        File::copy($pathname, $destDir . DIRECTORY_SEPARATOR . $filename);
                        $copiedCount++;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'satker_count' => count($satkerIds),
            'files_copied' => $copiedCount,
        ]);
    }

    /**
     * Upload & Extract File ZIP Backup dari komputer ke server.
     */
    public function uploadZip(Request $request)
    {
        $this->checkAdminAccess();

        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '1024M');
        ignore_user_abort(true);

        $request->validate([
            'tahun_restore' => 'required|numeric',
            'zip_file' => 'required|file|mimes:zip|max:512000', // max 500MB ZIP
        ]);

        $tahun = $request->tahun_restore;
        $file = $request->file('zip_file');

        if (!class_exists('ZipArchive')) {
            return redirect()->back()->with('error', 'Ekstensi PHP ZipArchive tidak aktif pada server.');
        }

        $tempExtractPath = storage_path('app/temp_restore_' . time() . '_' . rand(1000, 9999));
        File::makeDirectory($tempExtractPath, 0777, true, true);

        $zip = new ZipArchive();
        if ($zip->open($file->getRealPath()) === true) {
            // Mitigasi Zip Slip: Validasi nama setiap entri sebelum ekstraksi
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = $zip->getNameIndex($i);
                if (str_contains($entryName, '..') || str_starts_with($entryName, '/') || str_starts_with($entryName, '\\')) {
                    $zip->close();
                    File::deleteDirectory($tempExtractPath);
                    return redirect()->back()->with('error', 'File ZIP tidak valid: terdeteksi path traversal.');
                }
            }
            $zip->extractTo($tempExtractPath);
            $zip->close();
        } else {
            File::deleteDirectory($tempExtractPath);
            return redirect()->back()->with('error', 'Gagal mengekstrak file ZIP backup.');
        }

        $extractedFiles = File::allFiles($tempExtractPath);
        $matchedCount = 0;
        $placedCount = 0;

        $expectedDbMap = $this->getExpectedDbFileMap($tahun);

        foreach ($extractedFiles as $extFile) {
            $filename = $extFile->getFilename();
            $pathName = $extFile->getPathname();

            // Cegah upload script web (hanya proses berkas .pdf)
            if (!str_ends_with(strtolower($filename), '.pdf')) {
                continue;
            }

            if (isset($expectedDbMap[$filename])) {
                $info = $expectedDbMap[$filename];
                $destDir = $info['is_kep'] 
                    ? public_path('uploads/KEP') 
                    : public_path('uploads/repository/' . $info['satker']);

                if (!File::exists($destDir)) {
                    File::makeDirectory($destDir, 0757, true, true);
                }

                File::copy($pathName, $destDir . DIRECTORY_SEPARATOR . $filename);
                $matchedCount++;
                $placedCount++;
                continue;
            }

            $relativePath = str_replace($tempExtractPath . DIRECTORY_SEPARATOR, '', $pathName);
            $parts = explode(DIRECTORY_SEPARATOR, $relativePath);

            if (count($parts) >= 2 && is_numeric($parts[0])) {
                $satker = $parts[0];
                $destDir = public_path('uploads/repository/' . $satker);
                if (!File::exists($destDir)) {
                    File::makeDirectory($destDir, 0757, true, true);
                }
                File::copy($pathName, $destDir . DIRECTORY_SEPARATOR . $filename);
                $placedCount++;
            }
        }

        File::deleteDirectory($tempExtractPath);

        return redirect()->back()->with('success', "Proses Restore ZIP Selesai! Berhasil memproses {$placedCount} file (Secara otomatis mencocokkan {$matchedCount} file dengan database tahun {$tahun}).");
    }

    /**
     * Batch Upload Multi-File PDF via AJAX untuk menghindari 30s timeout.
     */
    public function uploadBatch(Request $request)
    {
        $this->checkAdminAccess();

        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        $request->validate([
            'tahun_restore' => 'required|numeric',
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf|max:20480',
        ]);

        $tahun = $request->tahun_restore;
        $uploadedFiles = $request->file('files');
        $expectedDbMap = $this->getExpectedDbFileMap($tahun);

        $savedCount = 0;

        foreach ($uploadedFiles as $file) {
            // Sanitasi nama file asli dan pastikan berekstensi .pdf
            $rawName = basename($file->getClientOriginalName());
            $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $rawName);
            if (!str_ends_with(strtolower($filename), '.pdf')) {
                $filename .= '.pdf';
            }

            if (isset($expectedDbMap[$filename])) {
                $info = $expectedDbMap[$filename];
                $destDir = $info['is_kep'] 
                    ? public_path('uploads/KEP') 
                    : public_path('uploads/repository/' . $info['satker']);

                if (!File::exists($destDir)) {
                    File::makeDirectory($destDir, 0757, true, true);
                }

                $file->move($destDir, $filename);
                $savedCount++;
            } else {
                $satker = session('id_satker');
                $destDir = public_path('uploads/repository/' . $satker);
                if (!File::exists($destDir)) {
                    File::makeDirectory($destDir, 0757, true, true);
                }
                $file->move($destDir, $filename);
                $savedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'files_uploaded' => count($uploadedFiles),
            'files_restored' => $savedCount,
        ]);
    }

    /**
     * Ambil pemetaan seluruh file yang dicatat di database untuk tahun tersebut.
     */
    private function getExpectedDbFileMap($tahun)
    {
        $map = [];

        $documentModels = [
            ['model' => Renstra::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename', 'is_renstra' => true],
            ['model' => Renja::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => Iku::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => Rkakl::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => Dipa::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => Renaksi::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => Pk::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => Lkjip::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => RapatStaffEka::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => LheAkip::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => TlLheAkip::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => MonevRenaksi::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => Kep::class, 'periode_col' => 'id_tahun', 'file_col' => 'id_filesurat', 'is_kep' => true],
            ['model' => sample_skp::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => sk_pm::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => sk_pk::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => absen_pm::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => notulensi_pm::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => nodis_p_sakip::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => nodis_eval_sakip::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => memo_datakinerja::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => nodis_datakinerja::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => reward_punish::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => sampel_rekom::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => ss_perencanaan::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => ss_laporanweb::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => ss_laporanapp::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => tar_lkjip::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => memo_lkjip::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => tar_pm::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => ba_praeval::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => ba_pleno::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
            ['model' => lhe_2023::class, 'periode_col' => 'id_periode', 'file_col' => 'id_filename'],
        ];

        foreach ($documentModels as $item) {
            $query = ($item['model'])::query();

            if (!empty($item['is_renstra'])) {
                $idPeriode = ($tahun == "2024") ? "P1" : (($tahun >= "2025" && $tahun <= "2029") ? "P2" : $tahun);
                $query->where(function($q) use ($tahun, $idPeriode) {
                    $q->where('id_periode', $tahun)->orWhere('id_periode', $idPeriode);
                });
            } else {
                $query->where($item['periode_col'], $tahun);
            }

            $records = $query->get();

            foreach ($records as $rec) {
                $filename = $rec->{$item['file_col']} ?? null;
                $satker = $rec->id_satker ?? null;

                if ($filename) {
                    $map[$filename] = [
                        'satker' => (string)$satker,
                        'is_kep' => !empty($item['is_kep']),
                    ];
                }
            }
        }

        return $map;
    }

    /**
     * Menganalisis status ketersediaan file fisik di server & E:\backup.
     */
    private function analyzeFileStatusInDb($tahun, $satkerList = null, $sourcePath = 'E:\\backup')
    {
        $map = $this->getExpectedDbFileMap($tahun);
        $sourcePathClean = rtrim($sourcePath, '\\/');
        $sourceExists = File::exists($sourcePathClean);

        $totalExpected = 0;
        $existingCount = 0;
        $missingCount = 0;
        $availableInBackupCount = 0;

        $missingFilesList = [];

        foreach ($map as $filename => $info) {
            if ($satkerList !== null && !in_array($info['satker'], $satkerList)) {
                continue;
            }

            $totalExpected++;
            $foundServer = false;
            $foundBackup = false;

            if ($info['is_kep']) {
                if (File::exists(public_path("uploads/KEP/{$filename}")) || File::exists(base_path("uploads/KEP/{$filename}"))) {
                    $foundServer = true;
                }
            } else {
                $satker = $info['satker'];
                if (File::exists(public_path("uploads/repository/{$satker}/{$filename}")) || File::exists(base_path("uploads/repository/{$satker}/{$filename}"))) {
                    $foundServer = true;
                }
            }

            // Periksa ketersediaan file di E:\backup\{satker}\{filename}
            if ($sourceExists && !empty($info['satker'])) {
                $satkerBackupFile = $sourcePathClean . DIRECTORY_SEPARATOR . $info['satker'] . DIRECTORY_SEPARATOR . $filename;
                if (File::exists($satkerBackupFile)) {
                    $foundBackup = true;
                }
            }

            if ($foundServer) {
                $existingCount++;
            } else {
                $missingCount++;
                if ($foundBackup) {
                    $availableInBackupCount++;
                }
                $missingFilesList[] = [
                    'filename' => $filename,
                    'satker' => $info['satker'],
                    'available_in_backup' => $foundBackup,
                ];
            }
        }

        return [
            'total_expected' => $totalExpected,
            'existing_count' => $existingCount,
            'missing_count' => $missingCount,
            'available_in_backup_count' => $availableInBackupCount,
            'source_exists' => $sourceExists,
            'source_path' => $sourcePathClean,
            'missing_files' => array_slice($missingFilesList, 0, 1000),
        ];
    }

    private function resolveSatkerList($cakupan, $idKejati = null, $idSatkerTarget = null)
    {
        if ($cakupan === 'satker' && $idSatkerTarget) {
            return [(string)$idSatkerTarget];
        }

        if ($cakupan === 'kejati' && $idKejati) {
            return DB::table('sinori_login')
                ->where('id_kejati', $idKejati)
                ->pluck('id_satker')
                ->map(fn($v) => (string)$v)
                ->toArray();
        }

        return null;
    }
}
