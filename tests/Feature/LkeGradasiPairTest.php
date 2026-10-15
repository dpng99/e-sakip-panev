<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Lke\LkeEvaluasiController;
use App\Models\LkeKriteria;
use App\Models\LkePenilaianSatker;
use App\Models\LkeSubkomponenKoefisien;

class LkeGradasiPairTest extends TestCase
{
    protected string $testSatker = '999TEST';
    protected int $testTahun = 2025;

    protected function setUp(): void
    {
        parent::setUp();

        // Bersihkan data tes sebelumnya
        LkePenilaianSatker::where('id_satker', $this->testSatker)->delete();
        LkeSubkomponenKoefisien::where('id_satker', $this->testSatker)->delete();
    }

    protected function tearDown(): void
    {
        LkePenilaianSatker::where('id_satker', $this->testSatker)->delete();
        LkeSubkomponenKoefisien::where('id_satker', $this->testSatker)->delete();

        parent::tearDown();
    }

    /**
     * Uji 1: Jika 1.B 100% terpenuhi tetapi 1.C BELUM 100%,
     * gerbang pasangan terkunci dan koefisien 1.B dibatasi maksimal BB (80%).
     */
    public function test_pair_gate_locked_when_one_subkomponen_is_incomplete(): void
    {
        $controller = new LkeEvaluasiController();

        // Ambil semua kriteria 1.B dan penuhi seluruhnya (100%)
        $crits1B = LkeKriteria::where('tahun', $this->testTahun)->where('subkomponen_id', '1.B')->get();
        foreach ($crits1B as $cr) {
            LkePenilaianSatker::create([
                'id_satker'     => $this->testSatker,
                'tahun'         => $this->testTahun,
                'kriteria_id'   => $cr->kode,
                'parameter_id'  => 1,
                'nilai'         => 'Terpenuhi',
                'skor'          => 1.0,
                'evaluator_id'  => 'evaluator',
            ]);
        }

        // Ambil kriteria 1.C dan hanya penuhi 1 saja (belum 100%)
        $crits1C = LkeKriteria::where('tahun', $this->testTahun)->where('subkomponen_id', '1.C')->get();
        if ($crits1C->count() > 1) {
            LkePenilaianSatker::create([
                'id_satker'     => $this->testSatker,
                'tahun'         => $this->testTahun,
                'kriteria_id'   => $crits1C->first()->kode,
                'parameter_id'  => 1,
                'nilai'         => 'Terpenuhi',
                'skor'          => 1.0,
                'evaluator_id'  => 'evaluator',
            ]);
        }

        // Simpan koefisien AA (100) palsu secara manual di database untuk menguji suppress
        LkeSubkomponenKoefisien::create([
            'id_satker'        => $this->testSatker,
            'tahun'            => $this->testTahun,
            'subkomponen_kode' => '1.B',
            'nilai'            => 'AA',
            'target_koefisien' => 100,
            'koefisien'        => 100,
        ]);

        $scores = $controller->calculateHierarchyScores($this->testSatker, $this->testTahun, 'evaluasi');

        // Pastikan gerbang pasangan 1.BC terdeteksi TIDAK lengkap
        $this->assertFalse($scores['pairs']['1']['is_pair_complete_eval']);

        // Pastikan koefisien 1.B di-suppress ke 80% (BB), bukan 100% (AA)
        $this->assertEquals(80, $scores['subkomponen']['1.B']['koefisien_evaluasi']);
        $this->assertEquals('BB', $scores['subkomponen']['1.B']['gradasi_eval']);
    }

