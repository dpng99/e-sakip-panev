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
    ];

    /**
     * Check if a target satker can ONLY be evaluated by JAMWAS:
     * - Seluruh Kejaksaan Tinggi (id_sakip_level == 2)
     * - Unit Eselon I / Pusat: JAMBIN, JAMINTEL, JAMDATUN, JAMPIDUM, JAMPIDSUS, JAMPIDMIL, BPA, BADIKLAT
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
            str_contains($targetNama, 'badan_pendidikan')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine evaluator type:
     * - 'admin': Administrator Pusat
     * - 'jamwas': Tim Pengawasan Pusat (JAMWAS)
     * - 'kejati': Kejaksaan Tinggi (Level 2 - PM mandiri & Evaluasi Kejari/Cabjari)
     * - 'pm': Satuan Kerja (Kejari, Cabjari, Unit Pusat)
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

        // 2. JAMWAS (Jaksa Agung Muda Bidang Pengawasan)
        $isJamwas = ($satker === '419346')
            || in_array($satker, ['888882', 'Pengawasan', 'Panev'])
            || str_contains($nama, 'jam_bidang_pengawasan')
            || str_contains($nama, 'jamwas')
            || (str_contains($nama, 'pengawasan') && !str_contains($nama, 'kejati') && !str_starts_with(strtolower($satker), 'was'));

        if ($isJamwas) {
            return 'jamwas';
        }

        // 3. Kejaksaan Tinggi (id_sakip_level == 2)
        if ($level == 2 || $level == '2' || str_contains($nama, 'kejati')) {
            return 'kejati';
        }

        // 4. PM (Penilaian Mandiri) Satker - Kejaksaan Negeri, Cabang Kejaksaan Negeri, dan Unit Pusat
        if (
            in_array($level, [1, 3, 4, '1', '3', '4'])
            || str_contains($nama, 'kejari')
            || str_contains($nama, 'cabjari')
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

        // 1. Jika tahap Penilaian Mandiri (PM): Setiap satker berhak mengisi PM untuk dirinya sendiri
        if ($tahap === 'pm') {
            return ((string)$target->id_satker === (string)$currentUser->id_satker);
        }

        // 2. Jika tahap Evaluasi:
        // Admin Pusat berwenang mengevaluasi seluruh satker
        if ($evaluatorType === 'admin') {
            return true;
        }

        // JAMWAS berwenang mengevaluasi KEJATI dan Unit Eselon I (JAMBIN, JAMINTEL, JAMDATUN, JAMPIDUM, JAMPIDSUS, JAMPIDMIL, BPA, BADIKLAT)
        if ($evaluatorType === 'jamwas') {
            return $this->isOnlyEvaluatedByJamwas($target);
        }

        // Kejaksaan Tinggi (Level 2) berwenang melakukan EVALUASI terhadap Kejaksaan Negeri (3) dan Cabang Kejaksaan Negeri (4) di wilayah hukum Kejati
        if ($evaluatorType === 'kejati' || (int)$currentUser->id_sakip_level === 2) {
            $targetLevel = (int)$target->id_sakip_level;

            // Kejati TIDAK BISA mengevaluasi diri sendiri atau sesama Kejati atau Unit Eselon I (karena hanya bisa dievaluasi oleh JAMWAS)
            if ($this->isOnlyEvaluatedByJamwas($target)) {
                return false;
            }

            // Kejati mengevaluasi Kejari (3) dan Cabjari (4) dalam wilayah Kejati miliknya
            if (in_array($targetLevel, [3, 4])) {
                return (!empty($currentUser->id_kejati) && (string)$target->id_kejati === (string)$currentUser->id_kejati);
            }

            return false;
        }

        // Satker Kejari & Cabjari hanya mengisi PM, tidak berwenang mengevaluasi satker lain
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
            // JAMWAS: Menilai Unit Eselon I (JAMBIN, JAMINTEL, dll) dan Seluruh KEJATI (Level 2)
            $satkerList = DB::table('sinori_login')
                ->where(function ($q) {
                    $q->where('id_sakip_level', 2)
                      ->orWhere(function ($sub) {
                          $sub->where('id_sakip_level', 1)
                              ->whereIn('id_satker', $this->centralUnitSatkerIds);
                      });
                })
                ->where('id_satker', 'not like', 'was%')
                ->whereNotIn('id_satker', ['admin', '999999', '888881', '888882', 'Pengawasan', 'Panev', '419346'])
                ->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->orderBy('id_sakip_level', 'asc')
                ->orderBy('satkernama', 'asc')
                ->get();
        } elseif ($evaluatorType === 'kejati' || $userLevel === 2) {
            // Kejaksaan Tinggi: tampilkan Kejati sendiri (PM) dan Kejari & Cabjari di wilayahnya (Evaluasi)
            $kejatiId = $currentUser ? $currentUser->id_kejati : null;

            $query = DB::table('sinori_login')
                ->where(function ($q) use ($kejatiId, $userSatkerId) {
                    if ($kejatiId) {
                        $q->where('id_kejati', $kejatiId);
                    } else {
                        $q->where('id_satker', $userSatkerId);
                    }
                })
                ->whereIn('id_sakip_level', [2, 3, 4])
                ->where('id_satker', 'not like', 'was%')
                ->whereNotIn('id_satker', ['admin', '999999', '888881', '888882', 'Pengawasan', 'Panev']);

            $satkerList = $query->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->orderBy('id_sakip_level', 'asc')
                ->orderBy('satkernama', 'asc')
                ->get();
        } elseif ($userLevel === 3) {
            // Kejaksaan Negeri: diri sendiri dan Cabjari di bawahnya (jika ada)
            $kejatiId = $currentUser ? $currentUser->id_kejati : null;
            $query = DB::table('sinori_login')
                ->where(function ($q) use ($userSatkerId, $currentUser, $kejatiId) {
                    $q->where('id_satker', $userSatkerId);
                    if ($kejatiId && isset($currentUser->id_kejari)) {
                        $q->orWhere(function ($sub) use ($kejatiId, $currentUser) {
                            $sub->where('id_kejati', $kejatiId)
                                ->where('id_sakip_level', 4)
                                ->where('id_kejari', $currentUser->id_kejari);
                        });
                    }
                })
                ->where('id_satker', 'not like', 'was%');

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
            // Cabang Kejaksaan Negeri atau Unit Pusat Mandiri: dirinya sendiri
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
        } elseif ($evaluatorType === 'kejati' || $userLevel === 2) {
            // Kejati: jika membuka Kejati sendiri -> mode PM; jika membuka Kejari/Cabjari -> mode Evaluasi!
            if ($selectedId === $userSatkerId) {
                $activeMode = 'pm';
            } else {
                $activeMode = 'evaluasi';
            }
        } else {
            // Kejari, Cabjari, dan Unit Pusat lainnya: selalu mode PM
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
            ->orderBy('id')
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

        $kriteria = LkeKriteria::where('kode', $kriteriaId)->first();

        $subkomponenId = $kriteria ? $kriteria->subkomponen_id : null;
        $komponenId = null;
        if ($subkomponenId) {
            $sub = LkeSubkomponen::where('kode', $subkomponenId)->first();
            $komponenId = $sub ? $sub->komponen_id : null;
        }

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
            $targetTahun = LkeKomponen::where('tahun', $tahun)->exists() ? $tahun : 2025;
            $allSubKriteria = LkeKriteria::where('tahun', $targetTahun)->where('subkomponen_id', $subkomponenId)->get();
            $totalSubCrit = $allSubKriteria->count();

            $subEvals = LkePenilaianSatker::where('id_satker', $idSatker)
                ->where('tahun', $tahun)
                ->whereIn('kriteria_id', $allSubKriteria->pluck('kode'))
                ->get();

            $subTerpenuhi = 0;
            foreach ($allSubKriteria as $cr) {
                $evRow = $subEvals->firstWhere('kriteria_id', $cr->kode);
                if ($evRow) {
                    if ($tahap === 'pm' && ($evRow->pm_nilai === 'Terpenuhi' || (float)$evRow->pm_skor > 0)) {
                        $subTerpenuhi++;
                    } elseif ($tahap !== 'pm' && ($evRow->nilai === 'Terpenuhi' || (float)$evRow->skor > 0)) {
                        $subTerpenuhi++;
                    }
                }
            }

            if ($totalSubCrit > 0 && $subTerpenuhi < $totalSubCrit) {
                $koefRec = LkeSubkomponenKoefisien::where('id_satker', $idSatker)
                    ->where('tahun', $tahun)
                    ->where('subkomponen_kode', $subkomponenId)
                    ->first();

                if ($koefRec) {
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

        return response()->json([
            'success'          => true,
            'message'          => "Nilai {$tahapLabel} untuk Kriteria {$kriteriaId} berhasil disimpan!" . ($autoResetKoef ? " Gradasi subkomponen {$subkomponenId} otomatis di-reset ke default 80% karena kriteria belum 100%." : ""),
            'penilaian'        => $penilaian,
            'scores'           => $scores,
            'tahap'            => $tahap,
            'subkomponen_id'   => $subkomponenId,
            'koefisien_reset'  => $autoResetKoef,
        ]);
    }

    /**
     * AJAX endpoint to save / update subkomponen gradasi (BB 80%, A 90%, AA 100%).
     * STRICT GATE: Only allowed if all criteria in this subkomponen are 100% fulfilled.
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
        $subkomponenKode = $request->subkomponen_kode;
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

        // GERBANG 100% MUTLAK: Seluruh kriteria subkomponen WAJIB 100% Terpenuhi
        $targetTahun = LkeKomponen::where('tahun', $tahun)->exists() ? $tahun : 2025;
        $kriteriaSub = LkeKriteria::where('tahun', $targetTahun)->where('subkomponen_id', $subkomponenKode)->get();
        $totalKriteriaSub = $kriteriaSub->count();

        $evalsSub = LkePenilaianSatker::where('id_satker', $idSatker)
            ->where('tahun', $tahun)
            ->whereIn('kriteria_id', $kriteriaSub->pluck('kode'))
            ->get();

        $terpenuhiSubCount = 0;
        foreach ($kriteriaSub as $cr) {
            $ev = $evalsSub->firstWhere('kriteria_id', $cr->kode);
            if ($ev) {
                if ($tahap === 'pm' && ($ev->pm_nilai === 'Terpenuhi' || (float)$ev->pm_skor > 0)) {
                    $terpenuhiSubCount++;
                } elseif ($tahap !== 'pm' && ($ev->nilai === 'Terpenuhi' || (float)$ev->skor > 0)) {
                    $terpenuhiSubCount++;
                }
            }
        }

        if ($totalKriteriaSub === 0 || $terpenuhiSubCount < $totalKriteriaSub) {
            return response()->json([
                'success' => false,
                'message' => "Subkomponen {$subkomponenKode} belum memenuhi seluruh kriteria (baru {$terpenuhiSubCount} dari {$totalKriteriaSub} terpenuhi). Pilihan gradasi terkunci sampai 100% kriteria terpenuhi.",
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
            $cleanName = time() . '_' . str_replace('.', '_', $subkomponenKode) . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalFileName);
            $destinationDir = public_path('uploads/lke_koefisien');
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0777, true);
            }
            $file->move($destinationDir, $cleanName);
            $savedFilePath = 'uploads/lke_koefisien/' . $cleanName;
        }

        $koefRecord = LkeSubkomponenKoefisien::firstOrNew([
            'id_satker'        => $idSatker,
            'tahun'            => $tahun,
            'subkomponen_kode' => $subkomponenKode,
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

        // Recalculate scores hierarchically with the new coefficient
        $scores = $this->calculateHierarchyScores($idSatker, $tahun, $tahap);

        $tahapLabel = ($tahap === 'pm') ? 'Penilaian Mandiri (PM)' : 'Evaluasi';
        $statusMsg = "berhasil disimpan sebagai Gradasi {$gradasi} (Koefisien {$targetKoefisien}%)";

        return response()->json([
            'success'             => true,
            'message'             => "Pengaturan Gradasi Subkomponen {$subkomponenKode} ({$tahapLabel}) {$statusMsg}!",
            'koefisien'           => $koefRecord,
            'gradasi'             => $gradasi,
            'effective_koefisien' => $targetKoefisien,
            'subkomponen_kode'    => $subkomponenKode,
            'scores'              => $scores,
            'tahap'               => $tahap,
        ]);
    }

    /**
     * Calculate bottom-up hierarchical scores for both Evaluasi and PM:
     * Subkomponen calculation:
     * a. Jumlah kriteria terpenuhi dibagi jumlah seluruh kriteria di subkomponen
     * b. Hasil a dikali koefisien defaultnya 80 (atau berubah menjadi 90/100 jika kriteria 100% dan section gradasi disetujui)
     * c. Hasil a dikali koefisien baru dikali bobot nilai di setiap subkomponen
     * d. Hasil perhitungan c dijumlahkan untuk seluruh 12 subkomponen di LKE
     */
    public function calculateHierarchyScores($idSatker, $tahun, $preferredStage = null)
    {
        if (!$idSatker) {
            return [
                'grand_total'           => 0,
                'grand_total_evaluasi'  => 0,
                'grand_total_pk'        => 0,
                'grand_total_pm'        => 0,
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
        $komponenList = LkeKomponen::where('tahun', $targetTahun)->orderBy('id')->get();
        $subkomponenList = LkeSubkomponen::where('tahun', $targetTahun)->orderBy('id')->get();
        $allKriteria = LkeKriteria::where('tahun', $targetTahun)->orderBy('id')->get();

        $subkomponenScores = [];
        $komponenScores = [];
        $grandTotalPk = 0;
        $grandTotalPm = 0;

        // Group kriteria by subkomponen_id
        $kriteriaBySub = $allKriteria->groupBy('subkomponen_id');

        $totalPkEvaluated = 0;
        $totalPmEvaluated = 0;

        $defaultBobotMap = [
            '1.A' => 6.00, '1.B' => 9.00, '1.C' => 15.00,
            '2.A' => 6.00, '2.B' => 9.00, '2.C' => 15.00,
            '3.A' => 3.00, '3.B' => 4.50, '3.C' => 7.50,
            '4.A' => 5.00, '4.B' => 7.50, '4.C' => 12.50,
        ];

        foreach ($subkomponenList as $sub) {
            $crits = $kriteriaBySub->get($sub->kode, collect());
            $subBobot = (float)($sub->bobot ?: ($defaultBobotMap[$sub->kode] ?? 0));
            $totalKriteriaCount = $crits->count();

            // Hitung kriteria terpenuhi dan kriteria yang sudah dievaluasi
            $terpenuhiEvalCount = 0;
            $terpenuhiPmCount = 0;
            $subPkEvaluated = 0;
            $subPmEvaluated = 0;

            foreach ($crits as $cr) {
                if (isset($evalMap[$cr->kode])) {
                    $ev = $evalMap[$cr->kode];
                    if (!is_null($ev->parameter_id)) {
                        $subPkEvaluated++;
                        if ($ev->nilai === 'Terpenuhi' || (float)$ev->skor > 0) {
                            $terpenuhiEvalCount++;
                        }
                    }
                    if (!is_null($ev->pm_parameter_id)) {
                        $subPmEvaluated++;
                        if ($ev->pm_nilai === 'Terpenuhi' || (float)$ev->pm_skor > 0) {
                            $terpenuhiPmCount++;
                        }
                    }
                }
            }

            $totalPkEvaluated += $subPkEvaluated;
            $totalPmEvaluated += $subPmEvaluated;

            // Strict Gate 100%: koefisien > 80 HANYA diizinkan jika seluruh kriteria telah terpenuhi
            $isCompleteEval = ($totalKriteriaCount > 0 && $terpenuhiEvalCount === $totalKriteriaCount);
            $isCompletePm   = ($totalKriteriaCount > 0 && $terpenuhiPmCount === $totalKriteriaCount);

            $koefRecord = $koefMap->get($sub->kode);

            // Koefisien subkomponen: default 80
            // Jika 100% terpenuhi, koefisien mengambil nilai gradasi (80, 90, atau 100)
            if ($isCompleteEval && $koefRecord) {
                $koefisienEval = (int)$koefRecord->effective_koefisien;
            } else {
                $koefisienEval = 80;
            }

            if ($isCompletePm && $koefRecord) {
                $koefisienPm = (int)$koefRecord->effective_pm_koefisien;
            } else {
                $koefisienPm = 80;
            }

            // a. Jumlah kriteria terpenuhi dibagi jumlah seluruh kriteria di subkomponen
            $rasioEval = $totalKriteriaCount > 0 ? ($terpenuhiEvalCount / $totalKriteriaCount) : 0;
            $rasioPm = $totalKriteriaCount > 0 ? ($terpenuhiPmCount / $totalKriteriaCount) : 0;

            // b & c. Hasil a dikali koefisien (default 80 atau 90/100 jika terpenuhi) baru dikali bobot nilai
            // Formula: rasio * (koefisien / 100) * bobot
            $subPkScore = round($rasioEval * ($koefisienEval / 100) * $subBobot, 2);
            $subPmScore = round($rasioPm * ($koefisienPm / 100) * $subBobot, 2);

            $evalPersen = round($rasioEval * $koefisienEval, 1);
            $pmPersen = round($rasioPm * $koefisienPm, 1);

            $subkomponenScores[$sub->kode] = [
                'nama'                  => $sub->nama,
                'komponen_id'           => $sub->komponen_id,
                'bobot'                 => $subBobot,
                'koefisien_evaluasi'    => $koefisienEval,
                'koefisien_pm'          => $koefisienPm,
                'skor'                  => $subPkScore,
                'evaluasi_skor'         => $subPkScore,
                'pk_skor'               => $subPkScore,
                'pm_skor'               => $subPmScore,
                'max_skor'              => $subBobot,
                'persentase'            => $evalPersen,
                'evaluasi_persentase'   => $evalPersen,
                'pk_persentase'         => $evalPersen,
                'pm_persentase'         => $pmPersen,
                'terpenuhi_eval_cnt'    => $terpenuhiEvalCount,
                'terpenuhi_pm_cnt'      => $terpenuhiPmCount,
                'evaluated_cnt'         => $subPkEvaluated,
                'evaluasi_evaluated_cnt'=> $subPkEvaluated,
                'pk_evaluated_cnt'      => $subPkEvaluated,
                'pm_evaluated_cnt'      => $subPmEvaluated,
                'total_cnt'             => $totalKriteriaCount,
                'is_100_eval'           => $isCompleteEval,
                'is_100_pm'             => $isCompletePm,
                'gradasi_eval'          => ($isCompleteEval && $koefRecord) ? $koefRecord->gradasi_label : 'BB',
                'gradasi_pm'            => ($isCompletePm && $koefRecord) ? $koefRecord->pm_gradasi_label : 'BB',
                'has_explicit_eval'     => $isCompleteEval && $koefRecord && !empty($koefRecord->nilai),
                'has_explicit_pm'       => $isCompletePm && $koefRecord && !empty($koefRecord->pm_nilai),
                'eval_needs_gradasi'    => $isCompleteEval && (!$koefRecord || empty($koefRecord->nilai)),
                'pm_needs_gradasi'      => $isCompletePm && (!$koefRecord || empty($koefRecord->pm_nilai)),
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

        // d. Hasil penghitungan dijumlahkan untuk seluruh subkomponen (total 12 subkomponen)
        return [
            'grand_total'           => round($isPm ? $grandTotalPm : $grandTotalPk, 2),
            'grand_total_evaluasi'  => round($grandTotalPk, 2),
            'grand_total_pk'        => round($grandTotalPk, 2),
            'grand_total_pm'        => round($grandTotalPm, 2),
            'komponen'              => $komponenScores,
            'subkomponen'           => $subkomponenScores,
            'evaluated_cnt'         => $isPm ? $totalPmEvaluated : $totalPkEvaluated,
            'evaluasi_evaluated_cnt'=> $totalPkEvaluated,
            'pk_evaluated_cnt'      => $totalPkEvaluated,
            'pm_evaluated_cnt'      => $totalPmEvaluated,
            'total_cnt'             => $allKriteria->count(),
        ];
    }
}
