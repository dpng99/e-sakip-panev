<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\LheAkip;
use App\Models\MonevRenaksi;
use App\Models\TlLheAkip;
use App\Models\DataLke1;
use App\Models\LkeKomponen;
use App\Models\LkeBuktidukung;
use App\Helpers\LkeEvidenceHelper;
use App\Http\Controllers\Lke\LkeEvaluasiController;

class EvaluasiController extends Controller
{
    /**
     * Daftar ID Satuan Kerja Unit Eselon I / Pusat:
     * Seluruh unit eselon 1 dinilai oleh Evaluator Pusat JAMWAS (user 888882).
     * Termasuk 419346 (satker JAMWAS) yang bertindak sebagai satker PM.
     */
    protected $centralUnitSatkerIds = [
        '005016', // JAM_BIDANG_PEMBINAAN (JAMBIN)
        '419345', // JAM_BIDANG_INTELIJEN (JAMINTEL)
        '417023', // JAM_BIDANG_PERDATA_DAN_TATA_USAHA_NEGARA (JAMDATUN)
        '418326', // JAM_BIDANG_TINDAK_PIDANA_UMUM (JAMPIDUM)
        '419344', // JAM_BIDANG_TINDAK_PIDANA_KHUSUS (JAMPIDSUS)
        '677111', // JAM_BIDANG_PIDANA_MILITER (JAMPIDMIL)
        '691270', // BADAN PEMULIHAN ASET (BPA)
        '666405', // BADAN_PENDIDIKAN_DAN_PELATIHAN (BADIKLAT)
        '419346', // JAM_BIDANG_PENGAWASAN (JAMWAS Satker - PM Only)
    ];

    /**
     * Ambil record user yang sedang login dari sinori_login.
     */
    protected function getCurrentUser()
    {
        $satker = session('id_satker');
        if (!$satker) {
            return null;
        }
        $padded = is_numeric($satker) ? str_pad($satker, 6, '0', STR_PAD_LEFT) : $satker;
        return DB::table('sinori_login')
            ->where('id_satker', $satker)
            ->orWhere('id_satker', $padded)
            ->first();
    }

    /**
     * Tentukan peran evaluator:
     * - 'admin': Hak akses penuh nasional (Level 99 / Admin).
     * - 'jamwas': Tim Pengawasan Pusat (User 888882 - menilai Kejati & Para Eselon 1).
     * - 'pengawasan_kejati': Tim Pengawasan Kejati (User wasxxxxxx - menilai Kejari & Cabjari wilayah hukumnya).
     * - 'satker': Satuan Kerja (User 419346 JAMWAS PM, Kejati reguler, Kejari, Cabjari, Eselon 1) - Read Only pada LHE AKIP.
     */
    protected function getEvaluatorRole()
    {
        $satker = (string) session('id_satker');
        $level = session('id_sakip_level');
        $currentUser = $this->getCurrentUser();
        $nama = strtolower($currentUser ? $currentUser->satkernama : (session('satkernama') ?? ''));

        // 1. Admin
        if (
            in_array($satker, ['admin', '999999', '888881'])
            || $level == '99'
            || $level == 99
            || str_contains($satker, 'admin')
            || $nama === 'admin'
            || $nama === 'administrator'
        ) {
            return 'admin';
        }

        // 2. JAMWAS Evaluator Pusat (user 888882, Panev)
        // User 419346 adalah satker JAMWAS yang HANYA bisa melakukan PM!
        $isJamwasEvaluator = ($satker === '888882')
            || in_array($satker, ['Panev'])
            || ($nama === 'pengawasan' && $satker !== '419346' && !str_starts_with(strtolower($satker), 'was'));

        if ($isJamwasEvaluator) {
            return 'jamwas';
        }

        // 3. User Pengawasan Kejati (wasxxxxxx, misal was005083)
        // Permisionnya khusus melakukan evaluasi hasil PM LHE AKIP Kejari/Cabjari di wilayah hukumnya
        if (str_starts_with(strtolower($satker), 'was')) {
            return 'pengawasan_kejati';
        }

        // 4. Satker binaan (PM Only): 419346, Kejati reguler (level 2), Kejari (level 3), Cabjari (level 4), Eselon 1 lainnya (level 1)
        return 'satker';
    }

