<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use App\Models\User;
use App\Models\LheAkip;

class LheAkipUploadTest extends TestCase
{
    protected string $kejatiSatker = '005083'; // Kejati Jabar, id_kejati = 10, level = 2
    protected string $kejariJabar = '005090';  // Kejari Bandung, id_kejati = 10, level = 3
    protected string $wasKejati = 'was005083'; // Pengawasan Kejati Jabar, id_kejati = 10, level = 0
    protected string $jamwasPm = '419346';     // Satker JAMWAS PM, level = 1, id_kejati = 88
    protected string $jamwasEval = '888882';   // Tim Pengawasan JAMWAS Pusat, level = 1, id_kejati = 0
    protected int $testTahun = 2025;

    protected function cleanTestFiles(): void
    {
        $records = LheAkip::where('id_periode', $this->testTahun)
            ->whereIn('id_satker', [$this->kejariJabar, $this->kejatiSatker, $this->jamwasPm])
            ->get();

        foreach ($records as $r) {
            $pub = public_path("uploads/repository/{$r->id_satker}/{$r->id_filename}");
            if (file_exists($pub)) {
                @unlink($pub);
            }
            $base = base_path("uploads/repository/{$r->id_satker}/{$r->id_filename}");
            if (file_exists($base)) {
                @unlink($base);
            }
            $r->delete();
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanTestFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanTestFiles();
        parent::tearDown();
    }

    /**
     * Uji 1: Kejari membuka halaman Evaluasi -> Tab LHE AKIP tampil dengan mode read-only,
     * menampilkan nilai evaluasi & predikat, dan TIDAK menampilkan form unggah LHE.
     */
    public function test_evaluasi_page_renders_with_lhe_tab_for_kejari(): void
    {
        $user = User::where('id_satker', $this->kejariJabar)->first();

        $response = $this->actingAs($user)->withSession([
            'id_satker'       => $this->kejariJabar,
            'satkernama'      => 'KEJARI_KOTA_BANDUNG',
            'id_sakip_level'  => 3,
            'id_kejati'       => 10,
            'tahun_terpilih'  => $this->testTahun,
            'active_tab'      => 'lhe-akip',
        ])->get('/evaluasi');

        $response->assertStatus(200);
        $response->assertSee('Laporan Hasil Evaluasi (LHE) AKIP');
        $response->assertSee('Penilaian Mandiri (PM)');
        $response->assertSee('Informasi Penerbitan Dokumen LHE AKIP');
        $response->assertDontSee('Form Unggah Dokumen LHE AKIP Kejari');
    }

    /**
     * Uji 2: Kejari mencoba mengirimkan request upload LHE AKIP -> Ditolak (Akses ditolak).
     */
    public function test_kejari_cannot_upload_lhe_akip(): void
    {
        $user = User::where('id_satker', $this->kejariJabar)->first();
        $fakePdf = UploadedFile::fake()->create('lhe_test.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->withSession([
            'id_satker'       => $this->kejariJabar,
            'satkernama'      => 'KEJARI_KOTA_BANDUNG',
            'id_sakip_level'  => 3,
            'id_kejati'       => 10,
            'tahun_terpilih'  => $this->testTahun,
        ])->post('/upload/lhe-akip', [
            'target_satker_id' => $this->kejariJabar,
            'lhe_akip_file'    => $fakePdf,
        ]);

        $response->assertSessionHas('error-lhe');
        $this->assertDatabaseMissing('lhe', [
            'id_satker'   => $this->kejariJabar,
            'id_periode'  => $this->testTahun,
            'id_filename' => 'lhe_test.pdf',
        ]);
    }

    /**
     * Uji 3: User Kejati reguler (005083) bertindak sebagai satker PM binaan -> Ditolak jika mengunggah LHE.
     * Kewenangan unggah LHE Kejari/Cabjari berada pada akun pengawasan kejati (was005083).
     */
    public function test_kejati_reguler_cannot_upload_lhe_akip(): void
    {
        $kejati = User::where('id_satker', $this->kejatiSatker)->first();
        $fakePdf = UploadedFile::fake()->create('lhe_test_kejati_reg.pdf', 500, 'application/pdf');

        $response = $this->actingAs($kejati)->withSession([
            'id_satker'       => $this->kejatiSatker,
            'satkernama'      => 'KEJATI_JAWA_BARAT',
            'id_sakip_level'  => 2,
            'id_kejati'       => 10,
            'tahun_terpilih'  => $this->testTahun,
        ])->post('/upload/lhe-akip', [
            'target_satker_id' => $this->kejariJabar,
            'lhe_akip_file'    => $fakePdf,
        ]);

        $response->assertSessionHas('error-lhe');
    }

