<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use App\Models\User;

class PengawasanAccessRestrictionTest extends TestCase
{
    protected string $jamwasEval = '888882';   // Tim Pengawasan JAMWAS Pusat
    protected string $wasKejati  = 'was005083'; // Tim Pengawasan Kejati Jabar
    protected string $kejatiJabar = '005083';  // Kejati Jabar (Target evaluasi JAMWAS)
    protected string $kejariJabar = '005090';  // Kejari Bandung (Target evaluasi Pengawasan Kejati)
    protected string $jamwasPm   = '419346';   // Satker JAMWAS Operasional (Pelaku PM)
    protected int $testTahun     = 2025;

    /**
     * Uji 1: Verifikasi Sidebar untuk Akun 888882 (JAMWAS Evaluator Pusat)
     * Hanya boleh tampil Beranda, Tata Kelola AKIP (Evaluasi & Evaluasi LKE).
     * Dilarang tampil Perencanaan, Pengukuran, Pelaporan, SAKIP Wilayah, Monitoring, Aturan, dan FAQ.
     */
    public function test_sidebar_menu_restriction_for_jamwas_888882(): void
    {
        $user = User::where('id_satker', $this->jamwasEval)->first();

        $response = $this->actingAs($user)->withSession([
            'id_satker'      => $this->jamwasEval,
            'satkernama'     => 'PENGAWASAN',
            'id_sakip_level' => 1,
            'id_kejati'      => 0,
            'tahun_terpilih' => $this->testTahun,
            'tahun_ui'       => $this->testTahun,
        ])->get('/dashboard');

        $response->assertStatus(200);

        // Menu yang WAJIB ada di sidebar
        $response->assertSee('Beranda');
        $response->assertSee('Tata Kelola AKIP');
        $response->assertSee(route('evaluasi'));
        $response->assertSee(route('lke.evaluasi.index'));

        // Menu yang DILARANG ada di sidebar
        $response->assertDontSee(route('perencanaan'));
        $response->assertDontSee(route('pengukuran'));
        $response->assertDontSee(route('pelaporan'));
        $response->assertDontSee(route('sakipwil'));
        $response->assertDontSee(route('monitoring'));
        $response->assertDontSee(route('aturan'));
        $response->assertDontSee(route('faq'));
    }

    /**
     * Uji 2: Verifikasi Sidebar untuk Akun wasxxxxxx (Pengawasan Kejati)
     * Hanya boleh tampil Beranda, Tata Kelola AKIP (Evaluasi & Evaluasi LKE).
     * Dilarang tampil Perencanaan, Pengukuran, Pelaporan, SAKIP Wilayah, Monitoring, Aturan, dan FAQ.
     */
    public function test_sidebar_menu_restriction_for_pengawasan_kejati(): void
    {
        $user = User::where('id_satker', $this->wasKejati)->first();

        $response = $this->actingAs($user)->withSession([
            'id_satker'      => $this->wasKejati,
            'satkernama'     => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level' => 0,
            'id_kejati'      => 10,
            'tahun_terpilih' => $this->testTahun,
            'tahun_ui'       => $this->testTahun,
        ])->get('/dashboard');

        $response->assertStatus(200);

        // Menu yang WAJIB ada di sidebar
        $response->assertSee('Beranda');
        $response->assertSee('Tata Kelola AKIP');
        $response->assertSee(route('evaluasi'));
        $response->assertSee(route('lke.evaluasi.index'));

        // Menu yang DILARANG ada di sidebar
        $response->assertDontSee(route('perencanaan'));
        $response->assertDontSee(route('pengukuran'));
        $response->assertDontSee(route('pelaporan'));
        $response->assertDontSee(route('sakipwil'));
        $response->assertDontSee(route('monitoring'));
        $response->assertDontSee(route('aturan'));
        $response->assertDontSee(route('faq'));
    }

    /**
     * Uji 3: Verifikasi Tampilan Dashboard untuk Akun Pengawasan
     * Tombol CTA hero mengarah ke Evaluasi LKE dan Upload Dokumen LHE AKIP.
     * Tidak menampilkan CTA Perencanaan/Pelaporan serta kartu kepatuhan perencanaan.
     */
    public function test_dashboard_cta_and_content_for_pengawasan_user(): void
    {
        $user = User::where('id_satker', $this->jamwasEval)->first();

        $response = $this->actingAs($user)->withSession([
            'id_satker'      => $this->jamwasEval,
            'satkernama'     => 'PENGAWASAN',
            'id_sakip_level' => 1,
            'id_kejati'      => 0,
            'tahun_terpilih' => $this->testTahun,
            'tahun_ui'       => $this->testTahun,
        ])->get('/dashboard');

        $response->assertStatus(200);

        // Tombol CTA pengawasan
        $response->assertSee('Evaluasi LKE AKIP');
        $response->assertSee('Upload Dokumen LHE AKIP');
        $response->assertSee('Pengawasan / Evaluator');

        // CTA operasional dilarang tampil
        $response->assertDontSee('Mulai Perencanaan');
        $response->assertDontSee('Pelaporan Kinerja');
        $response->assertDontSee('Status Kepatuhan Dokumen');
        $response->assertDontSee('Sumber Aturan');
        $response->assertDontSee('Pusat FAQ');
    }