    /**
     * Validasi apakah target satker diizinkan untuk diunggah dokumen LHE-nya oleh user yang login.
     */
    protected function isTargetSatkerAllowedForUpload($targetSatkerId)
    {
        $role = $this->getEvaluatorRole();
        $currentUser = $this->getCurrentUser();
        if (!$currentUser || !$targetSatkerId) {
            return false;
        }

        $target = DB::table('sinori_login')->where('id_satker', $targetSatkerId)->first();
        if (!$target) {
            return false;
        }

        // Admin dapat mengunggah untuk satker apapun yang sah (kecuali user admin sendiri)
        if ($role === 'admin') {
            return !in_array($targetSatkerId, ['admin', '999999', '888881']);
        }

        // JAMWAS (888882) berwenang mengunggah LHE untuk seluruh Kejati (Level 2) dan Unit Eselon I (termasuk 419346)
        if ($role === 'jamwas') {
            $targetLevel = (int)$target->id_sakip_level;
            $targetNama = strtolower($target->satkernama ?? '');
            return (
                $targetLevel === 2
                || str_contains($targetNama, 'kejati')
                || in_array((string)$target->id_satker, $this->centralUnitSatkerIds)
            );
        }

        // Pengawasan Kejati (wasxxxxxx) HANYA berwenang mengunggah LHE untuk Kejari (3) dan Cabjari (4) di wilayah hukumnya
        if ($role === 'pengawasan_kejati') {
            $targetLevel = (int)$target->id_sakip_level;
            if (!in_array($targetLevel, [3, 4])) {
                return false;
            }
            return (!empty($currentUser->id_kejati) && (string)$target->id_kejati === (string)$currentUser->id_kejati);
        }

        // Satker (419346, Kejati reguler, Kejari / Cabjari) TIDAK DAPAT mengunggah dokumen LHE AKIP
        return false;
    }

