@extends('layouts.app')

@section('content')
@php
    $isModeEvaluasi = ($activeMode === 'evaluasi');
    $isModePm = ($activeMode === 'pm');
    $currentTahap = $isModePm ? 'pm' : 'evaluasi';

    // Unit Eselon I / Pusat yang hanya dapat dievaluasi oleh JAMWAS
    $centralIds = ['005016', '419345', '417023', '418326', '419344', '677111', '691270', '666405'];

    // Menentukan siapa pihak yang berwenang mengevaluasi target Satker yang sedang dibuka:
    // KEJATI (Level 2) dan Unit Eselon I HANYA BISA dievaluasi oleh JAMWAS
    // KEJARI (Level 3) dan CABJARI (Level 4) dievaluasi oleh Kejaksaan Tinggi
    $isEvaluatedByJamwas = false;
    $evaluatorName = 'Kejaksaan Tinggi';

    if ($satkerInfo) {
        $stkLevel = (int)$satkerInfo->id_sakip_level;
        $stkId = (string)$satkerInfo->id_satker;
        if ($stkLevel === 2 || in_array($stkId, $centralIds) || $stkLevel === 1) {
            $isEvaluatedByJamwas = true;
            $evaluatorName = 'JAMWAS';
        } else {
            $evaluatorName = 'Kejaksaan Tinggi';
        }
    }

    $currentUserLevel = (int)session('id_sakip_level');
