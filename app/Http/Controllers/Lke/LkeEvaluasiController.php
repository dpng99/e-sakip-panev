<?php

namespace App\Http\Controllers\Lke;

use App\Http\Controllers\Controller;
use App\Models\LkeBuktidukung;
use App\Models\LkeKomponen;
use App\Models\LkeKriteria;
use App\Models\LkeParameter;
use App\Models\LkePenilaianSatker;
use App\Models\LkeSatkerBukti;
use App\Models\LkeSubkomponen;
use App\Models\LkeSubkomponenKoefisien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LkeEvaluasiController extends Controller
{
    /**
     * Get current authenticated user record from sinori_login.
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
     * Determine evaluator type:
     * - 'admin': Can inspect and evaluate all satkers.
     * - 'jamwas': Can inspect and evaluate all satkers (Jaksa Agung Muda Bidang Pengawasan).
     * - 'unauthorized': Blocked from evaluation.
     */
    /**
     * Satker IDs of Eselon I / Pusat that can ONLY be evaluated by JAMWAS.
     */
    protected $centralUnitSatkerIds = [
        '005016', // JAM_BIDANG_PEMBINAAN (JAMBIN)
        '419345', // JAM_BIDANG_INTELIJEN (JAMINTEL)
        '417023', // JAM_BIDANG_PERDATA_DAN_TUN (JAMDATUN)
        '418326', // JAM_BIDANG_TINDAK_PIDANA_UMUM (JAMPIDUM)
        '419344', // JAM_BIDANG_TINDAK_PIDANA_KHUSUS (JAMPIDSUS)
        '677111', // JAM_BIDANG_PIDANA_MILITER (JAMPIDMIL)
        '691270', // BADAN PEMULIHAN ASET (BPA)
        '666405', // BADAN_PENDIDIKAN_DAN_PELATIHAN (BADIKLAT)
        '419346', // JAM_BIDANG_PENGAWASAN (JAMWAS Satker - PM Only)
    ];

    /**
     * Check if a target satker can ONLY be evaluated by JAMWAS:
     * - Seluruh Kejaksaan Tinggi (id_sakip_level == 2)
     * - Unit Eselon I / Pusat: JAMBIN, JAMINTEL, JAMDATUN, JAMPIDUM, JAMPIDSUS, JAMPIDMIL, BPA, BADIKLAT, JAMWAS (419346)
     */
    protected function isOnlyEvaluatedByJamwas($targetSatker)
    {
        if (!$targetSatker) {
            return false;
        }

        $targetId = (string) $targetSatker->id_satker;
        $targetLevel = (int) $targetSatker->id_sakip_level;
        $targetNama = strtolower($targetSatker->satkernama ?? '');

        // 1. Seluruh Kejaksaan Tinggi (Level 2)
        if ($targetLevel === 2 || str_contains($targetNama, 'kejati')) {
            return true;
        }

        // 2. Unit Eselon I / Pusat
        if (in_array($targetId, $this->centralUnitSatkerIds)) {
            return true;
        }

        if (
            str_contains($targetNama, 'jambin') ||
            str_contains($targetNama, 'jam_bidang_pembinaan') ||
            str_contains($targetNama, 'jamintel') ||
            str_contains($targetNama, 'jam_bidang_intelijen') ||
            str_contains($targetNama, 'jamdatun') ||
            str_contains($targetNama, 'jam_bidang_perdata') ||
            str_contains($targetNama, 'jampidum') ||
            str_contains($targetNama, 'jam_bidang_tindak_pidana_umum') ||
            str_contains($targetNama, 'jampidsus') ||
            str_contains($targetNama, 'jam_bidang_tindak_pidana_khusus') ||
            str_contains($targetNama, 'jampidmil') ||
            str_contains($targetNama, 'jam_bidang_pidana_militer') ||
            str_contains($targetNama, 'pemulihan_aset') ||
            str_contains($targetNama, 'pemulihan aset') ||
            str_contains($targetNama, 'badiklat') ||
            str_contains($targetNama, 'badan_pendidikan') ||
            str_contains($targetNama, 'jamwas') ||
            str_contains($targetNama, 'jam_bidang_pengawasan')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine evaluator type:
     * - 'admin': Administrator Pusat
     * - 'jamwas': Tim Pengawasan Pusat (user 888882 - mengevaluasi Kejati & para Eselon 1)
     * - 'pengawasan_kejati': Pengawasan Kejaksaan Tinggi (user wasxxxxxx - mengevaluasi Kejari & Cabjari di wilayah hukumnya)
     * - 'pm': Satker Penilaian Mandiri (user 419346 JAMWAS, Kejati reguler, Kejari, Cabjari, Unit Pusat)
     * - 'unauthorized': Blocked from evaluation.
     */
    protected function getEvaluatorType()
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
        // Catatan: User 419346 adalah satker JAMWAS yang HANYA bisa melakukan PM!
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

        // 4. PM (Penilaian Mandiri) Satker:
        // Termasuk user 419346 (JAMWAS), Kejati reguler (level 2), Kejari (level 3), Cabjari (level 4), Eselon 1 lainnya (level 1)
        if (
            $satker === '419346'
            || in_array($level, [1, 2, 3, 4, '1', '2', '3', '4'])
            || in_array($satker, $this->centralUnitSatkerIds)
        ) {
            return 'pm';
        }

        return 'unauthorized';
    }

    /**
     * Check if user is authorized to access LKE evaluation module.
     */
    protected function checkEvaluator()
    {
        $type = $this->getEvaluatorType();

        if ($type === 'unauthorized') {
            abort(403, 'Akses Ditolak: Menu Penilaian LKE hanya dapat dilakukan oleh Satuan Kerja (PM), Kejaksaan Tinggi, JAMWAS, dan ADMIN.');
        }
    }

    /**
     * Check if a specific target satker is authorized to be evaluated or given PM by current user.
     */
    protected function isAuthorizedSatkerForEvaluator($targetSatkerId, $tahap = 'evaluasi')
    {
        $evaluatorType = $this->getEvaluatorType();
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            return false;
        }

        $target = DB::table('sinori_login')->where('id_satker', $targetSatkerId)->first();
        if (!$target) {
            return false;
        }

        // 1. Jika tahap Penilaian Mandiri (PM): User pengawasan (jamwas & pengawasan_kejati) TIDAK BOLEH melakukan PM!
        if ($tahap === 'pm') {
            if ($evaluatorType === 'jamwas' || $evaluatorType === 'pengawasan_kejati') {
                return false;
            }
            return ((string)$target->id_satker === (string)$currentUser->id_satker);
        }

        // 2. Jika tahap Evaluasi:
        // Admin Pusat berwenang mengevaluasi seluruh satker
        if ($evaluatorType === 'admin') {
            return true;
        }

        // JAMWAS (888882) berwenang mengevaluasi Seluruh KEJATI (Level 2) dan Unit Eselon I (termasuk 419346)
        if ($evaluatorType === 'jamwas') {
            return $this->isOnlyEvaluatedByJamwas($target);
        }

        // Pengawasan Kejati (wasxxxxxx) berwenang melakukan EVALUASI terhadap Kejaksaan Negeri (3) dan Cabang Kejaksaan Negeri (4) di wilayah hukumnya
        if ($evaluatorType === 'pengawasan_kejati') {
            $targetLevel = (int)$target->id_sakip_level;
            if (in_array($targetLevel, [3, 4])) {
                return (!empty($currentUser->id_kejati) && (string)$target->id_kejati === (string)$currentUser->id_kejati);
            }
            return false;
        }

        // Akun Penilaian Mandiri (419346, Kejati reguler, Kejari, Cabjari) tidak berwenang mengevaluasi satker lain
        return false;
    }

    /**
     * Main evaluation page.
     */
    public function index(Request $request)
    {
        $this->checkEvaluator();

        $tahun = $request->get('tahun', session('tahun_terpilih') ?? 2025);
        $evaluatorType = $this->getEvaluatorType();
        $currentUser = $this->getCurrentUser();
        $userLevel = (int)($currentUser ? $currentUser->id_sakip_level : session('id_sakip_level'));
        $userSatkerId = (string)($currentUser ? $currentUser->id_satker : session('id_satker'));

        if ($evaluatorType === 'jamwas') {
            // JAMWAS (888882): Menilai Unit Eselon I (termasuk 419346) dan Seluruh KEJATI (Level 2)
            $satkerList = DB::table('sinori_login')
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
        } elseif ($evaluatorType === 'pengawasan_kejati') {
            // Pengawasan Kejati (wasxxxxxx): Menilai Kejari (3) dan Cabjari (4) di wilayah hukum Kejati miliknya
            $kejatiId = $currentUser ? $currentUser->id_kejati : null;

            $query = DB::table('sinori_login')
                ->where(function ($q) use ($kejatiId, $userSatkerId) {
                    if ($kejatiId) {
                        $q->where('id_kejati', $kejatiId);
                    } else {
                        $q->where('id_satker', $userSatkerId);
                    }
                })
                ->whereIn('id_sakip_level', [3, 4])
                ->where('id_satker', 'not like', 'was%')
                ->whereNotIn('id_satker', ['admin', '999999', '888881', '888882', 'Pengawasan', 'Panev']);

            $satkerList = $query->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->orderBy('id_sakip_level', 'asc')
                ->orderBy('satkernama', 'asc')
                ->get();
        } elseif ($evaluatorType === 'admin') {
            // Admin: seluruh satker
            $satkerList = DB::table('sinori_login')
                ->whereNotIn('id_satker', ['admin', '999999', '888881', '888882', 'Pengawasan', 'Panev'])
                ->where('id_satker', 'not like', 'was%')
                ->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->orderByRaw("CASE WHEN id_sakip_level = 1 THEN 0 WHEN id_sakip_level = 2 THEN 1 ELSE 2 END")
                ->orderBy('satkernama', 'asc')
                ->get();
        } else {
            // Satker PM (419346, Kejati reguler, Kejari, Cabjari): hanya dirinya sendiri
            $satkerList = DB::table('sinori_login')
                ->where('id_satker', $userSatkerId)
                ->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->get();
        }

        // Validasi Satker terpilih
        $selectedSatker = $request->get('id_satker');
        $allowedIds = $satkerList->pluck('id_satker')->map(fn($id) => (string)$id)->toArray();

        if ($selectedSatker && !in_array((string)$selectedSatker, $allowedIds)) {
            $selectedSatker = null;
        }

        if (!$selectedSatker && $satkerList->isNotEmpty()) {
            $self = $satkerList->firstWhere('id_satker', session('id_satker'));
            $selectedSatker = $self ? $self->id_satker : $satkerList->first()->id_satker;
        }

        $satkerInfo = null;
        if ($selectedSatker) {
            $satkerInfo = DB::table('sinori_login')->where('id_satker', $selectedSatker)->first();
        }

        // Tentukan Mode Aktif ('evaluasi' atau 'pm') untuk Satker yang sedang dibuka:
        $selectedId = (string)$selectedSatker;
        if ($evaluatorType === 'admin') {
            $activeMode = $request->get('mode', 'evaluasi');
        } elseif ($evaluatorType === 'jamwas') {
            // JAMWAS selalu bertindak sebagai Evaluator
            $activeMode = 'evaluasi';
        } elseif ($evaluatorType === 'pengawasan_kejati') {
            // Pengawasan Kejati (wasxxxxxx) selalu bertindak sebagai Evaluator untuk Kejari & Cabjari di wilayahnya
            $activeMode = 'evaluasi';
        } else {
            // Satker PM (419346, Kejati reguler, Kejari, Cabjari): selalu mode PM
            $activeMode = 'pm';
        }

        // Fetch Komponen with Subkomponen and Kriteria
        $targetTahun = LkeKomponen::where('tahun', $tahun)->exists() ? $tahun : 2025;
        $komponenList = LkeKomponen::where('tahun', $targetTahun)
            ->with([
                'subkomponen' => function ($q) use ($targetTahun) {
                    $q->where('tahun', $targetTahun);
                },
                'subkomponen.kriteria' => function ($q) use ($targetTahun) {
                    $q->where('tahun', $targetTahun);
                },
                'subkomponen.kriteria.parameters' => function ($q) use ($targetTahun) {
                    $q->where('tahun', $targetTahun);
                }
            ])
            ->orderBy('no')
            ->get();

        // Fetch existing evaluations for this satker and tahun
        $existingPenilaian = [];
        if ($selectedSatker) {
            $existingPenilaian = LkePenilaianSatker::where('id_satker', $selectedSatker)
                ->where('tahun', $tahun)
                ->get()
                ->keyBy('kriteria_id');
        }

        // Fetch uploaded evidence files for this satker (Unified & Synchronized)
        $satkerBukti = collect();
        if ($selectedSatker) {
            $satkerBukti = \App\Helpers\LkeEvidenceHelper::getUnifiedSatkerEvidences($selectedSatker, $tahun);
        }

        // Master buktidukung map
        $masterBukti = LkeBuktidukung::pluck('dokumen', 'id');

        // Fetch subkomponen koefisien for this satker and tahun
        $subkomponenKoefisien = [];
        if ($selectedSatker) {
            $subkomponenKoefisien = LkeSubkomponenKoefisien::where('id_satker', $selectedSatker)
                ->where('tahun', $tahun)
                ->get()
                ->keyBy('subkomponen_kode');
        }

        // Calculate scores hierarchically
        $scores = $this->calculateHierarchyScores($selectedSatker, $tahun, $activeMode);

        return view('lke.evaluasi.index', compact(
            'satkerList',
            'selectedSatker',
            'satkerInfo',
            'tahun',
            'komponenList',
            'existingPenilaian',
            'satkerBukti',
            'masterBukti',
            'scores',
            'evaluatorType',
            'activeMode',
            'subkomponenKoefisien'
        ));
    }

    /**
     * AJAX endpoint to save evaluation for a criteria and recalculate scores.
     */
    public function saveScore(Request $request)
    {
        $this->checkEvaluator();

        $request->validate([
            'id_satker'     => 'required|string',
            'tahun'         => 'required|integer',
            'kriteria_id'   => 'required|string',
            'parameter_id'  => 'required|integer|exists:lke_parameter,id',
            'catatan'       => 'nullable|string',
            'tahap'         => 'nullable|in:pm,evaluasi,pk',
        ]);

        $idSatker = $request->id_satker;
        $tahap = $request->get('tahap', 'evaluasi');
        if ($tahap === 'pk') {
            $tahap = 'evaluasi';
        }

        // Validasi eksplisit: User Pengawasan dilarang keras melakukan Penilaian Mandiri (PM)
        $evaluatorType = $this->getEvaluatorType();
        if (($evaluatorType === 'jamwas' || $evaluatorType === 'pengawasan_kejati') && $tahap === 'pm') {
            return response()->json([
                'success' => false,
                'message' => 'Akses Ditolak: User Pengawasan tidak berwenang melakukan Penilaian Mandiri (PM).',
            ], 403);
        }

        // Validasi otoritas wilayah / jenjang evaluator terhadap Satker yang dinilai
        if (!$this->isAuthorizedSatkerForEvaluator($idSatker, $tahap)) {
            $labelAksi = ($tahap === 'pm') ? 'Penilaian Mandiri (PM)' : 'Evaluasi';
            return response()->json([
                'success' => false,
                'message' => "Akses Ditolak: Anda tidak memiliki wewenang untuk melakukan {$labelAksi} terhadap Satuan Kerja ini.",
            ], 403);
        }

        $tahun = $request->tahun;
        $kriteriaId = $request->kriteria_id;
        $parameterId = $request->parameter_id;
        $catatan = $request->catatan;

        // Fetch parameter details
        $param = LkeParameter::findOrFail($parameterId);

        // Validasi: Untuk kriteria yang dipilih "Tidak Terpenuhi", wajib mengisi kotak Uraian/Penjelasan (catatan)
        if (trim(strtolower($param->nilai)) === 'tidak terpenuhi' || $param->skor == 0) {
            if (empty(trim($catatan ?? ''))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kriteria yang dipilih "Tidak Terpenuhi" wajib mengisi kotak Uraian/Penjelasan baru bisa disimpan.',
                ], 422);
            }
        }

        $kriteria = LkeKriteria::with('subkomponen')->where('kode', $kriteriaId)->first();

        $sub = $kriteria ? $kriteria->subkomponen : null;
        $subkomponenId = $sub ? $sub->kode : ($kriteria ? $kriteria->subkomponen_id : null);
        $komponenId = $sub ? $sub->komponen_id : null;

        if ($tahap === 'pm') {
            // Simpan penilaian mandiri (PM)
            $dataToUpdate = [
                'komponen_id'     => $komponenId,
                'subkomponen_id'  => $subkomponenId,
                'pm_parameter_id' => $param->id,
                'pm_nilai'        => $param->nilai,
                'pm_skor'         => $param->skor,
                'pm_catatan'      => $catatan,
                'pm_evaluator_id' => session('id_satker') ?? 'satker',
            ];
        } else {
            // Simpan hasil evaluasi (Evaluator: Kejati untuk Kejari/Cabjari, JAMWAS untuk Kejati & Eselon I)
            $dataToUpdate = [
                'komponen_id'    => $komponenId,
                'subkomponen_id' => $subkomponenId,
                'parameter_id'   => $param->id,
                'nilai'          => $param->nilai,
                'skor'           => $param->skor,
                'catatan'        => $catatan,
                'evaluator_id'   => session('id_satker') ?? 'evaluator',
            ];
        }

        // Save or update evaluation
        $penilaian = LkePenilaianSatker::updateOrCreate(
            [
                'id_satker'   => $idSatker,
                'tahun'       => $tahun,
                'kriteria_id' => $kriteriaId,
            ],
            $dataToUpdate
        );

        // Auto-Reset Koefisien: Jika pemenuhan kriteria subkomponen < 100%,
        // pilihan gradasi/koefisien tinggi (> 80) otomatis di-reset ke default 80
        $autoResetKoef = false;
        if ($subkomponenId) {
            $targetTahun = LkeParameter::where('tahun', $tahun)->exists() ? $tahun : 2025;

            // Periksa apakah subkomponen ini bagian dari pasangan B & C
            $isSubB = str_ends_with($subkomponenId, '.B');
            $isSubC = str_ends_with($subkomponenId, '.C');
            $subCodesToCheck = [$subkomponenId];
            if ($isSubB || $isSubC) {
                $prefix = substr($subkomponenId, 0, strrpos($subkomponenId, '.'));
                $subCodesToCheck = ["{$prefix}.B", "{$prefix}.C"];
            }

            $subIdsToCheck = LkeSubkomponen::whereIn('kode', $subCodesToCheck)->pluck('id');
            $allSubKriteria = LkeKriteria::where(function($q) use ($subCodesToCheck, $subIdsToCheck) {
                $q->whereIn('subkomponen_id', $subIdsToCheck)
                  ->orWhereIn('subkomponen_id', $subCodesToCheck);
            })->get();
            $subEvals = LkePenilaianSatker::where('id_satker', $idSatker)
                ->where('tahun', $tahun)
                ->whereIn('kriteria_id', $allSubKriteria->pluck('kode'))
                ->get();

            $isAnySubIncomplete = false;
            foreach ($subCodesToCheck as $chkCode) {
                $chkSub = LkeSubkomponen::where('kode', $chkCode)->first();
                $chkKriteria = $allSubKriteria->filter(function($cr) use ($chkCode, $chkSub) {
                    return $cr->subkomponen_id == $chkCode || ($chkSub && $cr->subkomponen_id == $chkSub->id);
                });
                $totCrit = $chkKriteria->count();
                $terpenuhi = 0;
                foreach ($chkKriteria as $cr) {
                    $evRow = $subEvals->firstWhere('kriteria_id', $cr->kode);
                    if ($evRow) {
                        if ($tahap === 'pm' && ($evRow->pm_nilai === 'Terpenuhi' || (float)$evRow->pm_skor > 0)) {
                            $terpenuhi++;
                        } elseif ($tahap !== 'pm' && ($evRow->nilai === 'Terpenuhi' || (float)$evRow->skor > 0)) {
                            $terpenuhi++;
                        }
                    }
                }
                if ($totCrit > 0 && $terpenuhi < $totCrit) {
                    $isAnySubIncomplete = true;
                }
            }

            if ($isAnySubIncomplete) {
                $koefRecs = LkeSubkomponenKoefisien::where('id_satker', $idSatker)
                    ->where('tahun', $tahun)
                    ->whereIn('subkomponen_kode', $subCodesToCheck)
                    ->get();

                foreach ($koefRecs as $koefRec) {
                    if ($tahap === 'pm' && ($koefRec->pm_koefisien > 80 || in_array($koefRec->pm_nilai, ['A', 'AA', 'Terpenuhi', '90', '100']))) {
                        $koefRec->pm_target_koefisien = 80;
                        $koefRec->pm_koefisien = 80;
                        $koefRec->pm_nilai = null;
                        $koefRec->save();
                        $autoResetKoef = true;
                    } elseif ($tahap !== 'pm' && ($koefRec->koefisien > 80 || in_array($koefRec->nilai, ['A', 'AA', 'Terpenuhi', '90', '100']))) {
                        $koefRec->target_koefisien = 80;
                        $koefRec->koefisien = 80;
                        $koefRec->nilai = null;
                        $koefRec->save();
                        $autoResetKoef = true;
                    }
                }
            }
        }

        // Recalculate totals
        $scores = $this->calculateHierarchyScores($idSatker, $tahun, $tahap);

        $tahapLabel = ($tahap === 'pm') ? 'Penilaian Mandiri (PM)' : 'Hasil Evaluasi';

        $subScoreInfo = $scores['subkomponen'][$subkomponenId] ?? null;
        $currentKlusterGradasi = ($tahap === 'pm') ? ($subScoreInfo['gradasi_pm'] ?? 'BB') : ($subScoreInfo['gradasi_eval'] ?? 'BB');
        $currentKlusterKoef = ($tahap === 'pm') ? ($subScoreInfo['koefisien_pm'] ?? 80) : ($subScoreInfo['koefisien_evaluasi'] ?? 80);

        return response()->json([
            'success'          => true,
            'message'          => "Nilai {$tahapLabel} untuk Kriteria {$kriteriaId} berhasil disimpan!" . ($autoResetKoef ? " Gradasi subkomponen {$subkomponenId} otomatis disesuaikan ke Kluster Gradasi {$currentKlusterGradasi} ({$currentKlusterKoef}%)." : ""),
            'penilaian'        => $penilaian,
            'scores'           => $scores,
            'tahap'            => $tahap,
            'subkomponen_id'   => $subkomponenId,
            'koefisien_reset'  => $autoResetKoef,
        ]);
    }

    /**
     * AJAX endpoint to save / update subkomponen gradasi (BB 80%, A 90%, AA 100%).
     * STRICT GATE: 
     * - Untuk Subkomponen X.A: wajib seluruh kriteria X.A 100% terpenuhi.
     * - Untuk Subkomponen X.B & X.C (berpasangan): KEDUA subkomponen (X.B DAN X.C) WAJIB 100% terpenuhi.
     */
    public function saveSubkomponenKoefisien(Request $request)
    {
        $this->checkEvaluator();

        $request->validate([
            'id_satker'        => 'required|string',
            'tahun'            => 'required|integer',
            'subkomponen_kode' => 'required|string',
            'gradasi'          => 'nullable|in:BB,A,AA,80,90,100',
            'target_koefisien' => 'nullable|in:80,90,100',
            'nilai'            => 'nullable|string',
            'uraian'           => 'nullable|string',
            'bukti_dukung'     => 'nullable|string',
            'tahap'            => 'nullable|in:pm,evaluasi,pk',
            'bukti_file'       => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,zip,rar,jpg,jpeg,png',
        ]);

        $idSatker = $request->id_satker;
        $tahun = (int)$request->tahun;
        $subkomponenKode = trim($request->subkomponen_kode);
        $uraian = trim($request->uraian ?? '');
        $tahap = $request->get('tahap', 'evaluasi');
        if ($tahap === 'pk') {
            $tahap = 'evaluasi';
        }

        // Resolusi gradasi (BB: 80, A: 90, AA: 100)
        if ($request->filled('gradasi')) {
            $g = strtoupper((string)$request->gradasi);
            if (in_array($g, ['AA', '100'])) {
                $gradasi = 'AA';
                $targetKoefisien = 100;
            } elseif (in_array($g, ['A', '90'])) {
                $gradasi = 'A';
                $targetKoefisien = 90;
            } else {
                $gradasi = 'BB';
                $targetKoefisien = 80;
            }
        } elseif ($request->filled('target_koefisien') && in_array((int)$request->target_koefisien, [90, 100])) {
            $targetKoefisien = (int)$request->target_koefisien;
            $gradasi = ($targetKoefisien === 100) ? 'AA' : 'A';
        } else {
            $gradasi = 'BB';
            $targetKoefisien = 80;
        }

        // Validasi otoritas evaluator terhadap Satker yang dinilai
        if (!$this->isAuthorizedSatkerForEvaluator($idSatker, $tahap)) {
            $labelAksi = ($tahap === 'pm') ? 'Penilaian Mandiri (PM)' : 'Evaluasi';
            return response()->json([
                'success' => false,
                'message' => "Akses Ditolak: Anda tidak memiliki wewenang untuk melakukan {$labelAksi} terhadap Satuan Kerja ini.",
            ], 403);
        }

        // Deteksi apakah subkomponen yang disimpan adalah pasangan B & C
        $targetCodes = [$subkomponenKode];
        $isPair = false;
        $compPrefix = null;

        if (
            str_contains($subkomponenKode, 'BC') || 
            str_contains($subkomponenKode, ',') || 
            str_contains($subkomponenKode, '_') || 
            str_ends_with($subkomponenKode, '.B') || 
            str_ends_with($subkomponenKode, '.C')
        ) {
            if (preg_match('/^([0-9]+)/', $subkomponenKode, $m)) {
                $compPrefix = $m[1];
                $targetCodes = ["{$compPrefix}.B", "{$compPrefix}.C"];
                $isPair = true;
            }
        }

        // GERBANG 100% MUTLAK:
        // Jika berpasangan (X.B & X.C), KEDUA subkomponen harus 100% terpenuhi
        // Jika mandiri (misal X.A), subkomponen tersebut harus 100% terpenuhi
        $targetSubIds = LkeSubkomponen::whereIn('kode', $targetCodes)->pluck('id');
        $kriteriaCheck = LkeKriteria::where(function($q) use ($targetCodes, $targetSubIds) {
            $q->whereIn('subkomponen_id', $targetSubIds)
              ->orWhereIn('subkomponen_id', $targetCodes);
        })->get();
        $evalsCheck = LkePenilaianSatker::where('id_satker', $idSatker)
            ->where('tahun', $tahun)
            ->whereIn('kriteria_id', $kriteriaCheck->pluck('kode'))
            ->get();

        $incompleteMessages = [];
        foreach ($targetCodes as $sCode) {
            $tSub = LkeSubkomponen::where('kode', $sCode)->first();
            $subCrits = $kriteriaCheck->filter(function($cr) use ($sCode, $tSub) {
                return $cr->subkomponen_id == $sCode || ($tSub && $cr->subkomponen_id == $tSub->id);
            });
            $totalCnt = $subCrits->count();
            $terpCnt = 0;
            foreach ($subCrits as $cr) {
                $ev = $evalsCheck->firstWhere('kriteria_id', $cr->kode);
                if ($ev) {
                    if ($tahap === 'pm' && ($ev->pm_nilai === 'Terpenuhi' || (float)$ev->pm_skor > 0)) {
                        $terpCnt++;
                    } elseif ($tahap !== 'pm' && ($ev->nilai === 'Terpenuhi' || (float)$ev->skor > 0)) {
                        $terpCnt++;
                    }
                }
            }
            if ($totalCnt === 0 || $terpCnt < $totalCnt) {
                $incompleteMessages[] = "Subkomponen {$sCode} baru {$terpCnt}/{$totalCnt} terpenuhi";
            }
        }

        if (!empty($incompleteMessages)) {
            $detailMsg = implode(', ', $incompleteMessages);
            $ruleMsg = $isPair 
                ? "Pilihan gradasi lanjutan terkunci: {$detailMsg}. Seluruh kriteria pada Subkomponen {$compPrefix}.B dan {$compPrefix}.C wajib 100% terpenuhi."
                : "Subkomponen {$subkomponenKode} belum memenuhi seluruh kriteria ({$detailMsg}). Pilihan gradasi terkunci sampai 100% kriteria terpenuhi.";
            return response()->json([
                'success' => false,
                'message' => $ruleMsg,
            ], 422);
        }

        // Validasi Uraian: Wajib diisi untuk pilihan A atau AA
        if (in_array($gradasi, ['A', 'AA'])) {
            if (empty($uraian)) {
                return response()->json([
                    'success' => false,
                    'message' => "Untuk memilih opsi Gradasi {$gradasi} ({$targetKoefisien}%), wajib mengisi kotak Uraian / Penjelasan terlebih dahulu.",
                ], 422);
            }
        }

        // Handle File Upload (Bukti Dukung bersifat opsional)
        $hasFile = $request->hasFile('bukti_file');
        $hasTextBukti = !empty(trim($request->bukti_dukung ?? ''));
        $savedFilePath = null;
        $originalFileName = null;

        if ($hasFile) {
            $file = $request->file('bukti_file');
            $originalFileName = $file->getClientOriginalName();
            $cleanCodeName = $isPair ? "{$compPrefix}_BC" : str_replace('.', '_', $subkomponenKode);
            $cleanName = time() . '_' . $cleanCodeName . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalFileName);
            $destinationDir = public_path('uploads/lke_koefisien');
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0777, true);
            }
            $file->move($destinationDir, $cleanName);
            $savedFilePath = 'uploads/lke_koefisien/' . $cleanName;
        }

        // Simpan serentak ke semua subkomponen target (untuk pasangan B & C, keduanya diupdate)
        $savedRecords = [];
        foreach ($targetCodes as $sCode) {
            $koefRecord = LkeSubkomponenKoefisien::firstOrNew([
                'id_satker'        => $idSatker,
                'tahun'            => $tahun,
                'subkomponen_kode' => $sCode,
            ]);

            if ($tahap === 'pm') {
                $koefRecord->pm_target_koefisien = $targetKoefisien;
                $koefRecord->pm_uraian = $uraian;
                if ($savedFilePath) {
                    $koefRecord->pm_link_bukti_dukung = $savedFilePath;
                    $koefRecord->pm_bukti_dukung = $originalFileName;
                } elseif ($hasTextBukti) {
                    $koefRecord->pm_bukti_dukung = $request->bukti_dukung;
                }
                $koefRecord->pm_nilai = $gradasi;
                $koefRecord->pm_koefisien = $targetKoefisien;
                $koefRecord->pm_evaluator_id = session('id_satker') ?? 'satker';
            } else {
                $koefRecord->target_koefisien = $targetKoefisien;
                $koefRecord->uraian = $uraian;
                if ($savedFilePath) {
                    $koefRecord->link_bukti_dukung = $savedFilePath;
                    $koefRecord->bukti_dukung = $originalFileName;
                } elseif ($hasTextBukti) {
                    $koefRecord->bukti_dukung = $request->bukti_dukung;
                }
                $koefRecord->nilai = $gradasi;
                $koefRecord->koefisien = $targetKoefisien;
                $koefRecord->evaluator_id = session('id_satker') ?? 'evaluator';
            }

            $koefRecord->save();
            $savedRecords[$sCode] = $koefRecord;
        }

        // Recalculate scores hierarchically with the new coefficient
        $scores = $this->calculateHierarchyScores($idSatker, $tahun, $tahap);

        $tahapLabel = ($tahap === 'pm') ? 'Penilaian Mandiri (PM)' : 'Evaluasi';
        $statusMsg = "berhasil disimpan sebagai Gradasi {$gradasi} (Koefisien {$targetKoefisien}%)";
        $displayKode = $isPair ? "{$compPrefix}.B & {$compPrefix}.C" : $subkomponenKode;

        return response()->json([
            'success'             => true,
            'message'             => "Pengaturan Gradasi Subkomponen {$displayKode} ({$tahapLabel}) {$statusMsg}!",
            'koefisien'           => $savedRecords[reset($targetCodes)],
            'gradasi'             => $gradasi,
            'effective_koefisien' => $targetKoefisien,
            'subkomponen_kode'    => $subkomponenKode,
            'is_pair'             => $isPair,
            'pair_prefix'         => $compPrefix,
            'target_codes'        => $targetCodes,
            'scores'              => $scores,
            'tahap'               => $tahap,
        ]);
    }

    /**
     * Calculate bottom-up hierarchical scores for both Evaluasi and PM:
     * Subkomponen calculation dengan Klusterisasi Koefisien:
     * a. Hitung kriteria terpenuhi dibagi jumlah seluruh kriteria di subkomponen: P = (terpenuhi / total) * 100%
     * b. Klusterisasi P untuk menemukan koefisien dan gradasi yang tepat:
     *    - 0%           => E  (Koefisien 0%)
     *    - > 0% - 25%   => D  (Koefisien 30%)
     *    - > 25% - 50%  => C  (Koefisien 50%)
     *    - > 50% - 75%  => CC (Koefisien 60%)
     *    - > 75% - <100%=> B  (Koefisien 70%)
     *    - 100%         => BB (Koefisien 80% / Pilihan A 90% atau AA 100%)
     * c. Koefisien hasil klusterisasi dikali bobot nilai subkomponen: (Koefisien / 100) * Bobot
     * d. Hasil perhitungan c dijumlahkan untuk seluruh 12 subkomponen di LKE
     */
    public function calculateHierarchyScores($idSatker, $tahun, $preferredStage = null)
    {
        if (!$idSatker) {
            $defaultPredikat = LkeSubkomponenKoefisien::getPredikatAkip(0);
            return [
                'grand_total'           => 0,
                'grand_total_evaluasi'  => 0,
                'grand_total_pk'        => 0,
                'grand_total_pm'        => 0,
                'predikat_evaluasi'     => $defaultPredikat,
                'predikat_pk'           => $defaultPredikat,
                'predikat_pm'           => $defaultPredikat,
                'predikat_aktif'        => $defaultPredikat,
                'komponen'              => [],
                'subkomponen'           => [],
                'evaluated_cnt'         => 0,
                'evaluasi_evaluated_cnt'=> 0,
                'pk_evaluated_cnt'      => 0,
                'pm_evaluated_cnt'      => 0,
                'total_cnt'             => 0,
            ];
        }

        // Fetch all evaluations for this satker
        $evals = LkePenilaianSatker::where('id_satker', $idSatker)
            ->where('tahun', $tahun)
            ->get();

        $evalMap = $evals->keyBy('kriteria_id');

        // Fetch subkomponen koefisien for this satker
        $koefMap = LkeSubkomponenKoefisien::where('id_satker', $idSatker)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('subkomponen_kode');

        $targetTahun = LkeKomponen::where('tahun', $tahun)->exists() ? $tahun : 2025;
        $komponenList = LkeKomponen::where('tahun', $targetTahun)->orderBy('no')->get();
        $subkomponenList = LkeSubkomponen::where('tahun', $targetTahun)->orderBy('id')->get();
        $allKriteria = LkeKriteria::where('tahun', $targetTahun)->with('subkomponen')->orderBy('id')->get();

        $subkomponenScores = [];
        $komponenScores = [];
        $grandTotalPk = 0;
        $grandTotalPm = 0;

        // Group kriteria by subkomponen (supporting both numeric ID and string kode)
        $kriteriaBySubId = $allKriteria->groupBy('subkomponen_id');
        $kriteriaBySubKode = $allKriteria->groupBy(function($c) {
            return $c->subkomponen ? $c->subkomponen->kode : $c->subkomponen_id;
        });

        $totalPkEvaluated = 0;
        $totalPmEvaluated = 0;

        $defaultBobotMap = [
            '1.A' => 6.00, '1.B' => 9.00, '1.C' => 15.00,
            '2.A' => 6.00, '2.B' => 9.00, '2.C' => 15.00,
            '3.A' => 3.00, '3.B' => 4.50, '3.C' => 7.50,
            '4.A' => 5.00, '4.B' => 7.50, '4.C' => 12.50,
        ];

        // Pass 1: Kumpulkan statistik kriteria terpenuhi dan kelengkapan dasar untuk setiap subkomponen
        $subStats = [];
        foreach ($subkomponenList as $sub) {
            $crits = $kriteriaBySubKode->get($sub->kode)
                ?? $kriteriaBySubId->get($sub->id)
                ?? $kriteriaBySubId->get($sub->kode, collect());
            $totCnt = $crits->count();
            $terpEval = 0;
            $terpPm = 0;
            $subPkEval = 0;
            $subPmEval = 0;

            foreach ($crits as $cr) {
                if (isset($evalMap[$cr->kode])) {
                    $ev = $evalMap[$cr->kode];
                    if (!is_null($ev->parameter_id)) {
                        $subPkEval++;
                        if ($ev->nilai === 'Terpenuhi' || (float)$ev->skor > 0) {
                            $terpEval++;
                        }
                    }
                    if (!is_null($ev->pm_parameter_id)) {
                        $subPmEval++;
                        if ($ev->pm_nilai === 'Terpenuhi' || (float)$ev->pm_skor > 0) {
                            $terpPm++;
                        }
                    }
                }
            }

            $is100Eval = ($totCnt > 0 && $terpEval === $totCnt);
            $is100Pm   = ($totCnt > 0 && $terpPm === $totCnt);

            $subStats[$sub->kode] = [
                'total_cnt'        => $totCnt,
                'terpenuhi_eval'   => $terpEval,
                'terpenuhi_pm'     => $terpPm,
                'evaluated_eval'   => $subPkEval,
                'evaluated_pm'     => $subPmEval,
                'is_100_eval'      => $is100Eval,
                'is_100_pm'        => $is100Pm,
            ];
        }

        // Hitung kelengkapan pasangan B & C untuk setiap komponen (1, 2, 3, 4)
        $pairGateStatus = [];
        $compIds = $subkomponenList->pluck('kode')->map(function($k) {
            return explode('.', $k)[0];
        })->unique();

        foreach ($compIds as $pfx) {
            $bStats = $subStats["{$pfx}.B"] ?? null;
            $cStats = $subStats["{$pfx}.C"] ?? null;

            $pairGateStatus[$pfx] = [
                'prefix'                => $pfx,
                'is_pair_complete_eval' => ($bStats && $cStats && $bStats['is_100_eval'] && $cStats['is_100_eval']),
                'is_pair_complete_pm'   => ($bStats && $cStats && $bStats['is_100_pm'] && $cStats['is_100_pm']),
                'b_terpenuhi_eval'      => $bStats['terpenuhi_eval'] ?? 0,
                'b_total'               => $bStats['total_cnt'] ?? 0,
                'c_terpenuhi_eval'      => $cStats['terpenuhi_eval'] ?? 0,
                'c_total'               => $cStats['total_cnt'] ?? 0,
                'b_terpenuhi_pm'        => $bStats['terpenuhi_pm'] ?? 0,
                'c_terpenuhi_pm'        => $cStats['terpenuhi_pm'] ?? 0,
            ];
        }

        foreach ($subkomponenList as $sub) {
            $crits = $kriteriaBySubKode->get($sub->kode)
                ?? $kriteriaBySubId->get($sub->id)
                ?? $kriteriaBySubId->get($sub->kode, collect());
            $subBobot = (float)($sub->bobot ?: ($defaultBobotMap[$sub->kode] ?? 0));
            $stats = $subStats[$sub->kode] ?? [
                'total_cnt' => 0, 'terpenuhi_eval' => 0, 'terpenuhi_pm' => 0,
                'evaluated_eval' => 0, 'evaluated_pm' => 0, 'is_100_eval' => false, 'is_100_pm' => false
            ];

            $totalKriteriaCount = $stats['total_cnt'];
            $terpenuhiEvalCount = $stats['terpenuhi_eval'];
            $terpenuhiPmCount   = $stats['terpenuhi_pm'];
            $subPkEvaluated     = $stats['evaluated_eval'];
            $subPmEvaluated     = $stats['evaluated_pm'];

            $totalPkEvaluated += $subPkEvaluated;
            $totalPmEvaluated += $subPmEvaluated;

            // Klusterisasi pemenuhan kriteria masing-masing:
            $clusterEval = LkeSubkomponenKoefisien::clusterPemenuhan($terpenuhiEvalCount, $totalKriteriaCount);
            $clusterPm   = LkeSubkomponenKoefisien::clusterPemenuhan($terpenuhiPmCount, $totalKriteriaCount);

            $isCompleteEval = $stats['is_100_eval'];
            $isCompletePm   = $stats['is_100_pm'];

            // Deteksi pasangan B & C
            $isSubB = str_ends_with($sub->kode, '.B');
            $isSubC = str_ends_with($sub->kode, '.C');
            $isPair = ($isSubB || $isSubC);
            $pfx = explode('.', $sub->kode)[0];
            $isPairCompleteEval = $isPair ? ($pairGateStatus[$pfx]['is_pair_complete_eval'] ?? false) : $isCompleteEval;
            $isPairCompletePm   = $isPair ? ($pairGateStatus[$pfx]['is_pair_complete_pm'] ?? false) : $isCompletePm;

            // Ambil record koefisien (jika pasangan, record di C atau B saling berbagi gradasi yang sama)
            $koefRecord = $koefMap->get($sub->kode);
            $pairKoefRecord = $isPair ? ($koefMap->get("{$pfx}.C") ?: $koefMap->get("{$pfx}.B")) : $koefRecord;

            // Penentuan koefisien Evaluasi:
            // Jika berpasangan: koefisien lanjutan (>80) HANYA diizinkan jika KEDUA subkomponen (B & C) 100% terpenuhi
            if ($isPair) {
                if ($isPairCompleteEval) {
                    if ($pairKoefRecord && in_array((int)$pairKoefRecord->effective_koefisien, [80, 90, 100])) {
                        $koefisienEval = (int)$pairKoefRecord->effective_koefisien;
                        $gradasiEval   = $pairKoefRecord->gradasi_label;
                    } else {
                        $koefisienEval = 80;
                        $gradasiEval   = 'BB';
                    }
                } else {
                    if ($isCompleteEval) {
                        $koefisienEval = 80;
                        $gradasiEval   = 'BB';
                    } else {
                        $koefisienEval = (int)$clusterEval['koefisien'];
                        $gradasiEval   = $clusterEval['gradasi'];
                    }
                }
            } else {
                if ($isCompleteEval) {
                    if ($koefRecord && in_array((int)$koefRecord->effective_koefisien, [80, 90, 100])) {
                        $koefisienEval = (int)$koefRecord->effective_koefisien;
                        $gradasiEval   = $koefRecord->gradasi_label;
                    } else {
                        $koefisienEval = 80;
                        $gradasiEval   = 'BB';
                    }
                } else {
                    $koefisienEval = (int)$clusterEval['koefisien'];
                    $gradasiEval   = $clusterEval['gradasi'];
                }
            }

            // Penentuan koefisien PM:
            if ($isPair) {
                if ($isPairCompletePm) {
                    if ($pairKoefRecord && in_array((int)$pairKoefRecord->effective_pm_koefisien, [80, 90, 100])) {
                        $koefisienPm = (int)$pairKoefRecord->effective_pm_koefisien;
                        $gradasiPm   = $pairKoefRecord->pm_gradasi_label;
                    } else {
                        $koefisienPm = 80;
                        $gradasiPm   = 'BB';
                    }
                } else {
                    if ($isCompletePm) {
                        $koefisienPm = 80;
                        $gradasiPm   = 'BB';
                    } else {
                        $koefisienPm = (int)$clusterPm['koefisien'];
                        $gradasiPm   = $clusterPm['gradasi'];
                    }
                }
            } else {
                if ($isCompletePm) {
                    if ($koefRecord && in_array((int)$koefRecord->effective_pm_koefisien, [80, 90, 100])) {
                        $koefisienPm = (int)$koefRecord->effective_pm_koefisien;
                        $gradasiPm   = $koefRecord->pm_gradasi_label;
                    } else {
                        $koefisienPm = 80;
                        $gradasiPm   = 'BB';
                    }
                } else {
                    $koefisienPm = (int)$clusterPm['koefisien'];
                    $gradasiPm   = $clusterPm['gradasi'];
                }
            }

            // Langkah hitung nilai subkomponen:
            // Formula: (Koefisien / 100) * Bobot
            $subPkScore = round(($koefisienEval / 100) * $subBobot, 2);
            $subPmScore = round(($koefisienPm / 100) * $subBobot, 2);

            $evalPersen = (float)$koefisienEval;
            $pmPersen   = (float)$koefisienPm;

            $subkomponenScores[$sub->kode] = [
                'nama'                   => $sub->nama,
                'komponen_id'            => $sub->komponen_id,
                'bobot'                  => $subBobot,
                'koefisien_evaluasi'     => $koefisienEval,
                'koefisien_pm'           => $koefisienPm,
                'gradasi_eval'           => $gradasiEval,
                'gradasi_pm'             => $gradasiPm,
                'cluster_eval'           => $clusterEval,
                'cluster_pm'             => $clusterPm,
                'badge_eval'             => LkeSubkomponenKoefisien::getGradasiBadgeClass($gradasiEval),
                'badge_pm'               => LkeSubkomponenKoefisien::getGradasiBadgeClass($gradasiPm),
                'skor'                   => $subPkScore,
                'evaluasi_skor'          => $subPkScore,
                'pk_skor'                => $subPkScore,
                'pm_skor'                => $subPmScore,
                'max_skor'               => $subBobot,
                'persentase'             => $evalPersen,
                'evaluasi_persentase'    => $evalPersen,
                'pk_persentase'          => $evalPersen,
                'pm_persentase'          => $pmPersen,
                'terpenuhi_eval_cnt'     => $terpenuhiEvalCount,
                'terpenuhi_pm_cnt'       => $terpenuhiPmCount,
                'pemenuhan_eval_persen'  => $clusterEval['persen'],
                'pemenuhan_pm_persen'    => $clusterPm['persen'],
                'evaluated_cnt'          => $subPkEvaluated,
                'evaluasi_evaluated_cnt' => $subPkEvaluated,
                'pk_evaluated_cnt'       => $subPkEvaluated,
                'pm_evaluated_cnt'       => $subPmEvaluated,
                'total_cnt'              => $totalKriteriaCount,
                'is_100_eval'            => $isCompleteEval,
                'is_100_pm'              => $isCompletePm,
                'is_pair'                => $isPair,
                'pair_prefix'            => $isPair ? $pfx : null,
                'is_pair_complete_eval'  => $isPairCompleteEval,
                'is_pair_complete_pm'    => $isPairCompletePm,
                'has_explicit_eval'      => $isPair ? ($isPairCompleteEval && $pairKoefRecord && !empty($pairKoefRecord->nilai)) : ($isCompleteEval && $koefRecord && !empty($koefRecord->nilai)),
                'has_explicit_pm'        => $isPair ? ($isPairCompletePm && $pairKoefRecord && !empty($pairKoefRecord->pm_nilai)) : ($isCompletePm && $koefRecord && !empty($koefRecord->pm_nilai)),
                'eval_needs_gradasi'     => $isPair ? ($isPairCompleteEval && (!$pairKoefRecord || empty($pairKoefRecord->nilai))) : ($isCompleteEval && (!$koefRecord || empty($koefRecord->nilai))),
                'pm_needs_gradasi'       => $isPair ? ($isPairCompletePm && (!$pairKoefRecord || empty($pairKoefRecord->pm_nilai))) : ($isCompletePm && (!$koefRecord || empty($koefRecord->pm_nilai))),
            ];
        }

        // d. Aggregate ke Komponen level (jumlah dari seluruh subkomponen)
        foreach ($komponenList as $komp) {
            $kPkScore = 0;
            $kPmScore = 0;
            $kMax = 0;
            $kPkEvaluated = 0;
            $kPmEvaluated = 0;
            $kTotal = 0;

            foreach ($subkomponenScores as $subKode => $subData) {
                if ($subData['komponen_id'] == $komp->id) {
                    $kPkScore += $subData['evaluasi_skor'];
                    $kPmScore += $subData['pm_skor'];
                    $kMax += $subData['bobot'];
                    $kPkEvaluated += $subData['evaluasi_evaluated_cnt'];
                    $kPmEvaluated += $subData['pm_evaluated_cnt'];
                    $kTotal += $subData['total_cnt'];
                }
            }

            $komponenScores[$komp->id] = [
                'id'                    => $komp->id,
                'no'                    => $komp->no,
                'nama'                  => $komp->nama,
                'skor'                  => round($kPkScore, 2),
                'evaluasi_skor'         => round($kPkScore, 2),
                'pk_skor'               => round($kPkScore, 2),
                'pm_skor'               => round($kPmScore, 2),
                'max_skor'              => round($kMax, 2),
                'persentase'            => $kMax > 0 ? round(($kPkScore / $kMax) * 100, 1) : 0,
                'evaluasi_persentase'   => $kMax > 0 ? round(($kPkScore / $kMax) * 100, 1) : 0,
                'pk_persentase'         => $kMax > 0 ? round(($kPkScore / $kMax) * 100, 1) : 0,
                'pm_persentase'         => $kMax > 0 ? round(($kPmScore / $kMax) * 100, 1) : 0,
                'evaluated_cnt'         => $kPkEvaluated,
                'evaluasi_evaluated_cnt'=> $kPkEvaluated,
                'pk_evaluated_cnt'      => $kPkEvaluated,
                'pm_evaluated_cnt'      => $kPmEvaluated,
                'total_cnt'             => $kTotal,
            ];

            $grandTotalPk += $kPkScore;
            $grandTotalPm += $kPmScore;
        }

        $isPm = ($preferredStage === 'pm');

        $roundedGrandTotalPk = round($grandTotalPk, 2);
        $roundedGrandTotalPm = round($grandTotalPm, 2);
        $predikatPk = LkeSubkomponenKoefisien::getPredikatAkip($roundedGrandTotalPk);
        $predikatPm = LkeSubkomponenKoefisien::getPredikatAkip($roundedGrandTotalPm);

        // d. Hasil penghitungan dijumlahkan untuk seluruh subkomponen (total 12 subkomponen)
        return [
            'grand_total'           => $isPm ? $roundedGrandTotalPm : $roundedGrandTotalPk,
            'grand_total_evaluasi'  => $roundedGrandTotalPk,
            'grand_total_pk'        => $roundedGrandTotalPk,
            'grand_total_pm'        => $roundedGrandTotalPm,
            'predikat_evaluasi'     => $predikatPk,
            'predikat_pk'           => $predikatPk,
            'predikat_pm'           => $predikatPm,
            'predikat_aktif'        => $isPm ? $predikatPm : $predikatPk,
            'komponen'              => $komponenScores,
            'subkomponen'           => $subkomponenScores,
            'pairs'                 => $pairGateStatus,
            'evaluated_cnt'         => $isPm ? $totalPmEvaluated : $totalPkEvaluated,
            'evaluasi_evaluated_cnt'=> $totalPkEvaluated,
            'pk_evaluated_cnt'      => $totalPkEvaluated,
            'pm_evaluated_cnt'      => $totalPmEvaluated,
            'total_cnt'             => $allKriteria->count(),
        ];
    }
}