    public function index()
    {
        // Cek apakah tahun sudah dipilih
        if (!session()->has('tahun_terpilih')) {
            return redirect()->route('pilih.tahun');
        }
        // Ambil tahun yang dipilih dari session
        $tahun = session('tahun_terpilih');
        $idSatker = session('id_satker');
        $currentUser = $this->getCurrentUser();
        $evaluatorRole = $this->getEvaluatorRole();

        // Jika user adalah pengawasan (jamwas atau pengawasan_kejati), default dan kunci active_tab ke 'lhe-akip'
        if ($evaluatorRole === 'jamwas' || $evaluatorRole === 'pengawasan_kejati') {
            session(['active_tab' => 'lhe-akip']);
        }

        // Evaluasi Nilai resmi LKE untuk Satker sendiri
        $lkeController = new LkeEvaluasiController();
        $myLheScore = $lkeController->calculateHierarchyScores($idSatker, $tahun, 'evaluasi');

        $lheAkipFiles = LheAkip::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
            ->get();

        // Data satker bawahan / target berdasarkan hak akses evaluator
        $subordinateSatkers = collect();
        $kejatiList = collect();

        if ($evaluatorRole === 'pengawasan_kejati') {
            $kejatiId = $currentUser ? $currentUser->id_kejati : null;
            $subordinateQuery = DB::table('sinori_login')
                ->where('id_kejati', $kejatiId)
                ->whereIn('id_sakip_level', [3, 4])
                ->where('id_satker', 'not like', 'was%')
                ->whereNotIn('id_satker', ['admin', '999999', '888881', '888882', 'Pengawasan', 'Panev'])
                ->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->orderBy('id_sakip_level', 'asc')
                ->orderBy('satkernama', 'asc')
                ->get();

            $subIds = $subordinateQuery->pluck('id_satker')->toArray();
            $subLheFiles = LheAkip::whereIn('id_satker', $subIds)
                ->where('id_periode', $tahun)
                ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
                ->get()
                ->groupBy('id_satker');

            $subordinateSatkers = $subordinateQuery->map(function ($stk) use ($lkeController, $tahun, $subLheFiles) {
                $stkScores = $lkeController->calculateHierarchyScores($stk->id_satker, $tahun, 'evaluasi');
                $stk->score = $stkScores['grand_total'];
                $stk->predikat = $stkScores['predikat_evaluasi'];
                $stk->lheFiles = $subLheFiles->get($stk->id_satker, collect());
                $stk->latestLhe = $stk->lheFiles->first();
                return $stk;
            });
        } elseif ($evaluatorRole === 'jamwas' || $evaluatorRole === 'admin') {
            $kejatiQuery = DB::table('sinori_login')
                ->where(function ($q) {
                    $q->where('id_sakip_level', 2)
                      ->orWhere(function ($sub) {
                          $sub->where('id_sakip_level', 1)
                              ->whereIn('id_satker', $this->centralUnitSatkerIds);
                      });
                })
                ->where('id_satker', 'not like', 'was%')
                ->whereNotIn('id_satker', ['admin', '999999', '888881', '888882', 'Pengawasan', 'Panev'])
                ->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->orderBy('id_sakip_level', 'asc')
                ->orderBy('satkernama', 'asc')
                ->get();

            $kejatiIds = $kejatiQuery->pluck('id_satker')->toArray();
            $kejatiLheFiles = LheAkip::whereIn('id_satker', $kejatiIds)
                ->where('id_periode', $tahun)
                ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
                ->get()
                ->groupBy('id_satker');

            $kejatiList = $kejatiQuery->map(function ($stk) use ($lkeController, $tahun, $kejatiLheFiles) {
                $stkScores = $lkeController->calculateHierarchyScores($stk->id_satker, $tahun, 'evaluasi');
                $stk->score = $stkScores['grand_total'];
                $stk->predikat = $stkScores['predikat_evaluasi'];
                $stk->lheFiles = $kejatiLheFiles->get($stk->id_satker, collect());
                $stk->latestLhe = $stk->lheFiles->first();
                return $stk;
            });
        }

        $tlLheAkipFiles = TLLheAkip::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->orderBy('id_tglupload', 'desc')
            ->get();
        $monevRenaksiFiles = MonevRenaksi::where('id_periode', $tahun)
            ->where('id_satker', $idSatker)
            ->orderBy('id_perubahan', 'desc')
            ->get();

        // === Ambil dokumen bukti dukung (group by kriteria) ===
        $buktiDukung = DB::table('bukti_dukung')
            ->where('id_satker', $idSatker)
            ->get()
            ->groupBy('id_kriteria');

        // === Ambil semua kriteria yang punya format_nama_file ===
        $kriteria = DB::table('kriteria')->get();

        foreach ($kriteria as $row) {
            if (!$row->format_nama_file) continue;

            $formats = array_map('trim', explode(';', $row->format_nama_file));

            foreach ($formats as $format) {
                $regex = '/^' . str_replace('_', '.*', preg_quote($format, '/')) . '/i';

                $dokumenList = DB::table('bukti_dukung')
                    ->where('id_satker', $idSatker)
                    ->where('id_kriteria', $row->id)
                    ->get()
                    ->filter(fn($d) => preg_match($regex, $d->link_bukti_dukung));

                if ($dokumenList->isNotEmpty()) {
                    if (!isset($buktiDukung[$row->id])) {
                        $buktiDukung[$row->id] = collect();
                    }
                    foreach ($dokumenList as $dok) {
                        $buktiDukung[$row->id]->push($dok);
                    }
                }
            }
        }

        $komponen = DB::table('komponen')
            ->leftJoin('sub_komponen', 'komponen.id', '=', 'sub_komponen.id_komponen')
            ->leftJoin('kriteria', 'sub_komponen.id_subkomponen', '=', 'kriteria.id_sub_komponen')
            ->select(
                'komponen.id as id_komponen',
                'komponen.nama_komponen',
                'sub_komponen.id_subkomponen',
                'sub_komponen.nama_subkomponen',
                'kriteria.id as id_kriteria',
                'kriteria.nama_kriteria',
                'kriteria.bukti_pengisian',
                'kriteria.format_nama_file',
                'kriteria.kode'
            )
            ->orderBy('komponen.id')
            ->orderBy('sub_komponen.id_subkomponen')
            ->orderBy('kriteria.id')
            ->get();


        // Tambahkan info jumlah kebutuhan vs realisasi
        $komponen->transform(function ($item) use ($buktiDukung) {
            // pecah kebutuhan dari kriteria
            $kebutuhan = $item->bukti_pengisian
                ? array_map('trim', explode(';', $item->bukti_pengisian))
                : [];

            // dokumen realisasi
            $realisasi = isset($buktiDukung[$item->id_kriteria])
                ? $buktiDukung[$item->id_kriteria]
                : collect();

            $item->jumlah_kebutuhan = count($kebutuhan);
            $item->jumlah_realisasi = $realisasi->count();
            $item->persen_terpenuhi = $item->jumlah_kebutuhan > 0
                ? round(($item->jumlah_realisasi / $item->jumlah_kebutuhan) * 100, 2)
                : 0;

            return $item;
        });

        // Load Unified LKE Hierarchy and Evidence for synchronized UX (matching Penilaian Mandiri)
        $targetTahun = LkeKomponen::where('tahun', $tahun)->exists() ? $tahun : 2025;
        $komponenList = LkeKomponen::where('tahun', $targetTahun)
            ->with([
                'subkomponen' => function ($q) use ($targetTahun) {
                    $q->where('tahun', $targetTahun);
                },
                'subkomponen.kriteria' => function ($q) use ($targetTahun) {
                    $q->where('tahun', $targetTahun);
                },
            ])
            ->orderBy('id')
            ->get();

        $satkerBukti = LkeEvidenceHelper::getUnifiedSatkerEvidences($idSatker, $tahun);
        $masterBukti = LkeBuktidukung::pluck('dokumen', 'id');

        // Mapping format file & kebutuhan kriteria berdasarkan kode kriteria bersih
        $legacyKriteria = DB::table('kriteria')->get();
        $formatMap = [];
        $needMap = [];
        foreach ($legacyKriteria as $lk) {
            $cleanCode = LkeEvidenceHelper::cleanKriteriaKode($lk->kode ?: $lk->id);
            if (!isset($formatMap[$cleanCode])) {
                $formatMap[$cleanCode] = [];
                $needMap[$cleanCode] = [];
            }
            if ($lk->format_nama_file) {
                foreach (explode(';', $lk->format_nama_file) as $f) {
                    $f = trim($f);
                    if ($f && !in_array($f, $formatMap[$cleanCode])) {
                        $formatMap[$cleanCode][] = $f;
                    }
                }
            }
            if ($lk->bukti_pengisian) {
                foreach (explode(';', $lk->bukti_pengisian) as $n) {
                    $n = trim($n);
                    if ($n && !in_array($n, $needMap[$cleanCode])) {
                        $needMap[$cleanCode][] = $n;
                    }
                }
            }
        }

        // Summary metrics kelengkapan bukti dukung LKE yang sinkron dengan Penilaian Mandiri
        $allCriteria = $komponenList->flatMap(fn($k) => $k->subkomponen->flatMap(fn($s) => $s->kriteria));
        $totalKriteriaLke = $allCriteria->count();
        $lengkapCount = 0;
        foreach ($allCriteria as $cr) {
            $reqIds = $cr->bukti_dukung_ids;
            if (empty($reqIds)) {
                $lengkapCount++;
                continue;
            }
            $crEvidences = $satkerBukti->get($cr->kode, collect());
            $allMet = true;
            foreach ($reqIds as $bId) {
                $has = $crEvidences->first(function ($item) use ($bId) {
                    return $item->buktidukung_id == $bId
                        || $item->kode_bukti == $bId
                        || LkeEvidenceHelper::matchesBuktiId($item->link_bukti_dukung, (int)$bId);
                });
                if (!$has) {
                    $allMet = false;
                    break;
                }
            }
            if ($allMet) {
                $lengkapCount++;
            }
        }
        $belumCount = $totalKriteriaLke - $lengkapCount;
        $persenLengkap = $totalKriteriaLke > 0 ? round(($lengkapCount / $totalKriteriaLke) * 100, 1) : 0;

        $sections = [
            'Perencanaan' => DataLke1::where('subkomponen_id', 'LIKE', '1.%')->get(),
            'Pengukuran'  => DataLke1::where('subkomponen_id', 'LIKE', '2.%')->get(),
            'Pelaporan'   => DataLke1::where('subkomponen_id', 'LIKE', '3.%')->get(),
            'Evaluasi Akuntabilitas Kinerja Internal (LKE Eval AKIP)' => DataLke1::where('subkomponen_id', 'LIKE', '4.%')->get(),
        ];

        return view('kelola.evaluasi', [
            'tahun'             => $tahun,
            'lheAkipFiles'      => $lheAkipFiles,
            'monevRenaksiFiles' => $monevRenaksiFiles,
            'tlLheAkipFiles'    => $tlLheAkipFiles,
            'komponen'          => $komponen,
            'komponenList'      => $komponenList,
            'satkerBukti'       => $satkerBukti,
            'masterBukti'       => $masterBukti,
            'formatMap'         => $formatMap,
            'needMap'           => $needMap,
            'idSatker'          => $idSatker,
            'buktiDukung'       => $buktiDukung,
            'currentUser'        => $currentUser,
            'evaluatorRole'      => $evaluatorRole,
            'myLheScore'         => $myLheScore,
            'subordinateSatkers' => $subordinateSatkers,
            'kejatiList'         => $kejatiList,
            'sections'          => $sections,
            'totalKriteriaLke'  => $totalKriteriaLke,
            'lengkapCount'      => $lengkapCount,
            'belumCount'        => $belumCount,
            'persenLengkap'     => $persenLengkap,
        ]);
    }

