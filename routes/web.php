<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TahunController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KepController;
use App\Http\Controllers\SakipvalidasiController;
use App\Http\Controllers\SakipwilController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\PelaporanController;
use App\Http\Controllers\PerencanaanController;
use App\Http\Controllers\PengukuranController;
use App\Http\Controllers\PengukuranIkpController;
use App\Http\Controllers\KepatuhanController;
use App\Http\Controllers\ChatsupportController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\AturanController;
use App\Http\Controllers\LiterasiController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\KeloladataController;
use App\Http\Controllers\DokumenSakipController;
use App\Http\Controllers\UbahpasswordController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\DataLke;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\Indikator2025Controller;
use App\Http\Controllers\Lke\LkeEvidenceMappingController;
use App\Http\Controllers\Lke\LkeMasterBuktiController;
use App\Http\Controllers\Lke\LkeEvaluasiController;
use App\Http\Controllers\HapusFileController;
use App\Http\Controllers\RestoreFileController;
use App\Http\Controllers\BackupFileController;

// === Helper CPanel Setup & Diagnostic (Protected with Secret Key) ===
Route::get('/cpanel-setup', function (\Illuminate\Http\Request $request) {
    if ($request->query('key') !== 'prosakip2026') {
        abort(403, 'Akses ditolak. Parameter key tidak valid.');
    }

    $action = $request->query('action');
    $actionResult = null;

    if ($action === 'clear-cache') {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $actionResult = 'Cache berhasil dibersihkan: ' . \Illuminate\Support\Facades\Artisan::output();
    } elseif ($action === 'storage-link') {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $actionResult = 'Storage link: ' . \Illuminate\Support\Facades\Artisan::output();
    } elseif ($action === 'migrate') {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $actionResult = 'Migrate: ' . \Illuminate\Support\Facades\Artisan::output();
    }

    $dbStatus = 'OK';
    $dbError = null;
    $tablesCount = 0;
    try {
        $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
        $tablesCount = count($tables);
    } catch (\Throwable $e) {
        $dbStatus = 'ERROR';
        $dbError = $e->getMessage();
    }

    $storageWritable = is_writable(storage_path());
    $cacheWritable = is_writable(base_path('bootstrap/cache'));
    $publicUploadsWritable = is_writable(public_path('uploads'));

    $currentUrl = $request->url();

    return response('<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CPanel Deployment Status - PROSAKIP</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f0f2f5; padding: 30px; color: #1e293b; }
        .card { max-width: 700px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 24px; }
        h1 { font-size: 20px; margin-top: 0; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; }
        .item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .badge { padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .btn { display: inline-block; padding: 8px 16px; margin: 6px 4px 6px 0; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 500; }
        .btn:hover { background: #1d4ed8; }
        .btn-warning { background: #d97706; }
        .btn-warning:hover { background: #b45309; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; line-height: 1.5; }
        .alert-info { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; word-break: break-all; }
        pre { background: #0f172a; color: #f8fafc; padding: 12px; border-radius: 6px; font-size: 12px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="card">
        <h1>PROSAKIP CPanel Diagnostic & Quick Setup</h1>
        ' . ($actionResult ? '<div class="alert alert-info"><strong>Hasil Tindakan:</strong><br><pre>' . htmlspecialchars($actionResult) . '</pre></div>' : '') . '
        ' . ($dbError ? '<div class="alert alert-error"><strong>Database Error:</strong><br>' . htmlspecialchars($dbError) . '</div>' : '') . '
        
        <div class="item">
            <span>Koneksi Database (' . htmlspecialchars(config('database.connections.mysql.database')) . ')</span>
            <span class="badge ' . ($dbStatus === 'OK' ? 'badge-success' : 'badge-danger') . '">' . $dbStatus . ' (' . $tablesCount . ' tabel)</span>
        </div>
        <div class="item">
            <span>Storage Permission (storage/)</span>
            <span class="badge ' . ($storageWritable ? 'badge-success' : 'badge-danger') . '">' . ($storageWritable ? 'Writable' : 'Not Writable') . '</span>
        </div>
        <div class="item">
            <span>Bootstrap Cache Permission (bootstrap/cache/)</span>
            <span class="badge ' . ($cacheWritable ? 'badge-success' : 'badge-danger') . '">' . ($cacheWritable ? 'Writable' : 'Not Writable') . '</span>
        </div>
        <div class="item">
            <span>Uploads Directory (public/uploads/)</span>
            <span class="badge ' . ($publicUploadsWritable ? 'badge-success' : 'badge-danger') . '">' . ($publicUploadsWritable ? 'Writable' : 'Not Writable') . '</span>
        </div>
        <div class="item">
            <span>PHP Version</span>
            <span class="badge badge-success">' . PHP_VERSION . '</span>
        </div>
        <div class="item">
            <span>App Environment / Debug</span>
            <span class="badge badge-success">' . config('app.env') . ' / debug=' . (config('app.debug') ? 'true' : 'false') . '</span>
        </div>
        <div class="item">
            <span>App URL</span>
            <span class="badge badge-success">' . config('app.url') . '</span>
        </div>

        <div style="margin-top: 20px;">
            <p style="font-size: 13px; font-weight: 600; margin-bottom: 8px;">Tindakan Cepat (Tanpa CMD):</p>
            <a href="' . $currentUrl . '?key=prosakip2026&action=clear-cache" class="btn btn-warning">Bersihkan Cache (Optimize Clear)</a>
            <a href="' . $currentUrl . '?key=prosakip2026&action=storage-link" class="btn">Buat Storage Link</a>
            <a href="' . $currentUrl . '?key=prosakip2026&action=migrate" class="btn">Jalankan Migrasi Database</a>
            <a href="' . url('/login-auto') . '" class="btn" style="background: #16a34a;">Buka Halaman Login</a>
        </div>
    </div>
</body>
</html>');
});

Route::get('/spip', function () {
    return view('spip'); // resources/views/spip.blade.php
});

Route::get('/', function () {
    return view('auth/login');
});

//Handle Sicana
// Route::get('/', function () {
//     return redirect()->away('https://sicana.kejaksaan.go.id');
// });
// Route::get('/receive_token', [SicanaController::class, 'receiveToken']);

//Handle Maintenance
// Route::get('/', function () {
//     return view('maintenance');
// });
// Route::get('/receive_token', function () {
//     return view('maintenance');
// });

// Handle login
Route::get('/login-auto', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login-auto', [LoginController::class, 'login'])->middleware('throttle:5,1');

// Route::get('/login', function () {
//     return redirect()->away('https://sicana.kejaksaan.go.id');
// });

//spip routes
use App\Http\Controllers\Spip\SpipAuthController;
use App\Http\Controllers\Spip\SpipDashboardController;

Route::get('/login-spip', [SpipAuthController::class, 'index'])->name('login.spip');
Route::post('/login-spip', [SpipAuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/dashboard-spip', [SpipDashboardController::class, 'index'])->name('spip.dashboard');
Route::post('/spip/ubah-password',[App\Http\Controllers\Spip\SpipAuthController::class, 'updatePassword'])->name('spip.ubah_password');
Route::get('/spip', function () {return view('spip.spip');});

Route::post('/logout', [SpipAuthController::class, 'logout']);

// Handle Logout
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Middleware Auth
Route::middleware(['auth'])->group(function () {

    // === Pemilihan Tahun ===
    Route::get('/pilih-tahun', [TahunController::class, 'showTahunForm'])->name('pilih.tahun');
    Route::post('/pilih-tahun', [TahunController::class, 'pilihTahun'])->name('pilih_tahun');
    Route::post('/pilih2-tahun', [TahunController::class, 'setTahun'])->name('set.tahun');
    Route::post('/set-bulan', [TahunController::class, 'setBulan'])->name('set.bulan');

    // === Dashboard & Menu Utama ===
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // === Rute Khusus Operasional Satker (Dibatasi dari Akun Pengawasan 888882 & wasxxxxxx) ===
    Route::middleware(['restrict.pengawasan'])->group(function () {
        Route::get('/kep', [KepController::class, 'index'])->name('kep');
        Route::post('kep', [KepController::class, 'store'])->name('kep.store');

        // === Perencanaan ===
        Route::get('/perencanaan', [PerencanaanController::class, 'index'])->name('perencanaan');
        Route::get('/perencanaan/indikator', [PerencanaanController::class, 'showIndikator'])->name('perencanaan.indikator');
        Route::post('/perencanaan/store', [PerencanaanController::class, 'store'])->name('perencanaan.store');
        Route::post('/target/store', [PerencanaanController::class, 'storetarget'])->name('target.store');
        Route::get('/perencanaan/target-ikp/data/{id_satker_bidang}', [PerencanaanController::class, 'getTargetIkpData'])->name('perencanaan.targetIkp.getData');
        Route::post('/perencanaan/target-ikp/store', [PerencanaanController::class, 'storeTargetIkp'])->name('perencanaan.targetIkp.store');

        Route::post('/perencanaan/upload-renstra', [PerencanaanController::class, 'uploadRenstra'])->name('upload.renstra');
        Route::post('/perencanaan/upload-iku', [PerencanaanController::class, 'uploadIku'])->name('upload.iku');
        Route::post('/perencanaan/upload-renja', [PerencanaanController::class, 'uploadRenja'])->name('upload.renja');
        Route::post('/perencanaan/upload-rkakl', [PerencanaanController::class, 'uploadRkakl'])->name('upload.rkakl');
        Route::post('/perencanaan/upload-renaksi', [PerencanaanController::class, 'uploadRenaksi'])->name('upload.renaksi');
        Route::post('/upload-pk', [PerencanaanController::class, 'uploadPK'])->name('upload.pk');
        Route::post('/upload-dipa', [PerencanaanController::class, 'uploadDipa'])->name('upload.dipa');

        // === Pengukuran ===
        Route::get('/pengukuran', [PengukuranController::class, 'index'])->name('pengukuran');
        Route::get('/pengukuran/form/{id}', [PengukuranController::class, 'form'])->name('pengukuran.form');
        Route::get('/pengukuran/indikator/{id_bidang}', [PengukuranController::class, 'getIndikatorByBidang'])->name('pengukuran.getIndikatorByBidang');
        Route::get('/pengukuran/indikator-nama', [PengukuranController::class, 'getIndikatorNama'])->name('pengukuran.getIndikatorNama');
        Route::get('/get-indikator/{id_bidang}', [PengukuranController::class, 'getIndikatorByBidang']);
        Route::get('/pengukuran/{id_bidang}/{sub_indikator}', [PengukuranController::class, 'getDataByBidangAndSubIndikator'])->name('pengukuran.getDataByBidangAndSubIndikator');
        Route::get('/get-pengukuran/{indikator_id}', [PengukuranController::class, 'getPengukuran'])->name('pengukuran.getPengukuran');
        Route::post('/simpan-pengukuran', [PengukuranController::class, 'store'])->name('pengukuran.store');
        Route::post('/pengukuran/update-inline', [PengukuranController::class, 'updateInline'])->name('pengukuran.updateInline');
        Route::post('/pengukuran/update-bulanan', [PengukuranController::class, 'updateBulanan'])->name('pengukuran.updateBulanan');
        Route::get('/get-subindikator-by-id/{id}', [PengukuranController::class, 'getIndikatorNama']);
        Route::get('/get-subindikator/{rumpun}', [PelaporanController::class, 'getSubIndikator']);

        // === Pengukuran IKP (Level 1 Kejagung) ===
        Route::get('/pengukuran-ikp', [PengukuranIkpController::class, 'index'])->name('pengukuran-ikp');
        Route::get('/pengukuran-ikp/data/{id_satker_bidang}', [PengukuranIkpController::class, 'getData'])->name('pengukuran-ikp.getData');
        Route::post('/pengukuran-ikp/store', [PengukuranIkpController::class, 'store'])->name('pengukuran-ikp.store');

        // === Pelaporan ===
        Route::get('/pelaporan', [PelaporanController::class, 'index'])->name('pelaporan');
        Route::post('/upload/lkjip', [PelaporanController::class, 'uploadLkjip'])->name('upload.lkjip');
        Route::delete('/delete/lkjip/{id}', [PelaporanController::class, 'deleteLkjip'])->name('delete.lkjip');
        Route::post('/upload/rapat-staff-eka', [PelaporanController::class, 'uploadRapatStaffEka'])->name('upload.rapat_staff_eka');
        Route::get('/pelaporan/subindikator/{rumpun}', [PelaporanController::class, 'getSubIndikator2']);
        Route::post('/pelaporan/simpan-keterangan', [PelaporanController::class, 'simpanKeterangan']);

        // === Pelaporan IKP (Level 1 Capaian Kinerja) ===
        Route::get('/pelaporan/ikp-data/{id_satker_bidang}', [PelaporanController::class, 'getIkpData'])->name('pelaporan.ikpData');
        Route::post('/pelaporan/simpan-ikp-keterangan', [PelaporanController::class, 'simpanIkpKeterangan'])->name('pelaporan.simpanIkpKeterangan');

        // === Monitoring ===
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
        Route::get('/monitoring/search-satker', [MonitoringController::class, 'searchSatker'])->name('monitoring.searchSatker');
        Route::get('/monitoring/bidang/{idSatker}', [MonitoringController::class, 'getBidang'])->name('monitoring.getBidang');
        Route::get('/monitoring/subindikator/{rumpun}/{id_satker}', [MonitoringController::class, 'getSubIndikator'])->name('monitoring.subindikator');
        Route::get('/monitoring/subindikator2/{rumpun}', [MonitoringController::class, 'getSubIndikator2']);
        Route::get('/monitoring/capaian-saspro-all', [MonitoringController::class, 'capaianSasproAll'])->name('capaian.saspro.all');
        Route::get('/monitoring/capaian-saspro-per-kejati', [MonitoringController::class, 'capaianSasproPerKejati'])->name('capaian.saspro.perkejati');
        Route::get('/capaian/saspro/all/{id_satker}/{id_kejati}/{tahun}/{level}', [MonitoringController::class, 'capaianSasproAll'])->name('capaian.saspro.all.params');
        Route::get('/monitoring/export', [MonitoringController::class, 'exportExcel'])->name('monitoring.export');

        // === Data LKE Lama ===
        Route::get('/evaluasi-akip', [DataLke::class, 'index'])->name('dataLke');
        Route::get('/upload/bukti-dukung', [DataLke::class, 'showUploadForm'])->name('upload_buktidukung');
        Route::post('/upload/bukti-dukung', [DataLke::class, 'upload'])->name('upload.store');
        Route::get('/upload/files/{id}', [DataLke::class, 'getUploadedFiles'])->name('upload.files');
        Route::get('/cekbdeval-lke/{kode}', [DataLke::class, 'cekBuktiDukung'])->name('cekbdeval_lke');
    });

    // === Evaluasi & LHE AKIP ===
    Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi');
    Route::post('/upload/lhe-akip', [EvaluasiController::class, 'uploadLheAkip'])->name('upload.lhe_akip');
    Route::post('/upload/tl-lhe-akip', [EvaluasiController::class, 'uploadTlLheAkip'])->name('upload.tl_lhe_akip');
    Route::post('/upload/monev-renaksi', [EvaluasiController::class, 'uploadMonevRenaksi'])->name('upload.monev_renaksi');
    Route::post('/dokumen/verifikasi', [EvaluasiController::class, 'verifikasi'])->name('verifikasi.dokumen');
    Route::post('/dokumen/upload', [EvaluasiController::class, 'upload'])->name('upload.dokumen');
    Route::post('/evaluasi/upload-bukti-direct', [EvaluasiController::class, 'uploadBuktiDirect'])->name('upload.bukti_direct');
    Route::delete('/lhe-akip/{id}', [EvaluasiController::class, 'deleteLheAkip'])->name('delete.lhe_akip');
    Route::get('/lhe-akip/detail-nilai', [EvaluasiController::class, 'getSatkerLheDetail'])->name('lhe.detail_nilai');

    // === Sistem LKE Baru (Evaluasi Pengawasan & Evidence Mapping) ===
    Route::get('/lke/evaluasi', [LkeEvaluasiController::class, 'index'])->name('lke.evaluasi.index');
    Route::post('/lke/evaluasi/save-score', [LkeEvaluasiController::class, 'saveScore'])->name('lke.evaluasi.save_score');
    Route::post('/lke/evaluasi/save-koefisien', [LkeEvaluasiController::class, 'saveSubkomponenKoefisien'])->name('lke.evaluasi.save_koefisien');

    // Evidence Mapping & Master Bukti Dukung
    Route::get('/lke/evidence-mapping', [LkeEvidenceMappingController::class, 'index'])->name('lke.evidence_mapping.index');
    Route::get('/lke/evidence-mapping/{kode}', [LkeEvidenceMappingController::class, 'getCriteriaDetail'])->name('lke.evidence_mapping.detail');
    Route::post('/lke/evidence-mapping/{kode}', [LkeEvidenceMappingController::class, 'updateMapping'])->name('lke.evidence_mapping.update');

    Route::get('/lke/master-bukti', [LkeMasterBuktiController::class, 'index'])->name('lke.master_bukti.index');
    Route::post('/lke/master-bukti', [LkeMasterBuktiController::class, 'store'])->name('lke.master_bukti.store');
    Route::put('/lke/master-bukti/{id}', [LkeMasterBuktiController::class, 'update'])->name('lke.master_bukti.update');
    Route::delete('/lke/master-bukti/{id}', [LkeMasterBuktiController::class, 'destroy'])->name('lke.master_bukti.destroy');

    // === Pengumuman ===
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman');

    // === Aturan ===
    Route::get('/aturan', [AturanController::class, 'index'])->name('aturan');
    // === Rute Khusus Administrator (Kelola Data Master, Dokumen SAKIP, Backup & Restore) ===
    Route::middleware(['admin'])->group(function () {
        // === Kelola Data ===
        Route::get('/keloladata', [KeloladataController::class, 'index'])->name('keloladata');
        Route::post('/keloladata/bidang', [KeloladataController::class, 'bidang'])->name('bidang.store');
        Route::post('/keloladata/saspro', [KeloladataController::class, 'saspro'])->name('saspro.store');
        Route::post('/keloladata/storeOrUpdateBidang', [KeloladataController::class, 'storeOrUpdateBidang'])->name('bidang.storeOrUpdateBidang');
        Route::get('/keloladata/edit/{id}', [KeloladataController::class, 'edit'])->name('bidang.edit');
        Route::delete('/keloladata/destroy/{id}', [KeloladataController::class, 'destroy'])->name('bidang.destroy');
        Route::post('/keloladata/update/{id}', [KeloladataController::class, 'sasproUpdate'])->name('saspro.update');
        Route::delete('/keloladata/delete/{id}', [KeloladataController::class, 'destroySaspro'])->name('saspro.destroy');
        Route::post('/indikator/store', [KeloladataController::class, 'storeIndikator'])->name('indikator.store');
        Route::post('/indikator/delete/{id}', [KeloladataController::class, 'deleteIndikator'])->name('indikator.delete');
        Route::post('/indikator/update/{id}', [KeloladataController::class, 'updateIndikator'])->name('indikator.update');
        
        // === Dokumen SAKIP ===
        Route::get('/keloladata/dokumen-sakip', [DokumenSakipController::class, 'index'])->name('dokumen-sakip.index');
        Route::post('/keloladata/dokumen-sakip', [DokumenSakipController::class, 'store'])->name('dokumen-sakip.store');
        Route::put('/keloladata/dokumen-sakip/{id}', [DokumenSakipController::class, 'update'])->name('dokumen-sakip.update');
        Route::delete('/keloladata/dokumen-sakip/{id}', [DokumenSakipController::class, 'destroy'])->name('dokumen-sakip.destroy');

        // === Hapus File & Data Berdasarkan Tahun (Admin Only) ===
        Route::get('/keloladata/hapus-tahun', [HapusFileController::class, 'index'])->name('hapustahun.index');
        Route::post('/keloladata/hapus-tahun/preview', [HapusFileController::class, 'previewData'])->name('hapustahun.preview');
        Route::post('/keloladata/hapus-tahun/destroy', [HapusFileController::class, 'destroyByTahun'])->name('hapustahun.destroy');
        Route::post('/keloladata/hapus-tahun/get-satker-batches', [HapusFileController::class, 'getSatkerBatches'])->name('hapustahun.batches');
        Route::post('/keloladata/hapus-tahun/process-batch', [HapusFileController::class, 'processBatch'])->name('hapustahun.processBatch');

        // === Restore / Upload Backup File Berdasarkan Tahun (Admin Only) ===
        Route::get('/keloladata/restore-tahun', [RestoreFileController::class, 'index'])->name('restoretahun.index');
        Route::post('/keloladata/restore-tahun/check-missing', [RestoreFileController::class, 'checkMissingFiles'])->name('restoretahun.check');
        Route::post('/keloladata/restore-tahun/upload-zip', [RestoreFileController::class, 'uploadZip'])->name('restoretahun.uploadZip');
        Route::post('/keloladata/restore-tahun/upload-batch', [RestoreFileController::class, 'uploadBatch'])->name('restoretahun.uploadBatch');
        Route::post('/keloladata/restore-tahun/get-sync-batches', [RestoreFileController::class, 'getSyncBatches'])->name('restoretahun.syncBatches');
        Route::post('/keloladata/restore-tahun/process-sync-batch', [RestoreFileController::class, 'processSyncBatch'])->name('restoretahun.processSyncBatch');

        // === Backup File Berdasarkan Kejati & Tahun (Admin Only) ===
        Route::get('/keloladata/backup-tahun', [BackupFileController::class, 'index'])->name('backuptahun.index');
        Route::post('/keloladata/backup-tahun/preview', [BackupFileController::class, 'previewData'])->name('backuptahun.preview');
        Route::post('/keloladata/backup-tahun/get-batches', [BackupFileController::class, 'getBackupBatches'])->name('backuptahun.batches');
        Route::post('/keloladata/backup-tahun/process-batch', [BackupFileController::class, 'processBackupBatch'])->name('backuptahun.processBatch');
        Route::post('/keloladata/backup-tahun/init-zip', [BackupFileController::class, 'initZipBatch'])->name('backuptahun.initZip');
        Route::post('/keloladata/backup-tahun/add-zip-batch', [BackupFileController::class, 'addBatchToZip'])->name('backuptahun.addZipBatch');
        Route::get('/keloladata/backup-tahun/download-zip-ready', [BackupFileController::class, 'downloadZipReady'])->name('backuptahun.downloadZipReady');
        Route::get('/keloladata/backup-tahun/download-zip', [BackupFileController::class, 'downloadZip'])->name('backuptahun.downloadZip');

        // === Pengumuman CUD ===

        // === Aturan CUD ===
        Route::post('/aturan', [AturanController::class, 'store'])->name('aturan.store');
        Route::get('/aturan/{id}/edit', [AturanController::class, 'edit'])->name('aturan.edit');
        Route::put('/aturan/{id}', [AturanController::class, 'update'])->name('aturan.update');
        Route::delete('/aturan/{id}', [AturanController::class, 'destroy'])->name('aturan.destroy');

        // === Kriteria Master ===

        // === LKE Master Bukti & Evidence Mapping ===
    });

    // === Fitur Lain ===
    Route::middleware(['restrict.pengawasan'])->group(function () {
        Route::get('/sakipwil', [SakipwilController::class, 'index'])->name('sakipwil');
        Route::get('/sakipvalidasi', [SakipvalidasiController::class, 'index'])->name('sakipvalidasi');
        Route::get('/chatsupport', [ChatsupportController::class, 'index'])->name('chatsupport');
    });
    Route::get('/literasi', [LiterasiController::class, 'index'])->name('literasi');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    Route::get('/ubahpassword', [UbahpasswordController::class, 'index'])->name('ubahpassword');
    Route::put('/password/update', [UbahpasswordController::class, 'updatePassword'])->name('password.update');

    // === Kriteria & Indikator 2025 ===
    Route::get('/get-subkomponen/{id}', [KriteriaController::class, 'getSubkomponen']);
    // Route::get('/indikator2025', [Indikator2025Controller::class, 'index'])->name('indikator2025.index');
    // Route::post('/pengukuran2025/store', [Indikator2025Controller::class, 'store'])->name('pengukuran2025.store');
});