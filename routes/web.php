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
Route::post('/login-auto', [LoginController::class, 'login']);

// Route::get('/login', function () {
//     return redirect()->away('https://sicana.kejaksaan.go.id');
// });

//spip routes
use App\Http\Controllers\Spip\SpipAuthController;
use App\Http\Controllers\Spip\SpipDashboardController;

Route::get('/login-spip', [SpipAuthController::class, 'index'])->name('login.spip');
Route::post('/login-spip', [SpipAuthController::class, 'login']);
Route::get('/dashboard-spip', [SpipDashboardController::class, 'index']);
Route::post('/spip/ubah-password',[App\Http\Controllers\Spip\SpipAuthController::class, 'updatePassword']);
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

    // === Evaluasi ===
    Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi');
    Route::post('/upload/lhe-akip', [EvaluasiController::class, 'uploadLheAkip'])->name('upload.lhe_akip');
    Route::post('/upload/tl-lhe-akip', [EvaluasiController::class, 'uploadTlLheAkip'])->name('upload.tl_lhe_akip');
    Route::post('/upload/monev-renaksi', [EvaluasiController::class, 'uploadMonevRenaksi'])->name('upload.monev_renaksi');
    Route::post('/dokumen/verifikasi', [EvaluasiController::class, 'verifikasi'])->name('verifikasi.dokumen');
    Route::post('/dokumen/upload', [EvaluasiController::class, 'upload'])->name('upload.dokumen');

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
    // === Data LKE ===
    Route::get('/evaluasi-akip', [DataLke::class, 'index'])->name('dataLke');
    Route::get('/upload/bukti-dukung', [DataLke::class, 'showUploadForm'])->name('upload_buktidukung');
    Route::post('/upload/bukti-dukung', [DataLke::class, 'upload'])->name('upload.store');
    Route::get('/upload/files/{id}', [DataLke::class, 'getUploadedFiles'])->name('upload.files');
    Route::get('/cekbdeval-lke/{kode}', [DataLke::class, 'cekBuktiDukung'])->name('cekbdeval_lke');

    // === Sistem LKE Baru (Evaluasi Pengawasan & Evidence Mapping) ===
    Route::get('/lke/evaluasi', [LkeEvaluasiController::class, 'index'])->name('lke.evaluasi.index');
    Route::post('/lke/evaluasi/save-score', [LkeEvaluasiController::class, 'saveScore'])->name('lke.evaluasi.save_score');
    Route::post('/lke/evaluasi/save-koefisien', [LkeEvaluasiController::class, 'saveSubkomponenKoefisien'])->name('lke.evaluasi.save_koefisien');
    Route::get('/lke/evidence-mapping', [LkeEvidenceMappingController::class, 'index'])->name('lke.evidence_mapping.index');
    Route::get('/lke/evidence-mapping/detail/{kode}', [LkeEvidenceMappingController::class, 'getCriteriaDetail'])->name('lke.evidence_mapping.detail');
    Route::post('/lke/evidence-mapping/update/{kode}', [LkeEvidenceMappingController::class, 'updateMapping'])->name('lke.evidence_mapping.update');
    Route::get('/lke/master-bukti', [LkeMasterBuktiController::class, 'index'])->name('lke.master_bukti.index');
    Route::post('/lke/master-bukti/update/{id}', [LkeMasterBuktiController::class, 'update'])->name('lke.master_bukti.update');

    // === Pengumuman ===
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman');
    Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
    Route::get('/pengumuman/{id}/edit', [PengumumanController::class, 'edit'])->name('pengumuman.edit');
    Route::put('/pengumuman/{id}', [PengumumanController::class, 'update'])->name('pengumuman.update');
    Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');

    // === Aturan ===
    Route::get('/aturan/create', [AturanController::class, 'create'])->name('aturan.create');
    Route::get('/aturan', [AturanController::class, 'index'])->name('aturan');
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

    // === Fitur Lain ===
    Route::get('/sakipwil', [SakipwilController::class, 'index'])->name('sakipwil');
    Route::get('/sakipvalidasi', [SakipvalidasiController::class, 'index'])->name('sakipvalidasi');
    Route::get('/chatsupport', [ChatsupportController::class, 'index'])->name('chatsupport');
    Route::get('/literasi', [LiterasiController::class, 'index'])->name('literasi');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    Route::get('/ubahpassword', [UbahpasswordController::class, 'index'])->name('ubahpassword');
    Route::put('/password/update', [UbahpasswordController::class, 'updatePassword'])->name('password.update');

    // === Kriteria & Indikator 2025 ===
    Route::get('/input-kriteria', [KriteriaController::class, 'create'])->name('kriteria.create');
    Route::post('/input-kriteria', [KriteriaController::class, 'store'])->name('kriteria.store');
    Route::get('/get-subkomponen/{id}', [KriteriaController::class, 'getSubkomponen']);
    // Route::get('/indikator2025', [Indikator2025Controller::class, 'index'])->name('indikator2025.index');
    // Route::post('/pengukuran2025/store', [Indikator2025Controller::class, 'store'])->name('pengukuran2025.store');
});