    // 📌 Upload LHE AKIP
    public function uploadLheAkip(Request $request)
    {
        $request->validate([
            'target_satker_id' => 'required|string',
            'lhe_akip_file'    => 'required|mimes:pdf|max:4096',
        ]);

        $tahun = session('tahun_terpilih', date('Y'));
        $targetSatkerId = $request->input('target_satker_id');

        // Validasi Otorisasi: Kejari/Cabjari tidak boleh upload, Kejati hanya untuk wilayah hukumnya, JAMWAS/Admin untuk Kejati
        if (!$this->isTargetSatkerAllowedForUpload($targetSatkerId)) {
            return back()->with([
                'error-lhe'  => 'Akses ditolak: Anda tidak memiliki wewenang untuk mengunggah dokumen LHE AKIP untuk Satuan Kerja ini.',
                'active_tab' => 'lhe-akip',
            ]);
        }

        $targetSatker = DB::table('sinori_login')->where('id_satker', $targetSatkerId)->first();
        if (!$targetSatker) {
            return back()->with([
                'error-lhe'  => 'Satuan Kerja target tidak ditemukan.',
                'active_tab' => 'lhe-akip',
            ]);
        }

        // Cek id_perubahan yang sudah ada untuk satker target
        $latestLhe = LheAkip::where('id_satker', $targetSatkerId)
            ->where('id_periode', $tahun)
            ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
            ->first();

        $id_perubahan = $latestLhe ? ($latestLhe->id_perubahan + 1) : 0;

        // Pastikan folder tujuan ada
        $publicDest = public_path('uploads/repository/' . $targetSatkerId);
        if (!file_exists($publicDest)) {
            mkdir($publicDest, 0777, true);
        }

        $file = $request->file('lhe_akip_file');
        $fileName = 'lhe_' . $tahun . '_' . $id_perubahan . '.pdf';
        $file->move($publicDest, $fileName);

        // Jika direktori base_path terpisah dan ada, lakukan sinkronisasi salinan berkas
        $baseDest = base_path('uploads/repository/' . $targetSatkerId);
        if ($baseDest !== $publicDest && file_exists(base_path('uploads/repository'))) {
            if (!file_exists($baseDest)) {
                @mkdir($baseDest, 0777, true);
            }
            @copy($publicDest . '/' . $fileName, $baseDest . '/' . $fileName);
        }

        LheAkip::create([
            'id_periode'   => $tahun,
            'id_satker'    => $targetSatkerId,
            'id_perubahan' => $id_perubahan,
            'id_filename'  => $fileName,
            'id_tglupload' => now()->format('d/m/Y h:i A'),
        ]);

        $targetNama = str_replace('_', ' ', $targetSatker->satkernama);
        return redirect()->route('evaluasi')->with([
            'success-lhe' => "Dokumen LHE AKIP (v{$id_perubahan}) berhasil diunggah untuk {$targetNama}!",
            'active_tab'  => 'lhe-akip',
        ]);
    }