    /**
     * Uji 2: Jika KEDUA subkomponen (1.B dan 1.C) 100% terpenuhi,
     * gerbang pasangan terbuka dan pilihan gradasi lanjutan (AA 100%) berhasil diterapkan ke keduanya.
     */
    public function test_pair_gate_opens_and_applies_when_both_subkomponen_are_100_percent(): void
    {
        $controller = new LkeEvaluasiController();

        // Penuhi 100% seluruh kriteria 1.B
        $crits1B = LkeKriteria::where('tahun', $this->testTahun)->where('subkomponen_id', '1.B')->get();
        foreach ($crits1B as $cr) {
            LkePenilaianSatker::create([
                'id_satker'     => $this->testSatker,
                'tahun'         => $this->testTahun,
                'kriteria_id'   => $cr->kode,
                'parameter_id'  => 1,
                'nilai'         => 'Terpenuhi',
                'skor'          => 1.0,
                'evaluator_id'  => 'evaluator',
            ]);
        }

        // Penuhi 100% seluruh kriteria 1.C
        $crits1C = LkeKriteria::where('tahun', $this->testTahun)->where('subkomponen_id', '1.C')->get();
        foreach ($crits1C as $cr) {
            LkePenilaianSatker::create([
                'id_satker'     => $this->testSatker,
                'tahun'         => $this->testTahun,
                'kriteria_id'   => $cr->kode,
                'parameter_id'  => 1,
                'nilai'         => 'Terpenuhi',
                'skor'          => 1.0,
                'evaluator_id'  => 'evaluator',
            ]);
        }

        // Simpan gradasi AA (100%) untuk pasangan 1.BC
        LkeSubkomponenKoefisien::create([
            'id_satker'        => $this->testSatker,
            'tahun'            => $this->testTahun,
            'subkomponen_kode' => '1.C',
            'nilai'            => 'AA',
            'target_koefisien' => 100,
            'koefisien'        => 100,
            'uraian'           => 'Inovasi nasional terintegrasi',
        ]);

        $scores = $controller->calculateHierarchyScores($this->testSatker, $this->testTahun, 'evaluasi');

        // Pastikan gerbang pasangan terbuka
        $this->assertTrue($scores['pairs']['1']['is_pair_complete_eval']);

        // Pastikan KEDUA subkomponen mendapatkan koefisien 100% (AA)
        $this->assertEquals(100, $scores['subkomponen']['1.B']['koefisien_evaluasi']);
        $this->assertEquals('AA', $scores['subkomponen']['1.B']['gradasi_eval']);
        $this->assertEquals(100, $scores['subkomponen']['1.C']['koefisien_evaluasi']);
        $this->assertEquals('AA', $scores['subkomponen']['1.C']['gradasi_eval']);

        // Skor 1.B = 100% * 9.0 = 9.00
        $this->assertEquals(9.00, $scores['subkomponen']['1.B']['evaluasi_skor']);
        // Skor 1.C = 100% * 15.0 = 15.00
        $this->assertEquals(15.00, $scores['subkomponen']['1.C']['evaluasi_skor']);
    }