@endphp
<div class="content" id="content">
<div class="container-fluid py-4 px-4">
    <!-- Header Title -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                @if($isModePm)
                    <i class="bi bi-pencil-square text-warning me-2"></i>Penilaian Mandiri (PM) LKE AKIP
                @elseif($evaluatorType === 'jamwas')
                    <i class="bi bi-shield-check text-success me-2"></i>Evaluasi LKE AKIP - JAMWAS
                @elseif($evaluatorType === 'kejati')
                    <i class="bi bi-shield-check text-primary me-2"></i>Evaluasi LKE AKIP - Kejaksaan Tinggi
                @else
                    <i class="bi bi-clipboard-check-fill text-primary me-2"></i>Evaluasi LKE AKIP
                @endif
            </h3>
            <p class="text-muted mb-0">
                @if($isModePm)
                    Tahap Penilaian Mandiri (PM) oleh Satuan Kerja berdasarkan parameter kriteria dan bukti dukung yang selanjutnya dievaluasi oleh {{ $evaluatorName }}.
                @elseif($evaluatorType === 'jamwas')
                    Tahap Evaluasi oleh JAMWAS untuk mengevaluasi Kejaksaan Tinggi dan Unit Eselon I serta meninjau bukti dukung dan hasil Penilaian Mandiri (PM) mereka.
                @elseif($evaluatorType === 'kejati')
                    Tahap Evaluasi oleh Kejaksaan Tinggi terhadap Kejaksaan Negeri dan Cabang Kejaksaan Negeri di wilayah hukumnya, meninjau bukti dukung dan hasil Penilaian Mandiri (PM).
                @else
                    Monitoring dan Evaluasi LKE AKIP Kejaksaan (Penilaian Mandiri PM & Hasil Evaluasi).
                @endif
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            @if(session('id_sakip_level') == 99 || in_array(session('id_satker'), ['admin', '999999']))
                <a href="{{ route('lke.evidence_mapping.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-gear-fill me-1"></i> Mapping Bukti Dukung (Admin)
                </a>
            @endif

            @if($isModePm)
                @php
                    $curLevel = session('id_sakip_level');
                    $userSid = (string)session('id_satker');
                    if (in_array($userSid, $centralIds)) {
                        $levelBadgeText = 'Unit Eselon I / Pusat';
                    } else {
                        $levelBadgeText = ($curLevel == 2) ? 'Kejaksaan Tinggi' : (($curLevel == 3) ? 'Kejaksaan Negeri' : (($curLevel == 4) ? 'Cabang Kejaksaan Negeri' : 'Satuan Kerja'));
                    }
                @endphp
                <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Penilaian Mandiri ({{ $levelBadgeText }})
                </span>
            @elseif($evaluatorType === 'jamwas')
                <span class="badge bg-success px-3 py-2 fs-6 rounded-pill shadow-sm">
                    <i class="bi bi-shield-check me-1"></i> Evaluator: JAMWAS
                </span>
            @elseif($evaluatorType === 'kejati')
                <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill shadow-sm">
                    <i class="bi bi-shield-check me-1"></i> Evaluator: Kejaksaan Tinggi
                </span>
            @else
                <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill shadow-sm">
                    <i class="bi bi-shield-lock-fill me-1"></i> Administrator Pusat (Evaluasi)
                </span>
            @endif
        </div>
    </div>

    <!-- Satker & Tahun Selector Bar -->
    <div class="lke-filter-toolbar mb-4">
        <form method="GET" action="{{ route('lke.evaluasi.index') }}" class="row g-3 align-items-center" id="satkerFilterForm">
            <div class="col-md-7">
                <label class="form-label text-xs fw-semibold text-muted mb-1">
                    @if($evaluatorType === 'jamwas')
                        Pilih Target Evaluasi JAMWAS (Unit Eselon I & Kejaksaan Tinggi)
                    @elseif($evaluatorType === 'kejati' || $currentUserLevel === 2)
                        Pilih Satuan Kerja (Kejati Sendiri untuk PM, atau Kejari/Cabjari Target Evaluasi)
                    @elseif($isModePm)
                        Pilih Satuan Kerja (Pengisian Penilaian Mandiri / PM)
                    @else
                        Pilih Satuan Kerja (Target Evaluasi)
                    @endif
                </label>
                <select name="id_satker" class="form-select select2" onchange="this.form.submit()">
                    @php
                        // Grouping Satuan Kerja secara rapi berdasarkan kategori wewenang
                        $groupedSatker = [];
                        foreach ($satkerList as $stk) {
                            $sid = (string)$stk->id_satker;
                            $lvl = (int)$stk->id_sakip_level;
                            $isUserSelf = ($sid === (string)session('id_satker'));

                            if ($evaluatorType === 'kejati' || $currentUserLevel === 2) {
                                if ($isUserSelf) {
                                    $group = 'Satuan Kerja Sendiri (Pengisian Penilaian Mandiri)';
                                } elseif ($lvl === 3) {
                                    $group = 'Kejaksaan Negeri (Target Evaluasi Kejati)';
                                } elseif ($lvl === 4) {
                                    $group = 'Cabang Kejaksaan Negeri (Target Evaluasi Kejati)';
                                } else {
                                    $group = 'Satuan Kerja Lainnya';
                                }
                            } elseif ($evaluatorType === 'jamwas') {
                                if ($lvl === 1 || in_array($sid, $centralIds)) {
                                    $group = 'Unit Eselon I / Pusat (Target Evaluasi JAMWAS)';
                                } else {
                                    $group = 'Kejaksaan Tinggi (Target Evaluasi JAMWAS)';
                                }
                            } else {
                                if ($lvl === 1 || in_array($sid, $centralIds)) {
                                    $group = 'Unit Eselon I / Pusat';
                                } elseif ($lvl === 2) {
                                    $group = 'Kejaksaan Tinggi';
                                } elseif ($lvl === 3) {
                                    $group = 'Kejaksaan Negeri';
                                } elseif ($lvl === 4) {
                                    $group = 'Cabang Kejaksaan Negeri';
                                } else {
                                    $group = 'Satuan Kerja';
                                }
                            }
                            $groupedSatker[$group][] = $stk;
                        }
                    @endphp

                    @forelse($groupedSatker as $groupLabel => $items)
                        <optgroup label="{{ $groupLabel }}">
                            @foreach($items as $stk)
                                @php
                                    $isSelf = ((string)$stk->id_satker === (string)session('id_satker'));
                                    $tag = '';
                                    if ($isSelf && ($evaluatorType === 'kejati' || $currentUserLevel === 2)) {
                                        $tag = ' ★ [Satker Anda - Penilaian Mandiri]';
                                    } elseif ($stk->id_sakip_level == 2) {
                                        $tag = ' (Kejati)';
                                    } elseif ($stk->id_sakip_level == 3) {
                                        $tag = ' (Kejari)';
                                    } elseif ($stk->id_sakip_level == 4) {
                                        $tag = ' (Cabjari)';
                                    } elseif (in_array((string)$stk->id_satker, $centralIds)) {
                                        $tag = ' (Eselon I)';
                                    }
                                @endphp
                                <option value="{{ $stk->id_satker }}" {{ $selectedSatker == $stk->id_satker ? 'selected' : '' }}>
                                    [{{ $stk->id_satker }}] {{ str_replace('_', ' ', $stk->satkernama) }}{{ $tag }}
                                </option>
                            @endforeach
                        </optgroup>
                    @empty
                        <option value="" disabled selected>Tidak ada satuan kerja dalam wewenang Anda</option>
                    @endforelse
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs fw-semibold text-muted mb-1">Tahun Penilaian</label>
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    @foreach([2026, 2025, 2024] as $yr)
                        <option value="{{ $yr }}" {{ $tahun == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-emerald w-100">
                    <i class="bi bi-arrow-repeat me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    @if($satkerInfo)
        <!-- Satker Info Banner & Hierarchical Score Dashboard -->
        <div class="lke-hero-banner mb-4 p-4">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="d-flex align-items-center mb-2 flex-wrap gap-2">
                        <span class="badge bg-light text-dark px-2 py-1 font-monospace text-xs">ID: {{ $satkerInfo->id_satker }}</span>
                        <span class="badge bg-warning text-dark px-2 py-1 text-xs fw-semibold">Tahun: {{ $tahun }}</span>
                        <span class="badge {{ $isModeEvaluasi ? 'bg-success text-white' : 'bg-info-subtle text-info-emphasis' }} px-2 py-1 text-xs fw-semibold">
                            @if($isModeEvaluasi)
                                Tahap: Evaluasi ({{ $evaluatorType === 'jamwas' ? 'JAMWAS' : ($evaluatorType === 'kejati' ? 'Kejaksaan Tinggi' : 'Evaluator') }})
                            @else
                                Tahap: Penilaian Mandiri (PM)
                            @endif
                        </span>
                    </div>
                    <h4 class="fw-bold mb-1 text-white">{{ str_replace('_', ' ', $satkerInfo->satkernama) }}</h4>
                    <div class="text-white-50 text-sm mb-3 mb-lg-0 mt-2">
                        @if($isModeEvaluasi)
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <span>
                                    <i class="bi bi-shield-check text-success me-1"></i> Progres Evaluasi: 
                                    <strong class="text-white" id="progressEvaluated">{{ $scores['evaluasi_evaluated_cnt'] ?? $scores['pk_evaluated_cnt'] }}</strong> dari <strong class="text-white">{{ $scores['total_cnt'] }}</strong> Kriteria Telah Dievaluasi.
                                </span>
                                <span class="badge bg-white bg-opacity-10 text-white border border-light border-opacity-25 py-1 px-2">
                                    <i class="bi bi-person-check text-info me-1"></i> Diisi Satker (PM): 
                                    <strong id="progressPmEvaluated">{{ $scores['pm_evaluated_cnt'] }}</strong> / {{ $scores['total_cnt'] }} Kriteria
                                </span>
                            </div>
                        @else
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <span>
                                    <i class="bi bi-pencil-square text-warning me-1"></i> Progres Penilaian Mandiri (PM): 
                                    <strong class="text-white" id="progressEvaluated">{{ $scores['pm_evaluated_cnt'] }}</strong> dari <strong class="text-white">{{ $scores['total_cnt'] }}</strong> Kriteria Telah Dinilai.
                                </span>
                                <span class="badge bg-white bg-opacity-10 text-white border border-light border-opacity-25 py-1 px-2">
                                    <i class="bi bi-shield-check text-success me-1"></i> Telah Dievaluasi {{ $evaluatorName }}: 
                                    <strong id="progressEvalCount">{{ $scores['evaluasi_evaluated_cnt'] ?? $scores['pk_evaluated_cnt'] }}</strong> / {{ $scores['total_cnt'] }} Kriteria
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-5 text-lg-end">
                    @if($isModeEvaluasi)
                        <div class="d-flex justify-content-lg-end gap-2 flex-wrap">
                            <!-- Score Badge Hasil Evaluasi -->
                            <div class="d-inline-block lke-score-badge bg-white text-dark shadow-sm">
                                <div class="text-muted text-xs text-uppercase fw-semibold">
                                    <i class="bi bi-shield-check text-success me-1"></i>Total Hasil Evaluasi
                                </div>
                                <h2 class="fw-bold mb-0 text-success" id="cardGrandTotalEvaluasi">
                                    {{ number_format($scores['grand_total_evaluasi'] ?? $scores['grand_total_pk'], 2) }}
                                </h2>
                                <div class="text-muted text-xs mt-1">Evaluator: {{ $evaluatorType === 'jamwas' ? 'JAMWAS' : ($evaluatorType === 'kejati' ? 'Kejati' : 'Pusat') }}</div>
                            </div>

                            <!-- Score Badge PM (Satker) -->
                            <div class="d-inline-block lke-score-badge bg-white bg-opacity-90 text-dark shadow-sm">
                                <div class="text-muted text-xs text-uppercase fw-semibold">
                                    <i class="bi bi-file-earmark-person text-primary me-1"></i>Nilai PM Satker
                                </div>
                                <h2 class="fw-bold mb-0 text-primary" id="cardGrandTotalPm">
                                    {{ number_format($scores['grand_total_pm'], 2) }}
                                </h2>
                                <div class="text-muted text-xs mt-1">Penilaian Mandiri</div>
                            </div>
                        </div>
                    @else
                        <div class="d-flex justify-content-lg-end gap-2 flex-wrap">
                            <!-- Score Badge PM (Satker) -->
                            <div class="d-inline-block lke-score-badge bg-white text-dark shadow-sm">
                                <div class="text-muted text-xs text-uppercase fw-semibold">
                                    <i class="bi bi-pencil-square text-warning me-1"></i>Total Nilai PM
                                </div>
                                <h2 class="fw-bold mb-0 text-success" id="cardGrandTotal">
                                    {{ number_format($scores['grand_total_pm'], 2) }}
                                </h2>
                                <div class="text-muted text-xs mt-1">Penilaian Mandiri Satker</div>
                            </div>

                            <!-- Score Badge Hasil Evaluasi (dari JAMWAS atau Kejati) -->
                            <div class="d-inline-block lke-score-badge bg-white bg-opacity-90 text-dark shadow-sm">
                                <div class="text-muted text-xs text-uppercase fw-semibold">
                                    <i class="bi bi-shield-check text-info me-1"></i>Total Hasil Evaluasi
                                </div>
                                <h2 class="fw-bold mb-0 text-info" id="cardGrandTotalEvaluasi">
                                    {{ number_format($scores['grand_total_evaluasi'] ?? $scores['grand_total_pk'], 2) }}
                                </h2>
                                <div class="text-muted text-xs mt-1">Evaluasi: {{ $evaluatorName }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4 Komponen Summary Cards -->
        <div class="row g-3 mb-4">
            @foreach($komponenList as $komp)
                @php
                    $kScoreData = $scores['komponen'][$komp->id] ?? [
                        'skor' => 0, 'evaluasi_skor' => 0, 'pk_skor' => 0, 'pm_skor' => 0, 
                        'max_skor' => 0, 'persentase' => 0, 'evaluasi_persentase' => 0, 'pk_persentase' => 0, 'pm_persentase' => 0,
                        'evaluated_cnt' => 0, 'evaluasi_evaluated_cnt' => 0, 'pk_evaluated_cnt' => 0, 'pm_evaluated_cnt' => 0, 'total_cnt' => 0
                    ];
                    $evalScore = $kScoreData['evaluasi_skor'] ?? $kScoreData['pk_skor'];
                    $evalPercent = $kScoreData['evaluasi_persentase'] ?? $kScoreData['pk_persentase'];

                    $displayScore = $isModeEvaluasi ? $evalScore : $kScoreData['pm_skor'];
                    $displayPercent = $isModeEvaluasi ? $evalPercent : $kScoreData['pm_persentase'];
                @endphp
                <div class="col-md-3">
                    <div class="lke-komp-card h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-success-subtle text-success fw-bold text-xs">Komponen {{ $komp->no }}</span>
                            <div class="text-end">
                                <span class="fw-bold fs-5 text-dark" id="kompScore-{{ $komp->id }}">
                                    {{ number_format($displayScore, 2) }}
                                </span>
                                @if($isModeEvaluasi)
                                    <span class="badge bg-primary-subtle text-primary text-xs ms-1" title="Nilai Mandiri Satker">
                                        PM: <span id="kompPmScore-{{ $komp->id }}">{{ number_format($kScoreData['pm_skor'], 2) }}</span>
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info text-xs ms-1" title="Hasil Evaluasi {{ $evaluatorName }}">
                                        Hasil Evaluasi: <span id="kompEvalScore-{{ $komp->id }}">{{ number_format($evalScore, 2) }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="fw-semibold text-dark mb-2 text-sm" style="min-height: 42px;">
                            {{ $komp->nama }}
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-success" id="kompBar-{{ $komp->id }}" role="progressbar" style="width: {{ $displayPercent }}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted text-xs">
                            <span>Max: {{ number_format($komp->bobot ?: $kScoreData['max_skor'], 2) }}</span>
                            <span id="kompPercent-{{ $komp->id }}">{{ $displayPercent }}%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Hierarchical Evaluation Worksheets (Accordion per Komponen & Subkomponen) -->
        <div class="accordion" id="accordionLkeEvaluasi">
            @foreach($komponenList as $komp)
                <div class="accordion-item border-0 shadow-sm rounded-3 mb-3 overflow-hidden">
                    <h2 class="accordion-header" id="headingKomp-{{ $komp->id }}">
                        <button class="accordion-button bg-light text-dark fw-bold py-3 {{ $loop->first ? '' : 'collapsed' }}"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseKomp-{{ $komp->id }}"
                                data-bs-parent="#accordionLkeEvaluasi"
                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                <div>
                                    <span class="badge bg-success me-2 text-sm">Komponen {{ $komp->no }}</span>
                                    <span class="text-md">{{ $komp->nama }}</span>
                                    <span class="badge bg-dark-subtle text-dark border ms-2 text-xs">Bobot: {{ number_format($komp->bobot, 1) }}</span>
                                </div>
                                <div class="text-end">
                                    @php
                                        $evalScore = $scores['komponen'][$komp->id]['evaluasi_skor'] ?? ($scores['komponen'][$komp->id]['pk_skor'] ?? 0);
                                        $pmScore = $scores['komponen'][$komp->id]['pm_skor'] ?? 0;
                                    @endphp
                                    @if($isModeEvaluasi)
                                        <span class="badge bg-white text-dark border px-3 py-2 text-sm">
                                            Hasil Evaluasi: <strong class="text-success" id="headerKompScore-{{ $komp->id }}">{{ number_format($evalScore, 2) }}</strong>
                                            <span class="text-muted mx-1">|</span>
                                            PM Satker: <strong class="text-primary">{{ number_format($pmScore, 2) }}</strong>
                                        </span>
                                    @else
                                        <span class="badge bg-white text-dark border px-3 py-2 text-sm">
                                            Total Skor PM: <strong class="text-success" id="headerKompScore-{{ $komp->id }}">{{ number_format($pmScore, 2) }}</strong>
                                            <span class="text-muted mx-1">|</span>
                                            Hasil Evaluasi: <strong class="text-info">{{ number_format($evalScore, 2) }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseKomp-{{ $komp->id }}"
                         class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                         data-bs-parent="#accordionLkeEvaluasi">
                        <div class="accordion-body p-3 bg-white">
                            <!-- Komponen Action Bar: Toggle All Subkomponen -->
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                                <div class="text-xs text-muted">
                                    <i class="bi bi-diagram-3 me-1 text-primary"></i> Subkomponen dan Kriteria Evaluasi
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill btn-toggle-sub-all" data-target-komp="{{ $komp->id }}">
                                        <i class="bi bi-arrows-collapse me-1"></i> <span class="btn-text">Tutup Semua Subkomponen</span>
                                    </button>
                                </div>
                            </div>

                            @foreach($komp->subkomponen as $sub)
                                @php
                                    $subScoreData = $scores['subkomponen'][$sub->kode] ?? [
                                        'skor' => 0, 'evaluasi_skor' => 0, 'pk_skor' => 0, 'pm_skor' => 0, 
                                        'bobot' => 0, 'koefisien_evaluasi' => 80, 'koefisien_pm' => 80,
                                        'max_skor' => 0, 'persentase' => 0, 'evaluasi_persentase' => 0, 'pk_persentase' => 0, 'pm_persentase' => 0,
                                        'evaluated_cnt' => 0, 'evaluasi_evaluated_cnt' => 0, 'pk_evaluated_cnt' => 0, 'pm_evaluated_cnt' => 0, 'total_cnt' => 0
                                    ];
                                    $subEvalScore = $subScoreData['evaluasi_skor'] ?? $subScoreData['pk_skor'];
                                    $subDisplayScore = $isModeEvaluasi ? $subEvalScore : $subScoreData['pm_skor'];
                                    $subDisplayEvaluated = $isModeEvaluasi 
                                        ? ($subScoreData['evaluasi_evaluated_cnt'] ?? $subScoreData['pk_evaluated_cnt']) 
                                        : $subScoreData['pm_evaluated_cnt'];

                                    $subDashKode = str_replace('.', '-', $sub->kode);
                                    $subSafeKode = str_replace('.', '_', $sub->kode);

                                    $activeKoef = $isModeEvaluasi 
                                        ? ($subScoreData['koefisien_evaluasi'] ?? 80)
                                        : ($subScoreData['koefisien_pm'] ?? 80);

                                    // Subkomponen metadata & gerbang 100%
                                    $isSubKeberadaan = str_ends_with($sub->kode, '.A');
                                    $subType = $isSubKeberadaan ? 'keberadaan' : 'kualitas_pemanfaatan';
                                    $subTotalCnt = $subScoreData['total_cnt'] ?? 0;
                                    $subEvalTerpenuhi = $subScoreData['terpenuhi_eval_cnt'] ?? 0;
                                    $subPmTerpenuhi = $subScoreData['terpenuhi_pm_cnt'] ?? 0;

                                    $isCompleteEval = $subScoreData['is_100_eval'] ?? ($subTotalCnt > 0 && $subEvalTerpenuhi === $subTotalCnt);
                                    $isCompletePm = $subScoreData['is_100_pm'] ?? ($subTotalCnt > 0 && $subPmTerpenuhi === $subTotalCnt);

                                    $currentIsComplete = $isModeEvaluasi ? $isCompleteEval : $isCompletePm;
                                    $currentTerpenuhi = $isModeEvaluasi ? $subEvalTerpenuhi : $subPmTerpenuhi;
                                    $currentNeedsGradasi = $isModeEvaluasi ? ($subScoreData['eval_needs_gradasi'] ?? false) : ($subScoreData['pm_needs_gradasi'] ?? false);

                                    // Koefisien records
                                    $koefData = $subkomponenKoefisien->get($sub->kode);

                                    // Evaluasi koef state
                                    $hasKoefEval = $koefData && !is_null($koefData->nilai);
                                    $koefEvalGradasi = ($koefData && $isCompleteEval) ? ($koefData->gradasi_label ?? 'BB') : 'BB';
                                    $koefEvalUraian = $koefData ? $koefData->uraian : '';
                                    $koefEvalBukti = $koefData ? ($koefData->bukti_dukung ?? '') : '';
                                    $koefEvalLink = $koefData ? ($koefData->link_bukti_dukung ?? '') : '';

                                    // PM koef state
                                    $hasKoefPm = $koefData && !is_null($koefData->pm_nilai);
                                    $koefPmGradasi = ($koefData && $isCompletePm) ? ($koefData->pm_gradasi_label ?? 'BB') : 'BB';
                                    $koefPmUraian = $koefData ? $koefData->pm_uraian : '';
                                    $koefPmBukti = $koefData ? ($koefData->pm_bukti_dukung ?? '') : '';
                                    $koefPmLink = $koefData ? ($koefData->pm_link_bukti_dukung ?? '') : '';

                                    $currentGradasi = $isModeEvaluasi ? $koefEvalGradasi : $koefPmGradasi;
                                    $currentUraian = $isModeEvaluasi ? $koefEvalUraian : $koefPmUraian;
                                    $currentHasFile = $isModeEvaluasi ? !empty($koefEvalLink) : !empty($koefPmLink);
                                @endphp

                                <!-- Card Wrapper Subkomponen -->
                                <div class="card lke-subkomp-card mb-4 border rounded-3 overflow-hidden shadow-sm" id="subkompCard-{{ $subDashKode }}">
                                    <!-- Subkomponen Header (Clickable & Collapsible) -->
                                    <div class="card-header lke-subkomp-header py-2 px-3 d-flex flex-wrap justify-content-between align-items-center cursor-pointer user-select-none"
                                         data-bs-toggle="collapse"
                                         data-bs-target="#collapseSubkomp-{{ $subDashKode }}"
                                         aria-expanded="true">
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <i class="bi bi-chevron-down subkomp-chevron text-dark transition-transform"></i>
                                            <span class="badge bg-dark font-monospace text-xs">{{ $sub->kode }}</span>
                                            <strong class="text-dark text-sm">{{ $sub->nama }}</strong>
                                        </div>
                                        <div class="mt-2 mt-md-0 d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-white text-dark border text-xs" title="Bobot Subkomponen">
                                                Bobot: <strong>{{ number_format($subScoreData['bobot'] ?? $sub->bobot, 1) }}</strong>
                                            </span>
                                            <span class="badge {{ $activeKoef == 100 ? 'bg-success' : ($activeKoef == 90 ? 'bg-info text-dark' : 'bg-secondary') }} text-xs" id="subKoefBadge-{{ $subDashKode }}">
                                                Koef: <strong id="subKoefVal-{{ $subDashKode }}">{{ $activeKoef }}%</strong> ({{ $currentGradasi }})
                                            </span>
                                            <span class="badge bg-secondary-subtle text-secondary text-xs" id="subKriteriaBadge-{{ $subDashKode }}">
                                                {{ $subDisplayEvaluated }} / {{ $subScoreData['total_cnt'] }} Kriteria
                                            </span>
                                            <span class="badge bg-success px-3 py-2 text-xs rounded-pill shadow-xs">
                                                {{ $isModeEvaluasi ? 'Skor Hasil Evaluasi: ' : 'Skor PM: ' }}
                                                <span id="subScore-{{ $subDashKode }}">{{ number_format($subDisplayScore, 2) }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Collapsible Body Subkomponen (Menampung Gradasi & Kriteria di bawahnya) -->
                                    <div class="collapse show lke-subkomp-collapse" id="collapseSubkomp-{{ $subDashKode }}">
                                        <div class="card-body p-3 bg-white">

                                <!-- SECTION GRADASI NILAI SUBKOMPONEN (Default 80% BB -> Pilihan A 90% atau AA 100%) -->
                                <div class="card border border-primary-subtle shadow-sm rounded-3 mb-3 bg-white" id="cardKoefisien-{{ $subDashKode }}">
                                    <div class="card-header bg-light py-2 px-3 d-flex flex-wrap justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-sliders text-primary"></i>
                                            <strong class="text-xs text-dark">Section Gradasi Nilai Subkomponen {{ $sub->kode }}</strong>
                                            <span class="text-xs text-muted">(Rumus: Rasio Terpenuhi × Koefisien × Bobot {{ number_format($subScoreData['bobot'] ?? $sub->bobot, 1) }})</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-1 mt-md-0">
                                            <span class="badge {{ $activeKoef == 100 ? 'bg-success' : ($activeKoef == 90 ? 'bg-info text-dark' : 'bg-secondary') }} text-xs rounded-pill px-3 py-1 shadow-sm" id="badgeActiveKoef-{{ $subDashKode }}">
                                                Koefisien Aktif: <strong id="valActiveKoef-{{ $subDashKode }}">{{ $activeKoef }}%</strong> 
                                                ({{ $currentGradasi }})
                                            </span>
                                            <button class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKoef-{{ $subDashKode }}" aria-expanded="true">
                                                <i class="bi bi-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="collapse show" id="collapseKoef-{{ $subDashKode }}">
                                        <div class="card-body p-3">
                                            <div class="row g-3">
                                                <!-- Kolom Kiri: Riwayat / Usulan Pihak Lain -->
                                                <div class="col-lg-6">
                                                    @if($isModeEvaluasi)
                                                        <!-- Evaluator melihat usulan gradasi dari Satker (PM) -->
                                                        <div class="p-3 rounded-3 border border-primary-subtle bg-white h-100">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="text-xs fw-bold text-primary text-uppercase">
                                                                    <i class="bi bi-file-earmark-person-fill me-1"></i> Usulan Gradasi Satker (PM)
                                                                </span>
                                                                @if($hasKoefPm)
                                                                    <span class="badge {{ $koefPmGradasi === 'AA' ? 'bg-success' : ($koefPmGradasi === 'A' ? 'bg-info text-dark' : 'bg-secondary') }} rounded-pill px-2 py-1 text-xs">
                                                                        Gradasi {{ $koefPmGradasi }} ({{ $koefData->effective_pm_koefisien }}%)
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 text-xs">
                                                                        Belum Mengajukan (Default 80%)
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="text-xs text-muted mb-2">
                                                                <strong>Status Usulan PM:</strong> {{ $hasKoefPm ? 'Gradasi ' . $koefPmGradasi . ' (' . $koefData->effective_pm_koefisien . '%)' : 'Belum memilih gradasi (Default 80%)' }}
                                                            </div>
                                                            <div class="text-xs text-muted mb-1 fw-semibold">Uraian / Penjelasan Satker:</div>
                                                            <div class="p-2 rounded bg-light border text-xs text-dark mb-2" style="max-height: 80px; overflow-y: auto;">
                                                                {{ $koefPmUraian ?: 'Satker tidak mencantumkan uraian penyesuaian gradasi.' }}
                                                            </div>
                                                            <div class="text-xs text-muted">
                                                                <strong>Bukti Dukung Satker:</strong>
                                                                @if(!empty($koefPmLink))
                                                                    <a href="{{ asset($koefPmLink) }}" target="_blank" class="badge bg-success-subtle text-success text-decoration-none ms-1">
                                                                        <i class="bi bi-download me-1"></i> {{ $koefPmBukti ?: 'Lihat File Bukti' }}
                                                                    </a>
                                                                @elseif(!empty($koefPmBukti))
                                                                    <span class="badge bg-secondary-subtle text-dark ms-1">{{ $koefPmBukti }}</span>
                                                                @else
                                                                    <span class="text-muted fst-italic">Tidak ada bukti dukung diunggah.</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        <!-- Satker melihat hasil evaluasi gradasi dari Evaluator (JAMWAS / Kejati) -->
                                                        <div class="p-3 rounded-3 border {{ $hasKoefEval ? 'border-success-subtle bg-success-subtle bg-opacity-10' : 'border-secondary-subtle bg-light' }} h-100">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="text-xs fw-bold text-dark">
                                                                    <i class="bi bi-shield-check text-success me-1"></i> Hasil Evaluasi Gradasi ({{ $evaluatorName }}):
                                                                </span>
                                                                @if($hasKoefEval)
                                                                    <span class="badge {{ $koefEvalGradasi === 'AA' ? 'bg-success' : ($koefEvalGradasi === 'A' ? 'bg-info text-dark' : 'bg-secondary') }} rounded-pill px-2 py-1 text-xs">
                                                                        Gradasi {{ $koefEvalGradasi }} ({{ $koefData->effective_koefisien }}%)
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 text-xs">
                                                                        Menunggu Evaluasi {{ $evaluatorName }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            @if(!empty($koefEvalUraian))
                                                                <div class="p-2 rounded bg-white border text-xs text-dark mt-2">
                                                                    <span class="text-muted fw-semibold">Catatan Evaluator:</span><br>
                                                                    {{ $koefEvalUraian }}
                                                                </div>
                                                            @endif
                                                            @if(!empty($koefEvalLink))
                                                                <div class="mt-2 text-xs">
                                                                    <a href="{{ asset($koefEvalLink) }}" target="_blank" class="badge bg-primary-subtle text-primary text-decoration-none">
                                                                        <i class="bi bi-eye me-1"></i> Dokumen Catatan Evaluator
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Kolom Kanan: Form Penentuan Gradasi Aktif -->
                                                <div class="col-lg-6">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label class="text-xs fw-bold text-dark mb-0">
                                                            <i class="bi {{ $isModeEvaluasi ? 'bi-shield-check text-success' : 'bi-pencil-square text-warning' }} me-1"></i>
                                                            {{ $isModeEvaluasi ? 'Form Evaluasi Gradasi (' . ($evaluatorType === 'jamwas' ? 'JAMWAS' : 'Kejati') . '):' : 'Form Pengajuan Gradasi Satker (PM):' }}
                                                        </label>
                                                        <span class="badge {{ $currentIsComplete ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-muted' }}" id="statusGate-{{ $subDashKode }}">
                                                            <i class="bi {{ $currentIsComplete ? 'bi-unlock-fill' : 'bi-lock-fill' }} me-1"></i>
                                                            <span id="statusGateText-{{ $subDashKode }}">
                                                                {{ $currentIsComplete ? 'Gerbang Terbuka (100% Terpenuhi)' : 'Terkunci (' . $currentTerpenuhi . '/' . $subTotalCnt . ' Terpenuhi)' }}
                                                            </span>
                                                        </span>
                                                    </div>

                                                    <!-- Gate Alert Banner -->
                                                    <div id="gateAlertContainer-{{ $subDashKode }}">
                                                        @if(!$currentIsComplete)
                                                            <div class="alert alert-secondary py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0 bg-light" id="gateAlert-{{ $subDashKode }}">
                                                                <i class="bi bi-shield-lock-fill text-secondary fs-6"></i>
                                                                <div><strong>Form Gradasi Terkunci (Koefisien Default 80%):</strong> Sub komponen belum memenuhi seluruh kriteria (baru <strong>{{ $currentTerpenuhi }}</strong> dari <strong>{{ $subTotalCnt }}</strong> kriteria terpenuhi). Form gradasi baru dapat diisi jika seluruh kriteria 100% Terpenuhi.</div>
                                                            </div>
                                                        @elseif($currentNeedsGradasi)
                                                            <div class="alert alert-warning py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0" id="gateAlert-{{ $subDashKode }}">
                                                                <i class="bi bi-exclamation-triangle-fill text-warning fs-6"></i>
                                                                <div><strong>Kriteria 100% Terpenuhi!</strong> Harap pilih opsi Gradasi Nilai (BB, A, atau AA) untuk sub komponen ini.</div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Container Pilihan Gradasi -->
                                                    <div id="gradasiFields-{{ $subDashKode }}" class="{{ !$currentIsComplete ? 'opacity-50 pe-none' : '' }}">
                                                        <div class="text-xs fw-semibold text-muted mb-1">Pilih Gradasi Nilai Subkomponen:</div>
                                                        
                                                        <div class="d-flex flex-column gap-2 mb-2">
                                                            <!-- Opsi BB -->
                                                            <div class="form-check p-2 rounded border bg-light">
                                                                <input class="form-check-input radio-gradasi" 
                                                                       type="radio" 
                                                                       name="gradasi_{{ $currentTahap }}_{{ $subSafeKode }}" 
                                                                       id="gradasi_bb_{{ $currentTahap }}_{{ $subSafeKode }}" 
                                                                       value="BB" 
                                                                       data-subkomponen="{{ $sub->kode }}"
                                                                       data-koef="80"
                                                                       {{ ($currentGradasi === 'BB' || empty($currentGradasi)) ? 'checked' : '' }}
                                                                       {{ !$currentIsComplete ? 'disabled' : '' }}>
                                                                <label class="form-check-label text-xs cursor-pointer d-block" for="gradasi_bb_{{ $currentTahap }}_{{ $subSafeKode }}">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <strong class="text-dark"><span class="badge bg-secondary text-xs me-1">BB</span> Koefisien 80%</strong>
                                                                        <span class="badge bg-light text-muted border text-xs">Standar Pemenuhan</span>
                                                                    </div>
                                                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                                                        @if($subType === 'keberadaan')
                                                                            Jika kualitas seluruh kriteria telah terpenuhi (100%).
                                                                        @else
                                                                            Jika kualitas seluruh kriteria telah terpenuhi (100%).
                                                                        @endif
                                                                    </div>
                                                                </label>
                                                            </div>

                                                            <!-- Opsi A -->
                                                            <div class="form-check p-2 rounded border bg-light">
                                                                <input class="form-check-input radio-gradasi" 
                                                                       type="radio" 
                                                                       name="gradasi_{{ $currentTahap }}_{{ $subSafeKode }}" 
                                                                       id="gradasi_a_{{ $currentTahap }}_{{ $subSafeKode }}" 
                                                                       value="A" 
                                                                       data-subkomponen="{{ $sub->kode }}"
                                                                       data-koef="90"
                                                                       {{ $currentGradasi === 'A' ? 'checked' : '' }}
                                                                       {{ !$currentIsComplete ? 'disabled' : '' }}>
                                                                <label class="form-check-label text-xs cursor-pointer d-block" for="gradasi_a_{{ $currentTahap }}_{{ $subSafeKode }}">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <strong class="text-primary"><span class="badge bg-info text-dark text-xs me-1">A</span> Koefisien 90%</strong>
                                                                        <span class="badge bg-info-subtle text-info border text-xs">Tingkat Lanjutan</span>
                                                                    </div>
                                                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                                                        @if($subType === 'keberadaan')
                                                                            Jika seluruh kriteria telah terpenuhi (100%) dan telah dipertahankan dalam setidaknya <strong>2 tahun terakhir</strong>.
                                                                        @else
                                                                            Jika kualitas seluruh kriteria telah terpenuhi (100%) dan terdapat <strong>beberapa upaya yang bisa dihargai</strong> dari pemenuhan kriteria tersebut.
                                                                        @endif
                                                                    </div>
                                                                </label>
                                                            </div>

                                                            <!-- Opsi AA -->
                                                            <div class="form-check p-2 rounded border bg-light">
                                                                <input class="form-check-input radio-gradasi" 
                                                                       type="radio" 
                                                                       name="gradasi_{{ $currentTahap }}_{{ $subSafeKode }}" 
                                                                       id="gradasi_aa_{{ $currentTahap }}_{{ $subSafeKode }}" 
                                                                       value="AA" 
                                                                       data-subkomponen="{{ $sub->kode }}"
                                                                       data-koef="100"
                                                                       {{ $currentGradasi === 'AA' ? 'checked' : '' }}
                                                                       {{ !$currentIsComplete ? 'disabled' : '' }}>
                                                                <label class="form-check-label text-xs cursor-pointer d-block" for="gradasi_aa_{{ $currentTahap }}_{{ $subSafeKode }}">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <strong class="text-success"><span class="badge bg-success text-xs me-1">AA</span> Koefisien 100%</strong>
                                                                        <span class="badge bg-success-subtle text-success border text-xs">Keunggulan Nasional</span>
                                                                    </div>
                                                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                                                        @if($subType === 'keberadaan')
                                                                            Jika seluruh kriteria telah terpenuhi (100%) dan telah dipertahankan dalam setidaknya <strong>5 tahun terakhir</strong>.
                                                                        @else
                                                                            Jika kualitas seluruh kriteria telah terpenuhi (100%) dan terdapat <strong>upaya inovatif serta layak menjadi percontohan di tingkat nasional</strong>.
                                                                        @endif
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <!-- Kotak Input Uraian / Penjelasan -->
                                                        <div class="mb-2">
                                                            <label class="text-xs fw-semibold text-muted mb-1">
                                                                <i class="bi bi-chat-left-text me-1"></i> Uraian / Penjelasan <span class="text-danger req-star-uraian-{{ $subDashKode }} {{ ($currentGradasi === 'BB' || empty($currentGradasi)) ? 'd-none' : '' }}">(Wajib diisi untuk A / AA)*</span>:
                                                            </label>
                                                            <textarea class="form-control form-control-sm text-sm input-uraian-koef" 
                                                                      id="uraian_koef_{{ $currentTahap }}_{{ $subSafeKode }}" 
                                                                      rows="2" 
                                                                      placeholder="Tuliskan uraian dan penjelasan pemenuhan kriteria gradasi..." 
                                                                      style="resize: vertical;">{{ $currentUraian }}</textarea>
                                                        </div>

                                                        <!-- Input Bukti Dukung (File Upload - Opsional) -->
                                                        <div class="mb-2">
                                                            <label class="text-xs fw-semibold text-muted mb-1">
                                                                <i class="bi bi-paperclip me-1"></i> Upload Bukti Dukung (Opsional):
                                                            </label>
                                                            <input type="file" 
                                                                   class="form-control form-control-sm text-xs mb-1" 
                                                                   id="file_koef_{{ $currentTahap }}_{{ $subSafeKode }}">
                                                            @if($currentHasFile)
                                                                <div class="text-xs text-success" id="fileStatus-{{ $subDashKode }}">
                                                                    <i class="bi bi-check-circle-fill me-1"></i> File telah terunggah sebelumnya. Unggah lagi jika ingin mengganti.
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="text-end">
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-emerald px-3 rounded-pill btn-save-koef" 
                                                                    id="btnSaveKoef-{{ $subDashKode }}"
                                                                    data-subkomponen="{{ $sub->kode }}" 
                                                                    data-tahap="{{ $currentTahap }}"
                                                                    data-subtype="{{ $subType }}"
                                                                    data-has-file="{{ $currentHasFile ? 'true' : 'false' }}"
                                                                    {{ !$currentIsComplete ? 'disabled' : '' }}>
                                                                <i class="bi bi-save me-1"></i> Simpan Gradasi {{ $isModeEvaluasi ? 'Evaluasi' : 'PM' }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                            <!-- Header Section Kriteria di bawah Subkomponen -->
                                            <div class="d-flex justify-content-between align-items-center mb-2 px-1 flex-wrap gap-2">
                                                <div class="text-xs fw-bold text-muted text-uppercase tracking-wide">
                                                    <i class="bi bi-list-check me-1 text-primary"></i> Daftar Kriteria Penilaian ({{ count($sub->kriteria) }})
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill btn-toggle-crit-all" data-target-sub="{{ $subDashKode }}">
                                                        <i class="bi bi-arrows-collapse me-1"></i> <span class="btn-text">Tutup Semua Kriteria</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Daftar Kriteria Penilaian di Bawah Subkomponen -->
                                            <div class="lke-criteria-list" id="critList-{{ $subDashKode }}">
                                                @foreach($sub->kriteria as $cr)
                                                    @php
                                                        $eval = $existingPenilaian->get($cr->kode);
                                                        $evidences = $satkerBukti->get($cr->kode, collect());
                                                        $requiredBuktiIds = $cr->bukti_dukung_ids;
                                                        $safeKode = str_replace('.', '_', $cr->kode);
                                                        $dashKode = str_replace('.', '-', $cr->kode);

                                                        // Status Hasil Evaluasi (JAMWAS / Kejati)
                                                        $hasEvaluasi = $eval && !is_null($eval->parameter_id);
                                                        $evalNilai = $hasEvaluasi ? $eval->nilai : null;
                                                        $evalSkor = $hasEvaluasi ? (float)$eval->skor : 0.00;
                                                        $evalCatatan = $hasEvaluasi ? $eval->catatan : '';

                                                        // Status Penilaian Mandiri (PM Satker)
                                                        $hasPm = $eval && !is_null($eval->pm_parameter_id);
                                                        $pmNilai = $hasPm ? $eval->pm_nilai : null;
                                                        $pmSkor = $hasPm ? (float)$eval->pm_skor : 0.00;
                                                        $pmCatatan = $hasPm ? $eval->pm_catatan : '';
                                                    @endphp
                                                    <div class="card lke-kriteria-card mb-3 border rounded-3 overflow-hidden shadow-xs" id="cardKriteria-{{ $dashKode }}">
                                                        <!-- Kriteria Card Header (Clickable & Collapsible) -->
                                                        <div class="card-header lke-crit-header bg-white py-2 px-3 d-flex flex-wrap justify-content-between align-items-center cursor-pointer user-select-none"
                                                             data-bs-toggle="collapse" 
                                                             data-bs-target="#collapseCrit-{{ $dashKode }}" 
                                                             aria-expanded="true">
                                                            <div class="d-flex align-items-center gap-2 flex-grow-1 me-2">
                                                                <i class="bi bi-chevron-down crit-chevron text-muted transition-transform me-1"></i>
                                                                <span class="badge bg-secondary-subtle text-dark border font-monospace text-xs">{{ $cr->kode }}</span>
                                                                <span class="fw-semibold text-dark text-sm">{{ $cr->nama }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2 mt-1 mt-md-0">
                                                                @if($isModeEvaluasi)
                                                                    <span class="badge {{ ($hasEvaluasi && $evalNilai === 'Terpenuhi') ? 'bg-success-subtle text-success' : (($hasEvaluasi && $evalNilai === 'Tidak Terpenuhi') ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary') }} current-score-badge text-xs" id="badgeScore-{{ $dashKode }}">
                                                                        @if($hasEvaluasi)
                                                                            Hasil Evaluasi: {{ $evalNilai }} (Skor: {{ number_format($evalSkor, 2) }})
                                                                        @else
                                                                            Belum Dievaluasi
                                                                        @endif
                                                                    </span>
                                                                @else
                                                                    <span class="badge {{ ($hasPm && $pmNilai === 'Terpenuhi') ? 'bg-success-subtle text-success' : (($hasPm && $pmNilai === 'Tidak Terpenuhi') ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary') }} current-score-badge text-xs" id="badgeScore-{{ $dashKode }}">
                                                                        @if($hasPm)
                                                                            Nilai PM: {{ $pmNilai }} (Skor: {{ number_format($pmSkor, 2) }})
                                                                        @else
                                                                            Belum Dinilai Mandiri
                                                                        @endif
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Kriteria Card Collapsible Body -->
                                                        <div class="collapse show lke-crit-collapse" id="collapseCrit-{{ $dashKode }}">
                                                            <div class="card-body p-3 bg-white border-top">
                                                                <div class="row g-3">
                                                    <!-- Kriteria Description & Evidence Column -->
                                                    <div class="col-lg-6">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <span class="badge bg-dark font-monospace text-xs me-2">{{ $cr->kode }}</span>
                                                            <div class="fw-bold text-dark text-sm">{{ $cr->nama }}</div>
                                                        </div>

                                                        <!-- Required Evidence Badges -->
                                                        <div class="mt-2 p-2 bg-light rounded-2 border">
                                                            <div class="text-xs fw-bold text-muted mb-2">
                                                                <i class="bi bi-file-earmark-check me-1 text-success"></i> Bukti Dukung yang Diperlukan:
                                                            </div>
                                                            @if(!empty($requiredBuktiIds))
                                                                <ul class="list-unstyled mb-0 text-xs">
                                                                    @foreach($requiredBuktiIds as $bId)
                                                                        @php
                                                                            $docName = $masterBukti[$bId] ?? 'Dokumen #'.$bId;
                                                                            $hasUploaded = $evidences->first(function ($item) use ($bId) {
                                                                                return $item->buktidukung_id == $bId 
                                                                                    || $item->kode_bukti == $bId
                                                                                    || \App\Helpers\LkeEvidenceHelper::matchesBuktiId($item->link_bukti_dukung, (int)$bId);
                                                                            });
                                                                        @endphp
                                                                        <li class="mb-1 d-flex align-items-center justify-content-between">
                                                                            <span>
                                                                                <span class="badge bg-secondary me-1">#{{ $bId }}</span>
                                                                                {{ $docName }}
                                                                            </span>
                                                                            @if($hasUploaded)
                                                                                @php
                                                                                    $targetSatkerId = $hasUploaded->id_satker ?? $selectedSatker;
                                                                                    $rawLink = $hasUploaded->link_bukti_dukung;
                                                                                    $fileUrl = str_starts_with($rawLink, 'http')
                                                                                        ? $rawLink
                                                                                        : asset('uploads/repository/' . $targetSatkerId . '/' . basename($rawLink));
                                                                                @endphp
                                                                                <a href="{{ $fileUrl }}" target="_blank" class="badge bg-success-subtle text-success text-decoration-none border border-success-subtle hover-shadow">
                                                                                    <i class="bi bi-eye me-1"></i> Ada File
                                                                                </a>
                                                                            @else
                                                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Belum Diupload</span>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <span class="text-muted text-xs fst-italic">Tidak memerlukan bukti dukung khusus.</span>
                                                            @endif
                                                        </div>

                                                        @if($isModeEvaluasi)
                                                            <!-- Hasil Penilaian Mandiri (PM) Satker yang Sedang Dievaluasi -->
                                                            <div class="mt-3 p-3 rounded-3 border border-primary-subtle bg-white shadow-sm">
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <span class="text-xs fw-bold text-primary text-uppercase">
                                                                        <i class="bi bi-file-earmark-person-fill me-1"></i> Hasil Penilaian Mandiri (PM) Satker
                                                                    </span>
                                                                    @if($hasPm)
                                                                        <span class="badge {{ $pmNilai === 'Terpenuhi' ? 'bg-success' : 'bg-danger' }} rounded-pill px-2 py-1 text-xs shadow-sm" id="badgePm-{{ $dashKode }}">
                                                                            <i class="bi {{ $pmNilai === 'Terpenuhi' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                                                            {{ $pmNilai }} (Skor: {{ number_format($pmSkor, 0) }})
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 text-xs" id="badgePm-{{ $dashKode }}">
                                                                            <i class="bi bi-clock-history me-1"></i> Belum Dinilai Mandiri
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                <!-- Kotak Uraian / Penjelasan dari Satker -->
                                                                <div class="mt-2">
                                                                    <div class="text-xs text-muted fw-semibold mb-1">
                                                                        <i class="bi bi-chat-left-quote me-1"></i>Uraian / Penjelasan Satker:
                                                                    </div>
                                                                    @if(!empty($pmCatatan))
                                                                        <div class="p-2 rounded bg-light border border-secondary-subtle text-xs text-dark" style="max-height: 110px; overflow-y: auto; line-height: 1.4;">
                                                                            {{ $pmCatatan }}
                                                                        </div>
                                                                    @elseif($hasPm)
                                                                        <div class="p-2 rounded bg-light border border-light text-xs text-muted fst-italic">
                                                                            Satker tidak mencantumkan uraian tambahan (Penilaian: Terpenuhi).
                                                                        </div>
                                                                    @else
                                                                        <div class="p-2 rounded bg-light border border-light text-xs text-muted fst-italic">
                                                                            Satuan Kerja belum melakukan pengisian Penilaian Mandiri untuk kriteria ini.
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @else
                                                            <!-- Jika mode PM: Tampilkan Hasil Evaluasi dari JAMWAS / Kejati jika sudah ada -->
                                                            <div class="mt-3 p-3 rounded-3 border {{ $hasEvaluasi ? 'border-success-subtle bg-success-subtle bg-opacity-10' : 'border-secondary-subtle bg-light' }}">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="text-xs fw-bold text-dark">
                                                                        <i class="bi bi-shield-check text-success me-1"></i> Hasil Evaluasi ({{ $evaluatorName }}):
                                                                    </span>
                                                                    @if($hasEvaluasi)
                                                                        <span class="badge {{ $evalNilai === 'Terpenuhi' ? 'bg-success' : 'bg-danger' }} rounded-pill px-2 py-1 text-xs" id="badgeHasilEval-{{ $dashKode }}">
                                                                            <i class="bi {{ $evalNilai === 'Terpenuhi' ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                                                            Hasil Evaluasi: {{ $evalNilai }} (Skor: {{ number_format($evalSkor, 0) }})
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 text-xs" id="badgeHasilEval-{{ $dashKode }}">
                                                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu Evaluasi {{ $evaluatorName }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                @if(!empty($evalCatatan))
                                                                    <div class="p-2 rounded bg-white border border-secondary-subtle text-xs text-dark mt-2">
                                                                        <span class="text-muted fw-semibold"><i class="bi bi-chat-dots me-1"></i>Catatan Hasil Evaluasi / Rekomendasi {{ $evaluatorName }}:</span><br>
                                                                        {{ $evalCatatan }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Scoring / Action Column -->
                                                    <div class="col-lg-6">
                                                        @if($isModeEvaluasi)
                                                            <!-- Form Evaluasi (Evaluator: JAMWAS atau Kejati) -->
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <label class="text-xs fw-bold text-dark mb-0">
                                                                    <i class="bi bi-shield-check text-success me-1"></i>Form Evaluasi ({{ $evaluatorType === 'jamwas' ? 'JAMWAS' : ($evaluatorType === 'kejati' ? 'Kejaksaan Tinggi' : 'Evaluator') }}):
                                                                </label>
                                                                <span class="text-xs text-muted">
                                                                    Pilih opsi penilaian di bawah
                                                                </span>
                                                            </div>

                                                            <!-- Parameter Radio Options for Evaluasi -->
                                                            <div class="parameter-options-container mb-3">
                                                                @foreach($cr->parameters as $p)
                                                                    @php
                                                                        $isSelected = $hasEvaluasi && $eval->parameter_id == $p->id;
                                                                        $isTerpenuhi = $p->nilai === 'Terpenuhi';
                                                                    @endphp
                                                                    <div class="form-check lke-param-option mb-2 option-box {{ $isSelected ? 'selected' : '' }} {{ $isSelected ? ($isTerpenuhi ? 'border-success bg-success-subtle' : 'border-danger bg-danger-subtle') : '' }}">
                                                                        <input class="form-check-input ms-1 me-2 radio-param" 
                                                                               type="radio" 
                                                                               name="param_evaluasi_{{ $safeKode }}" 
                                                                               id="p_eval_{{ $p->id }}_{{ $safeKode }}" 
                                                                               value="{{ $p->id }}"
                                                                               data-kriteria="{{ $cr->kode }}"
                                                                               data-skor="{{ $p->skor }}"
                                                                               data-nilai="{{ $p->nilai }}"
                                                                               data-tahap="evaluasi"
                                                                               {{ $isSelected ? 'checked' : '' }}>
                                                                        <label class="form-check-label w-100 cursor-pointer" for="p_eval_{{ $p->id }}_{{ $safeKode }}">
                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                <strong class="text-dark text-sm">
                                                                                    <i class="bi {{ $isTerpenuhi ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} me-1"></i>
                                                                                    {{ $p->nilai }}
                                                                                </strong>
                                                                                <span class="badge {{ $isTerpenuhi ? 'bg-success' : 'bg-danger' }} rounded-pill text-xs">Skor: {{ number_format($p->skor, 0) }}</span>
                                                                            </div>
                                                                            @if($p->keterangan)
                                                                                <div class="text-xs text-muted mt-1">{{ $p->keterangan }}</div>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            <!-- Kotak Uraian / Catatan Hasil Evaluasi / Rekomendasi -->
                                                            <div class="mb-2">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label class="text-xs fw-semibold text-muted mb-0">
                                                                        <i class="bi bi-chat-left-text me-1"></i>Catatan Hasil Evaluasi / Rekomendasi:
                                                                    </label>
                                                                    <span class="badge bg-danger-subtle text-danger text-xs fw-semibold mandatory-badge-{{ $safeKode }}" 
                                                                          style="{{ ($hasEvaluasi && ($evalNilai === 'Tidak Terpenuhi' || $evalSkor == 0)) ? '' : 'display: none;' }}">
                                                                        <i class="bi bi-asterisk me-1"></i>Wajib diisi jika Tidak Terpenuhi
                                                                    </span>
                                                                </div>
                                                                <textarea class="form-control form-control-sm input-catatan text-sm" 
                                                                          id="catatan_{{ $safeKode }}" 
                                                                          rows="2" 
                                                                          placeholder="{{ ($hasEvaluasi && ($evalNilai === 'Tidak Terpenuhi' || $evalSkor == 0)) ? 'Wajib diisi: Uraian/penjelasan mengapa Tidak Terpenuhi...' : 'Tuliskan catatan hasil evaluasi / rekomendasi...' }}" 
                                                                          style="resize: vertical;">{{ $evalCatatan }}</textarea>
                                                            </div>

                                                            <div class="text-end">
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-emerald px-3 rounded-pill btn-save-kriteria" 
                                                                        data-kriteria="{{ $cr->kode }}" 
                                                                        data-tahap="evaluasi">
                                                                    <i class="bi bi-save me-1"></i> Simpan Hasil Evaluasi
                                                                </button>
                                                            </div>
                                                        @else
                                                            <!-- Form Penilaian Mandiri (PM) oleh Satuan Kerja -->
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <label class="text-xs fw-bold text-dark mb-0">
                                                                    <i class="bi bi-pencil-square text-warning me-1"></i>Form Penilaian Mandiri (PM) Satker:
                                                                </label>
                                                                <span class="text-xs text-muted">
                                                                    Pilih opsi penilaian di bawah
                                                                </span>
                                                            </div>

                                                            <!-- Parameter Radio Options for PM -->
                                                            <div class="parameter-options-container mb-3">
                                                                @foreach($cr->parameters as $p)
                                                                    @php
                                                                        $isSelected = $hasPm && $eval->pm_parameter_id == $p->id;
                                                                        $isTerpenuhi = $p->nilai === 'Terpenuhi';
                                                                    @endphp
                                                                    <div class="form-check lke-param-option mb-2 option-box {{ $isSelected ? 'selected' : '' }} {{ $isSelected ? ($isTerpenuhi ? 'border-success bg-success-subtle' : 'border-danger bg-danger-subtle') : '' }}">
                                                                        <input class="form-check-input ms-1 me-2 radio-param" 
                                                                               type="radio" 
                                                                               name="param_pm_{{ $safeKode }}" 
                                                                               id="p_pm_{{ $p->id }}_{{ $safeKode }}" 
                                                                               value="{{ $p->id }}"
                                                                               data-kriteria="{{ $cr->kode }}"
                                                                               data-skor="{{ $p->skor }}"
                                                                               data-nilai="{{ $p->nilai }}"
                                                                               data-tahap="pm"
                                                                               {{ $isSelected ? 'checked' : '' }}>
                                                                        <label class="form-check-label w-100 cursor-pointer" for="p_pm_{{ $p->id }}_{{ $safeKode }}">
                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                <strong class="text-dark text-sm">
                                                                                    <i class="bi {{ $isTerpenuhi ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} me-1"></i>
                                                                                    {{ $p->nilai }}
                                                                                </strong>
                                                                                <span class="badge {{ $isTerpenuhi ? 'bg-success' : 'bg-danger' }} rounded-pill text-xs">Skor: {{ number_format($p->skor, 0) }}</span>
                                                                            </div>
                                                                            @if($p->keterangan)
                                                                                <div class="text-xs text-muted mt-1">{{ $p->keterangan }}</div>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            <!-- Kotak Uraian / Penjelasan PM Satker -->
                                                            <div class="mb-2">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label class="text-xs fw-semibold text-muted mb-0">
                                                                        <i class="bi bi-chat-left-text me-1"></i>Kotak Uraian / Penjelasan PM:
                                                                    </label>
                                                                    <span class="badge bg-danger-subtle text-danger text-xs fw-semibold mandatory-badge-{{ $safeKode }}" 
                                                                          style="{{ ($hasPm && ($pmNilai === 'Tidak Terpenuhi' || $pmSkor == 0)) ? '' : 'display: none;' }}">
                                                                        <i class="bi bi-asterisk me-1"></i>Wajib diisi jika Tidak Terpenuhi
                                                                    </span>
                                                                </div>
                                                                <textarea class="form-control form-control-sm input-catatan text-sm" 
                                                                          id="catatan_{{ $safeKode }}" 
                                                                          rows="2" 
                                                                          placeholder="{{ ($hasPm && ($pmNilai === 'Tidak Terpenuhi' || $pmSkor == 0)) ? 'Wajib diisi: Uraian/Penjelasan mengapa Tidak Terpenuhi...' : 'Tuliskan uraian / penjelasan alasan penilaian mandiri...' }}" 
                                                                          style="resize: vertical;">{{ $pmCatatan }}</textarea>
                                                            </div>

                                                            <div class="text-end">
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-emerald px-3 rounded-pill btn-save-kriteria" 
                                                                        data-kriteria="{{ $cr->kode }}" 
                                                                        data-tahap="pm">
                                                                    <i class="bi bi-save me-1"></i> Simpan Nilai PM
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white my-4">
            <div class="mb-3">
                <div class="empty-state-icon-box bg-light p-4 text-warning">
                    <i class="bi bi-building-gear fs-1"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-2">Pilih Satuan Kerja untuk Memulai</h4>
            <p class="text-muted mx-auto mb-3 max-w-md">
                Silakan pilih Satuan Kerja (Satker) dan Tahun Penilaian pada panel filter di atas untuk memuat lembar kerja evaluasi serta kriteria penilaian SAKIP.
            </p>
        </div>
    @endif
</div>
</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3 z-toast">
    <div id="evalToast" class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fs-6" id="toastEvalMessage">
                Nilai berhasil disimpan!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toastEl = document.getElementById('evalToast');
    const toast = new bootstrap.Toast(toastEl, { delay: 2500 });

    function showToast(message, isSuccess = true) {
        document.getElementById('toastEvalMessage').innerHTML = message;
        toastEl.className = `toast align-items-center text-white border-0 shadow ${isSuccess ? 'bg-success' : 'bg-danger'}`;
        toast.show();
    }

    // Inisialisasi Select2 untuk kemudahan pencarian Satuan Kerja
    if (window.jQuery && jQuery.fn.select2) {
        $('select[name="id_satker"]').select2({
            width: '100%'
        }).on('select2:select', function () {
            const form = document.getElementById('satkerFilterForm');
            if (form) form.submit();
        });
    }

    // Interaktivitas: klik seluruh area kotak opsi untuk memilih radio
    document.querySelectorAll('.option-box').forEach(box => {
        box.addEventListener('click', function (e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'LABEL') {
                const radio = this.querySelector('input[type="radio"]');
                if (radio && !radio.disabled) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            }
        });
    });

    // Toggle required indicator for Uraian when gradasi changes (BB vs A/AA)
    document.querySelectorAll('.radio-gradasi').forEach(radio => {
        radio.addEventListener('change', function () {
            const sub = this.dataset.subkomponen;
            const gradVal = this.value;
            const dashSub = sub.replace(/\./g, '-');
            const reqStar = document.querySelector(`.req-star-uraian-${dashSub}`);
            if (reqStar) {
                if (gradVal === 'BB') {
                    reqStar.classList.add('d-none');
                } else {
                    reqStar.classList.remove('d-none');
                }
            }
        });
    });

    // Interactive Option Box Selection styling
    document.querySelectorAll('.radio-param').forEach(radio => {
        radio.addEventListener('change', function () {
            const container = this.closest('.parameter-options-container');
            container.querySelectorAll('.option-box').forEach(box => {
                box.classList.remove('selected', 'border-success', 'bg-success-subtle', 'border-danger', 'bg-danger-subtle');
            });

            const isTerpenuhi = this.dataset.nilai === 'Terpenuhi';
            const isTidakTerpenuhi = this.dataset.nilai === 'Tidak Terpenuhi' || parseFloat(this.dataset.skor) === 0;

            const box = this.closest('.option-box');
            box.classList.add('selected');
            if (isTerpenuhi) {
                box.classList.add('border-success', 'bg-success-subtle');
            } else {
                box.classList.add('border-danger', 'bg-danger-subtle');
            }

            const kriteriaKode = this.dataset.kriteria;
            const safeName = kriteriaKode.replace(/\./g, '_');
            const mandatoryBadge = document.querySelector(`.mandatory-badge-${safeName}`);
            const catatanInput = document.getElementById(`catatan_${safeName}`);

            if (mandatoryBadge) {
                mandatoryBadge.style.display = isTidakTerpenuhi ? 'inline-block' : 'none';
            }

            if (catatanInput) {
                if (isTidakTerpenuhi) {
                    catatanInput.setAttribute('placeholder', 'Wajib diisi: Uraian/Penjelasan mengapa Tidak Terpenuhi...');
                    if (!catatanInput.value.trim()) {
                        catatanInput.classList.add('border-danger');
                    }
                } else {
                    catatanInput.setAttribute('placeholder', 'Tuliskan catatan hasil evaluasi / penjelasan...');
                    catatanInput.classList.remove('border-danger', 'is-invalid');
                }
            }
        });
    });

    // Remove invalid styling on input
    document.querySelectorAll('.input-catatan, .input-uraian-koef').forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid', 'border-danger');
            }
        });
    });

    // Save Subkomponen Gradasi AJAX Handler
    document.querySelectorAll('.btn-save-koef').forEach(btn => {
        btn.addEventListener('click', function () {
            const subKode = this.getAttribute('data-subkomponen');
            const safeName = subKode.replace(/\./g, '_');
            const dashName = subKode.replace(/\./g, '-');
            const tahap = this.getAttribute('data-tahap') || '{{ $currentTahap }}';

            // Find selected gradasi radio (BB, A, AA)
            const gradasiRadio = document.querySelector(`input[name="gradasi_${tahap}_${safeName}"]:checked`)
                || document.querySelector(`input[name="gradasi_${safeName}"]:checked`);
            
            if (!gradasiRadio) {
                showToast('Harap pilih salah satu opsi Gradasi Nilai (BB, A, atau AA).', false);
                return;
            }

            const gradasi = gradasiRadio.value;
            const uraianInput = document.getElementById(`uraian_koef_${tahap}_${safeName}`)
                || document.getElementById(`uraian_koef_${safeName}`);
            const uraian = uraianInput ? uraianInput.value.trim() : '';

            const fileInput = document.getElementById(`file_koef_${tahap}_${safeName}`)
                || document.getElementById(`file_koef_${safeName}`);

            // Validasi: Jika memilih A atau AA, kotak Uraian / Penjelasan wajib diisi
            if (gradasi === 'A' || gradasi === 'AA') {
                if (!uraian) {
                    showToast(`Untuk memilih opsi <strong>Gradasi ${gradasi}</strong>, wajib mengisi kotak <strong>Uraian/Penjelasan</strong> terlebih dahulu.`, false);
                    if (uraianInput) {
                        uraianInput.classList.add('is-invalid', 'border-danger');
                        uraianInput.focus();
                    }
                    return;
                }
            }

            if (uraianInput) uraianInput.classList.remove('is-invalid', 'border-danger');
            if (fileInput) fileInput.classList.remove('is-invalid', 'border-danger');

            const idSatker = '{{ $selectedSatker }}';
            const tahun = '{{ $tahun }}';

            const formData = new FormData();
            formData.append('id_satker', idSatker);
            formData.append('tahun', tahun);
            formData.append('subkomponen_kode', subKode);
            formData.append('gradasi', gradasi);
            formData.append('uraian', uraian);
            formData.append('tahap', tahap);
            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                formData.append('bukti_file', fileInput.files[0]);
            }

            const button = this;
            button.disabled = true;
            const originalBtnHtml = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch('{{ route("lke.evaluasi.save_koefisien") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                button.disabled = false;
                button.innerHTML = originalBtnHtml;

                if (data.success) {
                    showToast(data.message, true);

                    // Update coefficient badges
                    const effKoef = data.effective_koefisien;
                    const resGradasi = data.gradasi || gradasi;
                    const badgeVal = document.getElementById(`valActiveKoef-${dashName}`);
                    const badgeEl = document.getElementById(`badgeActiveKoef-${dashName}`);
                    const subKoefVal = document.getElementById(`subKoefVal-${dashName}`);
                    const subKoefBadge = document.getElementById(`subKoefBadge-${dashName}`);

                    if (badgeVal) badgeVal.textContent = effKoef + '%';
                    if (subKoefVal) subKoefVal.textContent = effKoef + '%';

                    const higherClass = (effKoef == 100) ? 'bg-success' : (effKoef == 90 ? 'bg-info text-dark' : 'bg-secondary');

                    if (badgeEl) {
                        badgeEl.className = `badge ${higherClass} text-xs rounded-pill px-3 py-1 shadow-sm`;
                        badgeEl.innerHTML = `Koefisien Aktif: <strong id="valActiveKoef-${dashName}">${effKoef}%</strong> (${resGradasi})`;
                    }
                    if (subKoefBadge) {
                        subKoefBadge.className = `badge ${higherClass} text-xs`;
                        subKoefBadge.innerHTML = `Koef: <strong id="subKoefVal-${dashName}">${effKoef}%</strong> (${resGradasi})`;
                    }

                    // Bersihkan alert peringatan jika ada
                    const gateAlertContainer = document.getElementById(`gateAlertContainer-${dashName}`);
                    if (gateAlertContainer) {
                        gateAlertContainer.innerHTML = '';
                    }

                    // Dynamic scores update across subkomponen, komponen, and grand total
                    if (data.scores) {
                        // Grand Total Evaluasi
                        const gtEval = document.getElementById('cardGrandTotalEvaluasi');
                        if (gtEval) {
                            const val = data.scores.grand_total_evaluasi ?? data.scores.grand_total_pk;
                            gtEval.textContent = parseFloat(val).toFixed(2);
                        }

                        // Grand Total PM
                        const gtPm = document.getElementById('cardGrandTotalPm');
                        if (gtPm) gtPm.textContent = parseFloat(data.scores.grand_total_pm).toFixed(2);

                        // Grand Total default
                        const gt = document.getElementById('cardGrandTotal');
                        if (gt) gt.textContent = parseFloat(data.scores.grand_total_pm).toFixed(2);

                        // Subkomponen scores
                        for (const [sKode, subData] of Object.entries(data.scores.subkomponen)) {
                            const sDash = sKode.replace(/\./g, '-');
                            const subScoreEl = document.getElementById(`subScore-${sDash}`);
                            const activeSubScore = (tahap === 'pm') ? subData.pm_skor : (subData.evaluasi_skor ?? subData.pk_skor);
                            if (subScoreEl) subScoreEl.textContent = parseFloat(activeSubScore).toFixed(2);
                        }

                        // Komponen scores
                        for (const [kId, kData] of Object.entries(data.scores.komponen)) {
                            const scoreEl = document.getElementById(`kompScore-${kId}`);
                            const pmScoreEl = document.getElementById(`kompPmScore-${kId}`);
                            const evalScoreEl = document.getElementById(`kompEvalScore-${kId}`);
                            const headerScoreEl = document.getElementById(`headerKompScore-${kId}`);
                            const barEl = document.getElementById(`kompBar-${kId}`);
                            const percentEl = document.getElementById(`kompPercent-${kId}`);

                            const currentEvalScore = kData.evaluasi_skor ?? kData.pk_skor;
                            const currentEvalPercent = kData.evaluasi_persentase ?? kData.pk_persentase;

                            const activeScore = (tahap === 'pm') ? kData.pm_skor : currentEvalScore;
                            const activePercent = (tahap === 'pm') ? kData.pm_persentase : currentEvalPercent;

                            if (scoreEl) scoreEl.textContent = parseFloat(activeScore).toFixed(2);
                            if (pmScoreEl) pmScoreEl.textContent = parseFloat(kData.pm_skor).toFixed(2);
                            if (evalScoreEl) evalScoreEl.textContent = parseFloat(currentEvalScore).toFixed(2);
                            if (headerScoreEl) headerScoreEl.textContent = parseFloat(activeScore).toFixed(2);
                            if (barEl) barEl.style.width = `${activePercent}%`;
                            if (percentEl) percentEl.textContent = `${activePercent}%`;
                        }
                    }
                } else {
                    showToast(data.message || 'Gagal menyimpan gradasi.', false);
                }
            })
            .catch(err => {
                console.error(err);
                button.disabled = false;
                button.innerHTML = originalBtnHtml;
                showToast('Terjadi kesalahan jaringan saat menyimpan gradasi.', false);
            });
        });
    });

    // Save Criteria Score AJAX Handler
    document.querySelectorAll('.btn-save-kriteria').forEach(btn => {
        btn.addEventListener('click', function () {
            const kriteriaKode = this.getAttribute('data-kriteria');
            const safeName = kriteriaKode.replace(/\./g, '_');
            const tahap = this.getAttribute('data-tahap') || '{{ $currentTahap }}';

            // Find selected radio by tahap and safeName
            const selectedRadio = document.querySelector(`input[name="param_${tahap}_${safeName}"]:checked`)
                || document.querySelector(`input[name="param_evaluasi_${safeName}"]:checked`)
                || document.querySelector(`input[name="param_pm_${safeName}"]:checked`)
                || document.querySelector(`input[name="param_${safeName}"]:checked`);

            if (!selectedRadio) {
                showToast('Harap pilih salah satu parameter nilai terlebih dahulu.', false);
                return;
            }

            const parameterId = selectedRadio.value;
            const nilaiParam = selectedRadio.dataset.nilai;
            const catatanInput = document.getElementById(`catatan_${safeName}`);
            const catatan = catatanInput ? catatanInput.value.trim() : '';

            // Validasi: Kriteria yang dipilih "Tidak Terpenuhi" wajib mengisi kotak Uraian/Penjelasan
            if ((nilaiParam === 'Tidak Terpenuhi' || parseFloat(selectedRadio.dataset.skor) === 0) && !catatan) {
                showToast('Kriteria yang dipilih <strong>"Tidak Terpenuhi"</strong> wajib mengisi kotak <strong>Uraian/Penjelasan</strong> baru bisa disimpan.', false);
                if (catatanInput) {
                    catatanInput.classList.add('is-invalid', 'border-danger');
                    catatanInput.focus();
                    catatanInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            if (catatanInput) {
                catatanInput.classList.remove('is-invalid', 'border-danger');
            }

            const idSatker = '{{ $selectedSatker }}';
            const tahun = '{{ $tahun }}';

            const button = this;
            button.disabled = true;
            const originalBtnHtml = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch('{{ route("lke.evaluasi.save_score") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_satker: idSatker,
                    tahun: tahun,
                    kriteria_id: kriteriaKode,
                    parameter_id: parameterId,
                    catatan: catatan,
                    tahap: tahap
                })
            })
            .then(res => res.json())
            .then(data => {
                button.disabled = false;
                button.innerHTML = originalBtnHtml;

                if (data.success) {
                    showToast(data.message, true);

                    // Update criteria badge on right column
                    const safeKode = kriteriaKode.replace(/\./g, '-');
                    const badge = document.getElementById(`badgeScore-${safeKode}`);
                    if (badge) {
                        const nilai = (tahap === 'pm') ? data.penilaian.pm_nilai : data.penilaian.nilai;
                        const skor = (tahap === 'pm') ? data.penilaian.pm_skor : data.penilaian.skor;
                        const isTerpenuhi = nilai === 'Terpenuhi';
                        badge.className = `badge ${isTerpenuhi ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'} current-score-badge text-xs`;
                        
                        const labelTahap = (tahap === 'pm') ? 'Nilai PM' : 'Hasil Evaluasi';
                        badge.innerHTML = `${labelTahap}: ${nilai} (Skor: ${parseFloat(skor).toFixed(2)})`;
                    }

                    // Dynamic hierarchical scores update & Gate locking/unlocking
                    if (data.scores) {
                        // Grand Total Evaluasi
                        const gtEval = document.getElementById('cardGrandTotalEvaluasi');
                        if (gtEval) {
                            const val = data.scores.grand_total_evaluasi ?? data.scores.grand_total_pk;
                            gtEval.textContent = parseFloat(val).toFixed(2);
                        }

                        // Grand Total PM
                        const gtPm = document.getElementById('cardGrandTotalPm');
                        if (gtPm) gtPm.textContent = parseFloat(data.scores.grand_total_pm).toFixed(2);

                        // Grand Total generic
                        const gt = document.getElementById('cardGrandTotal');
                        if (gt) gt.textContent = parseFloat(data.scores.grand_total_pm).toFixed(2);

                        // Evaluated counts
                        const evCnt = document.getElementById('progressEvaluated');
                        if (evCnt) {
                            const evalCount = data.scores.evaluasi_evaluated_cnt ?? data.scores.pk_evaluated_cnt;
                            evCnt.textContent = (tahap === 'pm') ? data.scores.pm_evaluated_cnt : evalCount;
                        }
                        const evPmCnt = document.getElementById('progressPmEvaluated');
                        if (evPmCnt) evPmCnt.textContent = data.scores.pm_evaluated_cnt;

                        const evEvalCnt = document.getElementById('progressEvalCount');
                        if (evEvalCnt) {
                            evEvalCnt.textContent = data.scores.evaluasi_evaluated_cnt ?? data.scores.pk_evaluated_cnt;
                        }

                        // Komponen scores
                        for (const [kId, kData] of Object.entries(data.scores.komponen)) {
                            const scoreEl = document.getElementById(`kompScore-${kId}`);
                            const pmScoreEl = document.getElementById(`kompPmScore-${kId}`);
                            const evalScoreEl = document.getElementById(`kompEvalScore-${kId}`);
                            const headerScoreEl = document.getElementById(`headerKompScore-${kId}`);
                            const barEl = document.getElementById(`kompBar-${kId}`);
                            const percentEl = document.getElementById(`kompPercent-${kId}`);

                            const currentEvalScore = kData.evaluasi_skor ?? kData.pk_skor;
                            const currentEvalPercent = kData.evaluasi_persentase ?? kData.pk_persentase;

                            const activeScore = (tahap === 'pm') ? kData.pm_skor : currentEvalScore;
                            const activePercent = (tahap === 'pm') ? kData.pm_persentase : currentEvalPercent;

                            if (scoreEl) scoreEl.textContent = parseFloat(activeScore).toFixed(2);
                            if (pmScoreEl) pmScoreEl.textContent = parseFloat(kData.pm_skor).toFixed(2);
                            if (evalScoreEl) evalScoreEl.textContent = parseFloat(currentEvalScore).toFixed(2);
                            if (headerScoreEl) headerScoreEl.textContent = parseFloat(activeScore).toFixed(2);
                            if (barEl) barEl.style.width = `${activePercent}%`;
                            if (percentEl) percentEl.textContent = `${activePercent}%`;
                        }

                        // Subkomponen scores, badges, and gate status
                        for (const [subKode, subData] of Object.entries(data.scores.subkomponen)) {
                            const safeSub = subKode.replace(/\./g, '-');
                            const safeSubUnderscore = subKode.replace(/\./g, '_');
                            const subScoreEl = document.getElementById(`subScore-${safeSub}`);
                            const activeSubScore = (tahap === 'pm') ? subData.pm_skor : (subData.evaluasi_skor ?? subData.pk_skor);
                            if (subScoreEl) subScoreEl.textContent = parseFloat(activeSubScore).toFixed(2);

                            // Subkomponen evaluated count badge
                            const subKriteriaBadge = document.getElementById(`subKriteriaBadge-${safeSub}`);
                            const evalCountSub = (tahap === 'pm') ? subData.pm_evaluated_cnt : (subData.evaluasi_evaluated_cnt ?? subData.pk_evaluated_cnt);
                            if (subKriteriaBadge) {
                                subKriteriaBadge.textContent = `${evalCountSub} / ${subData.total_cnt} Kriteria`;
                            }

                            // Dynamic gate & koefisien elements
                            const isComplete = (tahap === 'pm') ? subData.is_100_pm : subData.is_100_eval;
                            const effKoef = (tahap === 'pm') ? subData.koefisien_pm : subData.koefisien_evaluasi;
                            const gradLabel = (tahap === 'pm') ? subData.gradasi_pm : subData.gradasi_eval;
                            const terpCount = (tahap === 'pm') ? subData.terpenuhi_pm_cnt : subData.terpenuhi_eval_cnt;
                            const totCount = subData.total_cnt;
                            const needsGrad = (tahap === 'pm') ? subData.pm_needs_gradasi : subData.eval_needs_gradasi;

                            // Update Koef badges
                            const higherClass = (effKoef == 100) ? 'bg-success' : (effKoef == 90 ? 'bg-info text-dark' : 'bg-secondary');
                            const valActiveKoef = document.getElementById(`valActiveKoef-${safeSub}`);
                            const badgeActiveKoef = document.getElementById(`badgeActiveKoef-${safeSub}`);
                            const subKoefVal = document.getElementById(`subKoefVal-${safeSub}`);
                            const subKoefBadge = document.getElementById(`subKoefBadge-${safeSub}`);

                            if (valActiveKoef) valActiveKoef.textContent = `${effKoef}%`;
                            if (subKoefVal) subKoefVal.textContent = `${effKoef}%`;
                            if (badgeActiveKoef) {
                                badgeActiveKoef.className = `badge ${higherClass} text-xs rounded-pill px-3 py-1 shadow-sm`;
                                badgeActiveKoef.innerHTML = `Koefisien Aktif: <strong id="valActiveKoef-${safeSub}">${effKoef}%</strong> (${gradLabel})`;
                            }
                            if (subKoefBadge) {
                                subKoefBadge.className = `badge ${higherClass} text-xs`;
                                subKoefBadge.innerHTML = `Koef: <strong id="subKoefVal-${safeSub}">${effKoef}%</strong> (${gradLabel})`;
                            }

                            // Update Gate UI (locked / unlocked state)
                            const statusGate = document.getElementById(`statusGate-${safeSub}`);
                            const statusGateText = document.getElementById(`statusGateText-${safeSub}`);
                            const gateAlertContainer = document.getElementById(`gateAlertContainer-${safeSub}`);
                            const gradFields = document.getElementById(`gradasiFields-${safeSub}`);
                            const saveKoefBtn = document.getElementById(`btnSaveKoef-${safeSub}`);

                            if (!isComplete) {
                                // Locked State
                                if (statusGate) {
                                    statusGate.className = 'badge bg-secondary-subtle text-muted';
                                    statusGate.innerHTML = `<i class="bi bi-lock-fill me-1"></i> <span id="statusGateText-${safeSub}">Terkunci (${terpCount}/${totCount} Terpenuhi)</span>`;
                                }
                                if (gradFields) gradFields.classList.add('opacity-50', 'pe-none');
                                if (saveKoefBtn) saveKoefBtn.disabled = true;

                                // Disable radio buttons & set to BB
                                document.querySelectorAll(`input[name="gradasi_${tahap}_${safeSubUnderscore}"]`).forEach(r => {
                                    r.disabled = true;
                                    if (r.value === 'BB') r.checked = true;
                                });

                                if (gateAlertContainer) {
                                    gateAlertContainer.innerHTML = `
                                        <div class="alert alert-secondary py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0 bg-light" id="gateAlert-${safeSub}">
                                            <i class="bi bi-shield-lock-fill text-secondary fs-6"></i>
                                            <div><strong>Form Gradasi Terkunci (Koefisien Default 80%):</strong> Sub komponen belum memenuhi seluruh kriteria (baru <strong>${terpCount}</strong> dari <strong>${totCount}</strong> kriteria terpenuhi). Form gradasi baru dapat diisi jika seluruh kriteria 100% Terpenuhi.</div>
                                        </div>
                                    `;
                                }
                            } else {
                                // Unlocked State
                                if (statusGate) {
                                    statusGate.className = 'badge bg-success-subtle text-success';
                                    statusGate.innerHTML = `<i class="bi bi-unlock-fill me-1"></i> <span id="statusGateText-${safeSub}">Gerbang Terbuka (100% Terpenuhi)</span>`;
                                }
                                if (gradFields) gradFields.classList.remove('opacity-50', 'pe-none');
                                if (saveKoefBtn) saveKoefBtn.disabled = false;

                                // Enable radio buttons
                                document.querySelectorAll(`input[name="gradasi_${tahap}_${safeSubUnderscore}"]`).forEach(r => {
                                    r.disabled = false;
                                });

                                if (gateAlertContainer) {
                                    if (needsGrad) {
                                        gateAlertContainer.innerHTML = `
                                            <div class="alert alert-warning py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0" id="gateAlert-${safeSub}">
                                                <i class="bi bi-exclamation-triangle-fill text-warning fs-6"></i>
                                                <div><strong>Kriteria 100% Terpenuhi!</strong> Harap pilih opsi Gradasi Nilai (BB, A, atau AA) untuk sub komponen ini.</div>
                                            </div>
                                        `;
                                    } else {
                                        gateAlertContainer.innerHTML = '';
                                    }
                                }
                            }
                        }
                    }
                } else {
                    showToast(data.message || 'Gagal menyimpan nilai.', false);
                }
            })
            .catch(err => {
                console.error(err);
                button.disabled = false;
                button.innerHTML = originalBtnHtml;
                showToast('Terjadi kesalahan jaringan saat menyimpan nilai.', false);
            });
        });
    });

    // ============================================================
    // KONTROL EXPAND & COLLAPSE HIERARKI SUBKOMPONEN DAN KRITERIA
    // ============================================================

    // Toggle Semua Subkomponen di dalam Komponen
    document.querySelectorAll('.btn-toggle-sub-all').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const kompId = this.dataset.targetKomp;
            const kompCollapse = document.getElementById('collapseKomp-' + kompId);
            if (!kompCollapse) return;

            const subCollapses = kompCollapse.querySelectorAll('.lke-subkomp-collapse');
            const anyOpen = Array.from(subCollapses).some(c => c.classList.contains('show'));

            subCollapses.forEach(c => {
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(c, { toggle: false });
                if (anyOpen) {
                    bsCollapse.hide();
                } else {
                    bsCollapse.show();
                }
            });

            const icon = this.querySelector('i');
            const span = this.querySelector('.btn-text');
            if (anyOpen) {
                if (icon) icon.className = 'bi bi-arrows-expand me-1';
                if (span) span.textContent = 'Buka Semua Subkomponen';
            } else {
                if (icon) icon.className = 'bi bi-arrows-collapse me-1';
                if (span) span.textContent = 'Tutup Semua Subkomponen';
            }
        });
    });

    // Toggle Semua Kriteria di dalam Subkomponen
    document.querySelectorAll('.btn-toggle-crit-all').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const subDash = this.dataset.targetSub;
            const list = document.getElementById('critList-' + subDash);
            if (!list) return;

            const critCollapses = list.querySelectorAll('.lke-crit-collapse');
            const anyOpen = Array.from(critCollapses).some(c => c.classList.contains('show'));

            critCollapses.forEach(c => {
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(c, { toggle: false });
                if (anyOpen) {
                    bsCollapse.hide();
                } else {
                    bsCollapse.show();
                }
            });

            const icon = this.querySelector('i');
            const span = this.querySelector('.btn-text');
            if (anyOpen) {
                if (icon) icon.className = 'bi bi-arrows-expand me-1';
                if (span) span.textContent = 'Buka Semua Kriteria';
            } else {
                if (icon) icon.className = 'bi bi-arrows-collapse me-1';
                if (span) span.textContent = 'Tutup Semua Kriteria';
            }
        });
    });

    // Sinkronisasi rotasi icon chevron & class collapsed pada header
    document.addEventListener('show.bs.collapse', function (e) {
        const target = e.target;
        if (target.classList.contains('lke-subkomp-collapse')) {
            const header = target.closest('.lke-subkomp-card')?.querySelector('.lke-subkomp-header');
            if (header) header.classList.remove('collapsed');
        } else if (target.classList.contains('lke-crit-collapse')) {
            const header = target.closest('.lke-kriteria-card')?.querySelector('.lke-crit-header');
            if (header) header.classList.remove('collapsed');
        }
    });

    document.addEventListener('hide.bs.collapse', function (e) {
        const target = e.target;
        if (target.classList.contains('lke-subkomp-collapse')) {
            const header = target.closest('.lke-subkomp-card')?.querySelector('.lke-subkomp-header');
            if (header) header.classList.add('collapsed');
        } else if (target.classList.contains('lke-crit-collapse')) {
            const header = target.closest('.lke-kriteria-card')?.querySelector('.lke-crit-header');
            if (header) header.classList.add('collapsed');
        }
    });
});
</script>
@endpush
@endsection