    // 📌 Hapus Berkas Dokumen LHE AKIP (Hanya Evaluator Berwenang)
    public function deleteLheAkip($id)
    {
        $lhe = LheAkip::findOrFail($id);

        if (!$this->isTargetSatkerAllowedForUpload($lhe->id_satker)) {
            return back()->with([
                'error-lhe'  => 'Akses ditolak: Anda tidak memiliki wewenang untuk menghapus dokumen LHE AKIP Satuan Kerja ini.',
                'active_tab' => 'lhe-akip',
            ]);
        }

        $targetSatkerId = $lhe->id_satker;
        $fileName = $lhe->id_filename;

        // Hapus file fisik
        $publicFile = public_path("uploads/repository/{$targetSatkerId}/{$fileName}");
        if (file_exists($publicFile)) {
            @unlink($publicFile);
        }
        $baseFile = base_path("uploads/repository/{$targetSatkerId}/{$fileName}");
        if (file_exists($baseFile)) {
            @unlink($baseFile);
        }

        $lhe->delete();

        return redirect()->route('evaluasi')->with([
            'success-lhe' => 'Dokumen LHE AKIP berhasil dihapus.',
            'active_tab'  => 'lhe-akip',
        ]);
    }

    // 📌 Endpoint AJAX: Mengambil Rincian Lengkap Nilai 4 Komponen & Subkomponen Satker
    public function getSatkerLheDetail(Request $request)
    {
        $idSatker = $request->query('id_satker');
        $tahun = session('tahun_terpilih', date('Y'));

        if (!$idSatker) {
            return response()->json(['success' => false, 'message' => 'ID Satker diperlukan.'], 400);
        }

        $currentUser = $this->getCurrentUser();
        $isSelf = ($currentUser && (string)$currentUser->id_satker === (string)$idSatker);
        $isAllowedEvaluator = $this->isTargetSatkerAllowedForUpload($idSatker);

        if (!$isSelf && !$isAllowedEvaluator) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak untuk melihat rincian Satker ini.'], 403);
        }