    /**
     * Uji 4: User 419346 (JAMWAS PM) HANYA bisa melakukan PM, TIDAK BISA mengunggah dokumen LHE AKIP.
     */
    public function test_user_419346_jamwas_pm_cannot_upload_lhe_akip(): void
    {
        $user = User::where('id_satker', $this->jamwasPm)->first();
        $fakePdf = UploadedFile::fake()->create('lhe_test_419346.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->withSession([
            'id_satker'       => $this->jamwasPm,
            'satkernama'      => 'JAM_BIDANG_PENGAWASAN',
            'id_sakip_level'  => 1,
            'id_kejati'       => 88,
            'tahun_terpilih'  => $this->testTahun,
        ])->post('/upload/lhe-akip', [
            'target_satker_id' => $this->kejatiSatker,
            'lhe_akip_file'    => $fakePdf,
        ]);

        $response->assertSessionHas('error-lhe');
    }

    /**
     * Uji 5: User 419346 di modul LKE hanya melihat dirinya sendiri (PM) dan ditolak jika mengevaluasi satker lain.
     */
    public function test_user_419346_jamwas_pm_restricted_to_self_in_lke(): void
    {
        $user = User::where('id_satker', $this->jamwasPm)->first();

        // 1. Index LKE: Satker list hanya berisi dirinya sendiri (419346)
        $response = $this->actingAs($user)->withSession([
            'id_satker'       => $this->jamwasPm,
            'satkernama'      => 'JAM_BIDANG_PENGAWASAN',
            'id_sakip_level'  => 1,
            'id_kejati'       => 88,
            'tahun_terpilih'  => $this->testTahun,
        ])->get('/lke/evaluasi');

        $response->assertStatus(200);
        $response->assertViewHas('satkerList', function ($list) {
            return $list->count() === 1 && $list->first()->id_satker === '419346';
        });

        // 2. Request simpan evaluasi untuk satker lain (005083) -> Ditolak 403
        $saveResponse = $this->actingAs($user)->withSession([
            'id_satker'       => $this->jamwasPm,
            'satkernama'      => 'JAM_BIDANG_PENGAWASAN',
            'id_sakip_level'  => 1,
            'id_kejati'       => 88,
            'tahun_terpilih'  => $this->testTahun,
        ])->postJson('/lke/evaluasi/save-score', [
            'id_satker'    => $this->kejatiSatker,
            'tahun'        => $this->testTahun,
            'kriteria_id'  => '1',
            'parameter_id' => 1,
            'tahap'        => 'evaluasi',
        ]);

        $saveResponse->assertStatus(403);
    }

    /**
     * Uji 6: User wasxxxxxx (Pengawasan Kejati Jabar, was005083) berhasil mengunggah dan menghapus LHE Kejari di wilayahnya.
     */
    public function test_user_was005083_can_upload_and_delete_lhe_for_kejari_in_jurisdiction(): void
    {
        $wasUser = User::where('id_satker', $this->wasKejati)->first();
        $fakePdf = UploadedFile::fake()->create('lhe_test_was_jabar.pdf', 500, 'application/pdf');

        $response = $this->actingAs($wasUser)->withSession([
            'id_satker'       => $this->wasKejati,
            'satkernama'      => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level'  => 0,
            'id_kejati'       => 10,
            'tahun_terpilih'  => $this->testTahun,
        ])->post('/upload/lhe-akip', [
            'target_satker_id' => $this->kejariJabar,
            'lhe_akip_file'    => $fakePdf,
        ]);

        $response->assertSessionHas('success-lhe');

        // Pastikan tersimpan
        $createdLhe = LheAkip::where('id_satker', $this->kejariJabar)
            ->where('id_periode', $this->testTahun)
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($createdLhe);

        // Hapus berkas
        $delResponse = $this->actingAs($wasUser)->withSession([
            'id_satker'       => $this->wasKejati,
            'satkernama'      => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level'  => 0,
            'id_kejati'       => 10,
            'tahun_terpilih'  => $this->testTahun,
        ])->delete('/lhe-akip/' . $createdLhe->id);

        $delResponse->assertSessionHas('success-lhe');
        $this->assertDatabaseMissing('lhe', [
            'id' => $createdLhe->id,
        ]);
    }

    /**
     * Uji 7: User wasxxxxxx mencoba mengunggah LHE untuk Kejari di luar wilayah hukumnya -> Ditolak.
     */
    public function test_user_was005083_cannot_upload_lhe_for_kejari_in_different_jurisdiction(): void
    {
        $wasUser = User::where('id_satker', $this->wasKejati)->first();

        $targetDki = DB::table('sinori_login')->where('id_kejati', 9)->where('id_sakip_level', 3)->first();
        $targetId = $targetDki ? $targetDki->id_satker : '005021';

        $fakePdf = UploadedFile::fake()->create('lhe_test_was_foreign.pdf', 500, 'application/pdf');

        $response = $this->actingAs($wasUser)->withSession([
            'id_satker'       => $this->wasKejati,
            'satkernama'      => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level'  => 0,
            'id_kejati'       => 10,
            'tahun_terpilih'  => $this->testTahun,
        ])->post('/upload/lhe-akip', [
            'target_satker_id' => $targetId,
            'lhe_akip_file'    => $fakePdf,
        ]);

        $response->assertSessionHas('error-lhe');
    }

    /**
     * Uji 8: User wasxxxxxx TIDAK BISA mengunggah LHE untuk Kejati atau Eselon I.
     */
    public function test_user_was005083_cannot_upload_lhe_for_kejati_or_eselon_1(): void
    {
        $wasUser = User::where('id_satker', $this->wasKejati)->first();
        $fakePdf = UploadedFile::fake()->create('lhe_test_was_kejati.pdf', 500, 'application/pdf');

        // Coba upload untuk Kejati 005083
        $response1 = $this->actingAs($wasUser)->withSession([
            'id_satker'       => $this->wasKejati,
            'satkernama'      => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level'  => 0,
            'id_kejati'       => 10,
            'tahun_terpilih'  => $this->testTahun,
        ])->post('/upload/lhe-akip', [
            'target_satker_id' => $this->kejatiSatker,
            'lhe_akip_file'    => $fakePdf,
        ]);
        $response1->assertSessionHas('error-lhe');

        // Coba upload untuk Eselon 1 (419346)
        $response2 = $this->actingAs($wasUser)->withSession([
            'id_satker'       => $this->wasKejati,
            'satkernama'      => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level'  => 0,
            'id_kejati'       => 10,
            'tahun_terpilih'  => $this->testTahun,
        ])->post('/upload/lhe-akip', [
            'target_satker_id' => $this->jamwasPm,
            'lhe_akip_file'    => $fakePdf,
        ]);
        $response2->assertSessionHas('error-lhe');
    }

    /**
     * Uji 9: User 888882 (JAMWAS Evaluator) berhasil mengunggah LHE untuk Kejati dan Para Eselon 1 (termasuk 419346).
     */
    public function test_user_888882_can_upload_and_delete_lhe_for_kejati_and_eselon_1(): void
    {
        $jamwasUser = User::where('id_satker', $this->jamwasEval)->first();
        $fakePdf = UploadedFile::fake()->create('lhe_test_888882_kejati.pdf', 500, 'application/pdf');

        // 1. Upload LHE untuk Kejati 005083
        $response1 = $this->actingAs($jamwasUser)->withSession([
            'id_satker'       => $this->jamwasEval,
            'satkernama'      => 'PENGAWASAN',
            'id_sakip_level'  => 1,
            'id_kejati'       => 0,
            'tahun_terpilih'  => $this->testTahun,
        ])->post('/upload/lhe-akip', [
            'target_satker_id' => $this->kejatiSatker,
            'lhe_akip_file'    => $fakePdf,
        ]);
        $response1->assertSessionHas('success-lhe');

        $createdKejatiLhe = LheAkip::where('id_satker', $this->kejatiSatker)
            ->where('id_periode', $this->testTahun)
            ->orderByDesc('id')
            ->first();
        $this->assertNotNull($createdKejatiLhe);

        // Hapus via endpoint resmi
        $this->actingAs($jamwasUser)->withSession([
            'id_satker'       => $this->jamwasEval,
            'satkernama'      => 'PENGAWASAN',
            'id_sakip_level'  => 1,
            'id_kejati'       => 0,
            'tahun_terpilih'  => $this->testTahun,
        ])->delete('/lhe-akip/' . $createdKejatiLhe->id);

        // 2. Upload LHE untuk Unit Eselon 1 (419346)
        $fakePdf2 = UploadedFile::fake()->create('lhe_test_888882_eselon.pdf', 500, 'application/pdf');
        $response2 = $this->actingAs($jamwasUser)->withSession([
            'id_satker'       => $this->jamwasEval,
            'satkernama'      => 'PENGAWASAN',
            'id_sakip_level'  => 1,
            'id_kejati'       => 0,
            'tahun_terpilih'  => $this->testTahun,
        ])->post('/upload/lhe-akip', [
            'target_satker_id' => $this->jamwasPm,
            'lhe_akip_file'    => $fakePdf2,
        ]);
        $response2->assertSessionHas('success-lhe');

        $createdEselonLhe = LheAkip::where('id_satker', $this->jamwasPm)
            ->where('id_periode', $this->testTahun)
            ->orderByDesc('id')
            ->first();
        $this->assertNotNull($createdEselonLhe);

        // Hapus via endpoint resmi
        $this->actingAs($jamwasUser)->withSession([
            'id_satker'       => $this->jamwasEval,
            'satkernama'      => 'PENGAWASAN',
            'id_sakip_level'  => 1,
            'id_kejati'       => 0,
            'tahun_terpilih'  => $this->testTahun,
        ])->delete('/lhe-akip/' . $createdEselonLhe->id);
    }

    /**
     * Uji 10: User 888882 (JAMWAS Evaluator) DITOLAK jika mencoba mengunggah LHE untuk Kejari/Cabjari.
     */
    public function test_user_888882_cannot_upload_lhe_for_kejari(): void
    {
        $jamwasUser = User::where('id_satker', $this->jamwasEval)->first();
        $fakePdf = UploadedFile::fake()->create('lhe_test_888882_kejari.pdf', 500, 'application/pdf');

        $response = $this->actingAs($jamwasUser)->withSession([
            'id_satker'       => $this->jamwasEval,
            'satkernama'      => 'PENGAWASAN',
            'id_sakip_level'  => 1,
            'id_kejati'       => 0,
            'tahun_terpilih'  => $this->testTahun,
        ])->post('/upload/lhe-akip', [
            'target_satker_id' => $this->kejariJabar,
            'lhe_akip_file'    => $fakePdf,
        ]);

        $response->assertSessionHas('error-lhe');
    }

    /**
     * Uji 11: User 888882 (JAMWAS Evaluator) di modul LKE dapat mengevaluasi Kejati dan Para Eselon 1 (termasuk 419346).
     */
    public function test_user_888882_sees_kejati_and_eselon1_in_lke(): void
    {
        $jamwasUser = User::where('id_satker', $this->jamwasEval)->first();

        $response = $this->actingAs($jamwasUser)->withSession([
            'id_satker'       => $this->jamwasEval,
            'satkernama'      => 'PENGAWASAN',
            'id_sakip_level'  => 1,
            'id_kejati'       => 0,
            'tahun_terpilih'  => $this->testTahun,
        ])->get('/lke/evaluasi');

        $response->assertStatus(200);
        $response->assertViewHas('satkerList', function ($list) {
            $ids = $list->pluck('id_satker')->map(fn($id) => (string)$id)->toArray();
            return in_array('005083', $ids) && in_array('419346', $ids);
        });
    }

    /**
     * Uji 12: Endpoint detail nilai AJAX mengembalikan data skor komponen dan subkomponen.
     */
    public function test_get_satker_lhe_detail_endpoint(): void
    {
        $wasUser = User::where('id_satker', $this->wasKejati)->first();

        $response = $this->actingAs($wasUser)->withSession([
            'id_satker'       => $this->wasKejati,
            'satkernama'      => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level'  => 0,
            'id_kejati'       => 10,
            'tahun_terpilih'  => $this->testTahun,
        ])->get('/lhe-akip/detail-nilai?id_satker=' . $this->kejariJabar);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'satker' => ['id_satker', 'satkernama', 'level'],
            'scores' => ['grand_total', 'predikat_evaluasi', 'komponen', 'subkomponen'],
            'lhe_files',
            'tahun',
        ]);
    }
}