    /**
     * Uji 4: Verifikasi Middleware RestrictPengawasanAccess memblokir rute operasional
     * Mengarahkan ke /evaluasi dengan flash message.
     */
    public function test_middleware_blocks_operational_routes_for_pengawasan(): void
    {
        $user = User::where('id_satker', $this->wasKejati)->first();

        $restrictedRoutes = [
            '/perencanaan',
            '/pengukuran',
            '/pelaporan',
            '/monitoring',
            '/sakipwil',
        ];

        foreach ($restrictedRoutes as $route) {
            $response = $this->actingAs($user)->withSession([
                'id_satker'      => $this->wasKejati,
                'satkernama'     => 'PENGAWASAN_KEJATI_JAWA_BARAT',
                'id_sakip_level' => 0,
                'id_kejati'      => 10,
                'tahun_terpilih' => $this->testTahun,
            ])->get($route);

            $response->assertRedirect('/evaluasi');
            $response->assertSessionHas('error');
        }
    }

    /**
     * Uji 5: Verifikasi Halaman Evaluasi (/evaluasi) untuk Akun Pengawasan
     * Hanya tab LHE AKIP yang aktif & tampil. Tab Bukti Dukung LKE, TL LHE AKIP,
     * dan Monev Renaksi disembunyikan.
     * Section 2 (Hasil Evaluasi & Dokumen LHE AKIP Satker Sendiri) disembunyikan.
     */
    public function test_evaluasi_page_only_shows_lhe_tab_without_self_card(): void
    {
        $user = User::where('id_satker', $this->jamwasEval)->first();

        $response = $this->actingAs($user)->withSession([
            'id_satker'      => $this->jamwasEval,
            'satkernama'     => 'PENGAWASAN',
            'id_sakip_level' => 1,
            'id_kejati'      => 0,
            'tahun_terpilih' => $this->testTahun,
        ])->get('/evaluasi');

        $response->assertStatus(200);

        // Hanya tab LHE AKIP yang ada
        $response->assertSee('Laporan Hasil Evaluasi (LHE) AKIP');
        $response->assertDontSee('id="bukti-dukung-tab"', false);
        $response->assertDontSee('id="tl-lhe-tab"', false);
        $response->assertDontSee('id="monev-renaksi-tab"', false);

        // Section 2 (kartu satker sendiri) disembunyikan
        $response->assertDontSee('Hasil Evaluasi & Dokumen LHE AKIP Satuan Kerja Sendiri');

        // Form unggah & rekap target satker tampil
        $response->assertSee('Form Unggah Dokumen LHE AKIP');
    }

    /**
     * Uji 6: Verifikasi Blokir Aksi Non-LHE pada EvaluasiController untuk Pengawasan
     * Akun pengawasan ditolak melakukan upload TL LHE, upload Monev Renaksi, upload bukti dukung, atau verifikasi.
     */
    public function test_evaluasi_disallowed_actions_blocked_for_pengawasan(): void
    {
        $user = User::where('id_satker', $this->wasKejati)->first();

        // 1. Upload TL LHE AKIP -> Ditolak
        $fakePdf = UploadedFile::fake()->create('tl.pdf', 500, 'application/pdf');
        $respTl = $this->actingAs($user)->withSession([
            'id_satker'      => $this->wasKejati,
            'satkernama'     => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level' => 0,
            'id_kejati'      => 10,
            'tahun_terpilih' => $this->testTahun,
        ])->post('/upload/tl-lhe-akip', [
            'tl_lhe_akip_file' => $fakePdf,
        ]);
        $respTl->assertSessionHas('error-lhe');

        // 2. Upload Monev Renaksi -> Ditolak
        $fakeExcel = UploadedFile::fake()->create('monev.xlsx', 500);
        $respMonev = $this->actingAs($user)->withSession([
            'id_satker'      => $this->wasKejati,
            'satkernama'     => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level' => 0,
            'id_kejati'      => 10,
            'tahun_terpilih' => $this->testTahun,
        ])->post('/upload/monev-renaksi', [
            'monev_renaksi_file' => $fakeExcel,
        ]);
        $respMonev->assertSessionHas('error-lhe');

        // 3. Upload Bukti Dukung LKE -> 403 Forbidden
        $respUpload = $this->actingAs($user)->withSession([
            'id_satker'      => $this->wasKejati,
            'satkernama'     => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level' => 0,
            'id_kejati'      => 10,
            'tahun_terpilih' => $this->testTahun,
        ])->postJson('/dokumen/upload', [
            'id_satker' => $this->wasKejati,
        ]);
        $respUpload->assertStatus(403);

        // 4. Verifikasi Bukti Dukung -> 403 Forbidden
        $respVerif = $this->actingAs($user)->withSession([
            'id_satker'      => $this->wasKejati,
            'satkernama'     => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level' => 0,
            'id_kejati'      => 10,
            'tahun_terpilih' => $this->testTahun,
        ])->postJson('/dokumen/verifikasi', [
            'id_satker' => $this->wasKejati,
        ]);
        $respVerif->assertStatus(403);
    }