    /**
     * Uji 3: Uji endpoint saveSubkomponenKoefisien
     * - Ditolak 422 jika salah satu belum 100%
     * - Berhasil 200 dan menyimpan ke 1.B dan 1.C jika keduanya 100%
     */
    public function test_endpoint_save_subkomponen_koefisien_pair(): void
    {
        // Setup session sebagai evaluator JAMWAS
        session([
            'id_satker'  => '005016',
            'role'       => 'evaluator',
            'level'      => 'jamwas',
        ]);

        // Simulasikan 1.B 100% dan 1.C 100% untuk satker '005016'
        $satkerId = '005016';
        $crits1B = LkeKriteria::where('tahun', $this->testTahun)->where('subkomponen_id', '1.B')->get();
        foreach ($crits1B as $cr) {
            LkePenilaianSatker::updateOrCreate(
                ['id_satker' => $satkerId, 'tahun' => $this->testTahun, 'kriteria_id' => $cr->kode],
                ['parameter_id' => 1, 'nilai' => 'Terpenuhi', 'skor' => 1.0, 'evaluator_id' => '005016']
            );
        }

        // 1.C dikosongkan dulu
        $crits1C = LkeKriteria::where('tahun', $this->testTahun)->where('subkomponen_id', '1.C')->get();
        LkePenilaianSatker::where('id_satker', $satkerId)
            ->where('tahun', $this->testTahun)
            ->whereIn('kriteria_id', $crits1C->pluck('kode'))
            ->delete();

        $userAdmin = \App\Models\User::where('id_satker', 'admin')->first();

        $sessionAdmin = [
            'id_satker'       => 'admin',
            'id_sakip_level'  => 99,
            'satkernama'      => 'Administrator',
        ];

        // Coba simpan gradasi saat 1.C belum 100% -> Harap tolak (422)
        $resLocked = $this->actingAs($userAdmin)
            ->withSession($sessionAdmin)
            ->postJson(route('lke.evaluasi.save_koefisien'), [
                'id_satker'        => $satkerId,
                'tahun'            => $this->testTahun,
                'subkomponen_kode' => '1.BC',
                'gradasi'          => 'AA',
                'target_koefisien' => 100,
                'uraian'           => 'Uraian inovasi',
                'tahap'            => 'evaluasi',
            ]);

        $resLocked->assertStatus(422);

        // Sekarang penuhi seluruh 1.C
        foreach ($crits1C as $cr) {
            LkePenilaianSatker::updateOrCreate(
                ['id_satker' => $satkerId, 'tahun' => $this->testTahun, 'kriteria_id' => $cr->kode],
                ['parameter_id' => 1, 'nilai' => 'Terpenuhi', 'skor' => 1.0, 'evaluator_id' => '005016']
            );
        }

        // Coba simpan kembali setelah 1.B dan 1.C keduanya 100% -> Harap berhasil (200)
        $resSuccess = $this->actingAs($userAdmin)
            ->withSession($sessionAdmin)
            ->postJson(route('lke.evaluasi.save_koefisien'), [
                'id_satker'        => $satkerId,
                'tahun'            => $this->testTahun,
                'subkomponen_kode' => '1.BC',
                'gradasi'          => 'AA',
                'target_koefisien' => 100,
                'uraian'           => 'Uraian inovasi nasional bersama B dan C',
                'tahap'            => 'evaluasi',
            ]);

        $resSuccess->assertStatus(200);
        $resSuccess->assertJson(['success' => true]);

        // Verifikasi bahwa data tersimpan di kedua record 1.B dan 1.C
        $recB = LkeSubkomponenKoefisien::where('id_satker', $satkerId)->where('tahun', $this->testTahun)->where('subkomponen_kode', '1.B')->first();
        $recC = LkeSubkomponenKoefisien::where('id_satker', $satkerId)->where('tahun', $this->testTahun)->where('subkomponen_kode', '1.C')->first();

        $this->assertNotNull($recB);
        $this->assertNotNull($recC);
        $this->assertEquals('AA', $recB->nilai);
        $this->assertEquals(100, $recB->koefisien);
        $this->assertEquals('AA', $recC->nilai);
        $this->assertEquals(100, $recC->koefisien);
        $this->assertEquals('Uraian inovasi nasional bersama B dan C', $recB->uraian);
        $this->assertEquals('Uraian inovasi nasional bersama B dan C', $recC->uraian);
    }

    /**
     * Uji 4: Memastikan render Blade LKE Evaluasi
     * - Menampilkan Bagian Gradasi untuk 1.A
     * - TIDAK menampilkan Bagian Gradasi mandiri untuk 1.B
     * - Menampilkan Section Gradasi Nilai Gabungan untuk 1.B & 1.C di bawah 1.C
     */
    public function test_blade_renders_unified_section_under_c_and_removes_from_b(): void
    {
        $userAdmin = \App\Models\User::where('id_satker', 'admin')->first();
        $sessionAdmin = [
            'id_satker'       => 'admin',
            'id_sakip_level'  => 99,
            'satkernama'      => 'Administrator',
            'tahun_terpilih'  => 2025,
        ];

        $response = $this->actingAs($userAdmin)
            ->withSession($sessionAdmin)
            ->get(route('lke.evaluasi.index', [
                'id_satker' => '005016',
                'tahun'     => 2025,
            ]));

        $response->assertStatus(200);

        $content = $response->getContent();

        // 1. Memastikan ada gradasi mandiri untuk 1.A (Keberadaan)
        $this->assertStringContainsString('cardKoefisien-1-A', $content);

        // 2. Memastikan TIDAK ada gradasi mandiri untuk 1.B
        $this->assertStringNotContainsString('cardKoefisien-1-B', $content);

        // 3. Memastikan ada Section Gradasi Nilai Gabungan Subkomponen 1.B & 1.C (cardKoefisienPair-1-BC)
        $this->assertStringContainsString('cardKoefisienPair-1-BC', $content);
        $this->assertStringContainsString('Section Gradasi Nilai Gabungan Subkomponen 1.B & 1.C', $content);
    }
}