        $satker = DB::table('sinori_login')->where('id_satker', $idSatker)->first();
        if (!$satker) {
            return response()->json(['success' => false, 'message' => 'Satuan kerja tidak ditemukan.'], 404);
        }

        $lkeController = new LkeEvaluasiController();
        $scores = $lkeController->calculateHierarchyScores($idSatker, $tahun, 'evaluasi');

        $lheFiles = LheAkip::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
            ->get();

        return response()->json([
            'success'   => true,
            'satker'    => [
                'id_satker'  => $satker->id_satker,
                'satkernama' => str_replace('_', ' ', $satker->satkernama),
                'level'      => $satker->id_sakip_level,
            ],
            'scores'    => $scores,
            'lhe_files' => $lheFiles,
            'tahun'     => $tahun,
        ]);
    }

    public function uploadTlLheAkip(Request $request)
    {
        $role = $this->getEvaluatorRole();
        if ($role === 'jamwas' || $role === 'pengawasan_kejati') {
            return back()->with([
                'error-lhe'  => 'Akses ditolak: Akun Pengawasan tidak berwenang mengunggah dokumen TL LHE AKIP.',
                'active_tab' => 'lhe-akip',
            ]);
        }

        $request->validate([
            'tllhe_file' => 'required|mimes:pdf|max:4096',
        ]);

        $tahun = session('tahun_terpilih');
        $idSatker = session('id_satker');

        // Cek versi terbaru
        $latestFile = TlLheAkip::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
            ->first();

        $id_perubahan = $latestFile ? $latestFile->id_perubahan + 1 : 0;

        if ($request->hasFile('tllhe_file')) {
            $file = $request->file('tllhe_file');
            $filename = 'tllhe_' . $tahun . '_' . $id_perubahan . '.pdf';
            $file->move(public_path('uploads/repository/' . $idSatker), $filename);

            TlLheAkip::create([
                'id_periode' => $tahun,
                'id_satker' => $idSatker,
                'id_perubahan' => $id_perubahan,
                'id_filename' => $filename,
                'id_tglupload' => now()->format('d/m/Y h:i A'),

            ]);

            return redirect()->route('evaluasi')->with(['success-tllhe' => 'File TL LHE AKIP berhasil diunggah.', 'active_tab' => 'tl-lhe-akip']);
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    public function uploadMonevRenaksi(Request $request)
    {
        $role = $this->getEvaluatorRole();
        if ($role === 'jamwas' || $role === 'pengawasan_kejati') {
            return back()->with([
                'error-lhe'  => 'Akses ditolak: Akun Pengawasan tidak berwenang mengunggah dokumen Monev Renaksi.',
                'active_tab' => 'lhe-akip',
            ]);
        }

        $request->validate([
            'id_triwulan' => 'required|in:TW 1,TW 2,TW 3,TW 4',
            'monev_file' => 'required|mimes:pdf|max:4096',
        ]);

        $tahun = session('tahun_terpilih');
        $idSatker = session('id_satker');
        $id_triwulan = $request->input('id_triwulan');

        // Ambil versi terakhir untuk triwulan tersebut
        $latestmonev = MonevRenaksi::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->where('id_triwulan', $id_triwulan)
            ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
            ->first();

        $id_perubahan = $latestmonev ? $latestmonev->id_perubahan + 1 : 0;

        if ($request->hasFile('monev_file')) {
            $file = $request->file('monev_file');

            // Pastikan folder ada
            $destination = base_path('uploads/repository/' . $idSatker);
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $filename = 'renaksieval_' . $tahun . '_' . $id_perubahan . '_' . $id_triwulan . '.pdf';
            $file->move($destination, $filename);

            // Simpan data ke database
            MonevRenaksi::create([
                'id_periode' => $tahun,
                'id_satker' => $idSatker,
                'id_perubahan' => $id_perubahan,
                'id_filename' => $filename,
                'id_tglupload' => now()->format('d/m/Y h:i A'),
                'id_triwulan' => $id_triwulan,
            ]);

            return redirect()->route('evaluasi')
                ->with([
                    'success-monev' => 'File Monev Renaksi berhasil diunggah.',
                    'active_tab' => 'monev-renaksi'
                ]);
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }


    public function verifikasi(Request $request)
    {
        $role = $this->getEvaluatorRole();
        if ($role === 'jamwas' || $role === 'pengawasan_kejati') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak: Akun Pengawasan tidak melakukan verifikasi bukti dukung PM.'], 403);
        }

        $idKriteria    = $request->input('id_kriteria');
        $idSatker      = session('id_satker');
        $idKomponen    = $request->input('id_komponen');
        $idSubKomponen = $request->input('id_subkomponen');
        $tahun         = session('tahun_terpilih', date('Y'));

        // Ambil formats jika dikirimkan langsung
        $formats = [];
        if ($request->has('formats') && !empty($request->input('formats'))) {
            $rawFormats = $request->input('formats');
            $formats = is_array($rawFormats) ? $rawFormats : json_decode($rawFormats, true);
        }

        // Jika formats kosong, cari dari tabel kriteria (support id, kode, atau prefix)
        if (empty($formats)) {
            $matchingKriteria = DB::table('kriteria')
                ->where('id', $idKriteria)
                ->orWhere('kode', $idKriteria)
                ->orWhere('id', 'like', $idKriteria . '.%')
                ->get();

            foreach ($matchingKriteria as $mk) {
                if ($mk->format_nama_file) {
                    foreach (explode(';', $mk->format_nama_file) as $f) {
                        $f = trim($f);
                        if ($f && !in_array($f, $formats)) {
                            $formats[] = $f;
                        }
                    }
                }
            }
        }

        if (empty($formats)) {
            return response()->json(['success' => false, 'message' => 'Format dokumen kriteria tidak ditemukan.']);
        }

        $mapTable = [
            'renstra'     => ['table' => 'sinori_sakip_renstra',    'prefix' => 'renstra'],
            'renja'       => ['table' => 'sinori_sakip_renja',      'prefix' => 'renja'],
            'iku'         => ['table' => 'sinori_sakip_iku',        'prefix' => 'IKU'],
            'pk'          => ['table' => 'pk',                      'prefix' => 'pk'],
            'renaksi'     => ['table' => 'sinori_sakip_renaksi',    'prefix' => 'renaksi'],
            'rkakl'       => ['table' => 'sinori_sakip_rkakl',      'prefix' => 'rkakl'],
            'dipa'        => ['table' => 'sinori_sakip_dipa',       'prefix' => 'dipa'],
            'lkjip'       => ['table' => 'sinori_sakip_lakip',      'prefix' => 'lkjip'],
            'rastaff'     => ['table' => 'sinori_sakip_rastaff',    'prefix' => 'rastaff'],
            'renaksieval' => ['table' => 'sinori_sakip_renaksieval', 'prefix' => 'renaksieval'],
            'lhe'         => ['table' => 'lhe',                     'prefix' => 'lhe'],
            'tllhe'       => ['table' => 'tl_lhe_akip',             'prefix' => 'tllhe'],
        ];

        $results = [];

        foreach ($formats as $format) {
            if (stripos($format, '(perubahan terakhir)') !== false) {
                $tipe = strtolower(strtok($format, '_'));
                if (!isset($mapTable[$tipe])) continue;

                $source = $mapTable[$tipe];

                preg_match('/\d{4}/', $format, $match);
                $tahunFormat = $match[0] ?? $tahun;

                preg_match('/TW\s*[1-4]/i', $format, $matchPeriode);
                $periodeTambahan = $matchPeriode[0] ?? null;
                $idTriwulan = $matchPeriode[1] ?? null;

                $idPeriode = ($tipe === 'renstra')
                    ? (($tahunFormat == 2024) ? 'P1' : 'P2')
                    : $tahunFormat;

                $query = DB::table($source['table'])
                    ->where('id_satker', $idSatker)
                    ->where('id_periode', $idPeriode);

                if (in_array($source['table'], ['lakip', 'restaff', 'renaksieval', 'sinori_sakip_lakip', 'sinori_sakip_rastaff', 'sinori_sakip_renaksieval']) && $idTriwulan) {
                    $query->where('id_triwulan', $idTriwulan);
                }

                $dokumen = $query->orderByDesc('id_perubahan')->first();

                if ($dokumen) {
                    $namaFile = $source['prefix'] . '_' . $tahunFormat . '_' . $dokumen->id_perubahan;

                    if ($periodeTambahan) {
                        $namaFile .= '_' . strtoupper(str_replace('TW', 'TW', $periodeTambahan));
                    }
                    $namaFile .= '.pdf';

                    // Simpan tersinkronisasi ke bukti_dukung dan lke_satker_bukti
                    LkeEvidenceHelper::syncEvidenceRecord(
                        (string)$idSatker,
                        (string)$idKriteria,
                        $namaFile,
                        $idKomponen,
                        $idSubKomponen,
                        $tahun
                    );

                    $results[] = $namaFile;
                }
            }
        }
        return response()->json(['success' => true, 'synced' => $results]);
    }


    /**
     * Upload manual (untuk format tanpa "(perubahan terakhir)")
     */
    public function upload(Request $request)
    {
        $role = $this->getEvaluatorRole();
        if ($role === 'jamwas' || $role === 'pengawasan_kejati') {
            if ($request->expectsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Akun Pengawasan tidak berwenang mengunggah bukti dukung PM.'
                ], 403);
            }
            return back()->with([
                'error-lhe'  => 'Akses ditolak: Akun Pengawasan tidak berwenang mengunggah bukti dukung PM.',
                'active_tab' => 'lhe-akip',
            ]);
        }

        $request->validate([
            'files.*'        => 'required|mimes:pdf|max:4096',
            'id_kriteria'    => 'required',
            'id_komponen'    => 'required',
            'id_subkomponen' => 'required',
        ]);

        $idKriteria    = $request->input('id_kriteria');
        $idSatker      = session('id_satker');
        $idKomponen    = $request->input('id_komponen');
        $idSubKomponen = $request->input('id_subkomponen');
        $tahun         = session('tahun_terpilih', date('Y'));

        $matchingKriteria = DB::table('kriteria')
            ->where('id', $idKriteria)
            ->orWhere('kode', $idKriteria)
            ->orWhere('id', 'like', $idKriteria . '.%')
            ->get();

        $formats = [];
        foreach ($matchingKriteria as $mk) {
            if ($mk->format_nama_file) {
                foreach (explode(';', $mk->format_nama_file) as $f) {
                    $f = trim($f);
                    if ($f && !in_array($f, $formats)) {
                        $formats[] = $f;
                    }
                }
            }
        }

        $manualFormats = collect($formats)->filter(fn($f) => stripos($f, '(perubahan terakhir)') === false)->values();

        $tujuanPath = public_path("uploads/repository/{$idSatker}");
        if (!file_exists($tujuanPath)) {
            mkdir($tujuanPath, 0777, true);
        }

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $manualFormat = $manualFormats[$index] ?? ('dokumen_' . ($index + 1));
                $namaFile = str_replace(' ', '_', $manualFormat) . '.pdf';

                $file->move($tujuanPath, $namaFile);

                // Simpan tersinkronisasi ke bukti_dukung dan lke_satker_bukti
                LkeEvidenceHelper::syncEvidenceRecord(
                    (string)$idSatker,
                    (string)$idKriteria,
                    $namaFile,
                    $idKomponen,
                    $idSubKomponen,
                    $tahun
                );

                $uploadedFiles[] = asset("uploads/repository/{$idSatker}/{$namaFile}");
            }
        }

        return back()->with('success', 'Upload bukti dukung berhasil dan disinkronkan.');
    }
}