    /**
     * Uji 7: Verifikasi Akun Pengawasan 888882 DITOLAK melakukan Penilaian Mandiri (PM)
     * Request save-score dengan tahap=pm wajib ditolak dengan HTTP 403 Forbidden.
     */
    public function test_user_888882_forbidden_from_submitting_pm_score(): void
    {
        $user = User::where('id_satker', $this->jamwasEval)->first();

        // Coba kirim skor dengan tahap=pm
        $response = $this->actingAs($user)->withSession([
            'id_satker'      => $this->jamwasEval,
            'satkernama'     => 'PENGAWASAN',
            'id_sakip_level' => 1,
            'id_kejati'      => 0,
            'tahun_terpilih' => $this->testTahun,
        ])->postJson('/lke/evaluasi/save-score', [
            'id_satker'    => $this->kejatiJabar,
            'tahun'        => $this->testTahun,
            'kriteria_id'  => '1',
            'parameter_id' => 1,
            'tahap'        => 'pm',
            'skor'         => 1,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Akses Ditolak: User Pengawasan tidak berwenang melakukan Penilaian Mandiri (PM).',
        ]);
    }

    /**
     * Uji 8: Verifikasi Akun wasxxxxxx DITOLAK melakukan Penilaian Mandiri (PM)
     * Request save-score dengan tahap=pm wajib ditolak dengan HTTP 403 Forbidden.
     */
    public function test_user_was005083_forbidden_from_submitting_pm_score(): void
    {
        $user = User::where('id_satker', $this->wasKejati)->first();

        // Coba kirim skor dengan tahap=pm
        $response = $this->actingAs($user)->withSession([
            'id_satker'      => $this->wasKejati,
            'satkernama'     => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level' => 0,
            'id_kejati'      => 10,
            'tahun_terpilih' => $this->testTahun,
        ])->postJson('/lke/evaluasi/save-score', [
            'id_satker'    => $this->kejariJabar,
            'tahun'        => $this->testTahun,
            'kriteria_id'  => '1',
            'parameter_id' => 1,
            'tahap'        => 'pm',
            'skor'         => 1,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Akses Ditolak: User Pengawasan tidak berwenang melakukan Penilaian Mandiri (PM).',
        ]);
    }

    /**
     * Uji 9: Verifikasi Akun Pengawasan DIIZINKAN melakukan Evaluasi LKE (tahap=evaluasi)
     */
    public function test_pengawasan_can_score_in_evaluasi_mode(): void
    {
        $user = User::where('id_satker', $this->wasKejati)->first();

        // Ambil kriteria pertama di database
        $kriteria = DB::table('lke_kriteria')->where('tahun', $this->testTahun)->first();
        if (!$kriteria) {
            $this->markTestSkipped('Data kriteria LKE belum tersedia.');
        }

        $param = DB::table('lke_parameter')->where('kriteria_id', $kriteria->id)->first();
        if (!$param) {
            $this->markTestSkipped('Data parameter LKE belum tersedia.');
        }

        $response = $this->actingAs($user)->withSession([
            'id_satker'      => $this->wasKejati,
            'satkernama'     => 'PENGAWASAN_KEJATI_JAWA_BARAT',
            'id_sakip_level' => 0,
            'id_kejati'      => 10,
            'tahun_terpilih' => $this->testTahun,
        ])->postJson('/lke/evaluasi/save-score', [
            'id_satker'    => $this->kejariJabar,
            'tahun'        => $this->testTahun,
            'kriteria_id'  => (string) $kriteria->id,
            'parameter_id' => $param->id,
            'tahap'        => 'evaluasi',
            'skor'         => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }
}
