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
                                <div class="mt-1">
                                    <span class="badge {{ $scores['predikat_evaluasi']['badge'] ?? 'bg-secondary text-white' }} text-xs" id="badgePredikatEvaluasi">
                                        Predikat {{ $scores['predikat_evaluasi']['predikat'] ?? '-' }} ({{ $scores['predikat_evaluasi']['keterangan'] ?? '-' }})
                                    </span>
                                </div>
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
                                <div class="mt-1">
                                    <span class="badge {{ $scores['predikat_pm']['badge'] ?? 'bg-secondary text-white' }} text-xs" id="badgePredikatPm">
                                        Predikat {{ $scores['predikat_pm']['predikat'] ?? '-' }} ({{ $scores['predikat_pm']['keterangan'] ?? '-' }})
                                    </span>
                                </div>
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
                                <div class="mt-1">
                                    <span class="badge {{ $scores['predikat_pm']['badge'] ?? 'bg-secondary text-white' }} text-xs" id="badgePredikatPm">
                                        Predikat {{ $scores['predikat_pm']['predikat'] ?? '-' }} ({{ $scores['predikat_pm']['keterangan'] ?? '-' }})
                                    </span>
                                </div>
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
                                <div class="mt-1">
                                    <span class="badge {{ $scores['predikat_evaluasi']['badge'] ?? 'bg-secondary text-white' }} text-xs" id="badgePredikatEvaluasi">
                                        Predikat {{ $scores['predikat_evaluasi']['predikat'] ?? '-' }} ({{ $scores['predikat_evaluasi']['keterangan'] ?? '-' }})
                                    </span>
                                </div>
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
                                    $koefEvalGradasi = $subScoreData['gradasi_eval'] ?? (($koefData && $isCompleteEval) ? ($koefData->gradasi_label ?? 'BB') : 'BB');
                                    $koefEvalUraian = $koefData ? $koefData->uraian : '';
                                    $koefEvalBukti = $koefData ? ($koefData->bukti_dukung ?? '') : '';
                                    $koefEvalLink = $koefData ? ($koefData->link_bukti_dukung ?? '') : '';

                                    // PM koef state
                                    $hasKoefPm = $koefData && !is_null($koefData->pm_nilai);
                                    $koefPmGradasi = $subScoreData['gradasi_pm'] ?? (($koefData && $isCompletePm) ? ($koefData->pm_gradasi_label ?? 'BB') : 'BB');
                                    $koefPmUraian = $koefData ? $koefData->pm_uraian : '';
                                    $koefPmBukti = $koefData ? ($koefData->pm_bukti_dukung ?? '') : '';
                                    $koefPmLink = $koefData ? ($koefData->pm_link_bukti_dukung ?? '') : '';

                                    $currentGradasi = $isModeEvaluasi ? $koefEvalGradasi : $koefPmGradasi;
                                    $currentBadgeClass = \App\Models\LkeSubkomponenKoefisien::getGradasiBadgeClass($currentGradasi);
                                    $currentPemenuhanPersen = $isModeEvaluasi 
                                        ? ($subScoreData['pemenuhan_eval_persen'] ?? ($subTotalCnt > 0 ? round(($subEvalTerpenuhi / $subTotalCnt) * 100, 1) : 0))
                                        : ($subScoreData['pemenuhan_pm_persen'] ?? ($subTotalCnt > 0 ? round(($subPmTerpenuhi / $subTotalCnt) * 100, 1) : 0));
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
                                            <span class="badge {{ $currentBadgeClass }} text-xs" id="subKoefBadge-{{ $subDashKode }}">
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

                                @if($isSubKeberadaan)
                                <!-- SECTION GRADASI NILAI SUBKOMPONEN (Khusus Subkomponen Keberadaan X.A) -->
                                <div class="card border border-primary-subtle shadow-sm rounded-3 mb-3 bg-white" id="cardKoefisien-{{ $subDashKode }}">
                                    <div class="card-header bg-light py-2 px-3 d-flex flex-wrap justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-sliders text-primary"></i>
                                            <strong class="text-xs text-dark">Section Gradasi Nilai Subkomponen {{ $sub->kode }}</strong>
                                            <span class="text-xs text-muted" id="rumusInfo-{{ $subDashKode }}">(Rumus: Koefisien Kluster {{ $activeKoef }}% × Bobot {{ number_format($subScoreData['bobot'] ?? $sub->bobot, 1) }})</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-1 mt-md-0">
                                            <span class="badge {{ $currentBadgeClass }} text-xs rounded-pill px-3 py-1 shadow-sm" id="badgeActiveKoef-{{ $subDashKode }}">
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
                                                        <span class="badge {{ $currentIsComplete ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary border border-primary-subtle' }}" id="statusGate-{{ $subDashKode }}">
                                                            <i class="bi {{ $currentIsComplete ? 'bi-unlock-fill' : 'bi-diagram-3-fill' }} me-1"></i>
                                                            <span id="statusGateText-{{ $subDashKode }}">
                                                                {{ $currentIsComplete ? 'Gerbang Terbuka (100% Terpenuhi)' : 'Kluster ' . $currentGradasi . ' (' . $activeKoef . '%) • ' . $currentTerpenuhi . '/' . $subTotalCnt . ' Terpenuhi' }}
                                                            </span>
                                                        </span>
                                                    </div>

                                                    <!-- Gate Alert Banner -->
                                                    <div id="gateAlertContainer-{{ $subDashKode }}">
                                                        @if(!$currentIsComplete)
                                                            <div class="alert alert-info py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0 bg-info-subtle text-info-emphasis" id="gateAlert-{{ $subDashKode }}">
                                                                <i class="bi bi-diagram-3-fill fs-6 text-primary"></i>
                                                                <div><strong>Kluster Otomatis:</strong> Pemenuhan <strong>{{ $currentTerpenuhi }}</strong> dari <strong>{{ $subTotalCnt }}</strong> kriteria (<strong>{{ $currentPemenuhanPersen }}%</strong>) masuk ke <strong class="badge {{ $currentBadgeClass }} px-2 py-1 ms-1">Gradasi {{ $currentGradasi }} ({{ $activeKoef }}%)</strong>. Form pengajuan Gradasi Lanjutan (A / AA) terbuka jika seluruh kriteria 100% Terpenuhi.</div>
                                                            </div>
                                                        @elseif($currentNeedsGradasi)
                                                            <div class="alert alert-warning py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0" id="gateAlert-{{ $subDashKode }}">
                                                                <i class="bi bi-exclamation-triangle-fill text-warning fs-6"></i>
                                                                <div><strong>Kriteria 100% Terpenuhi!</strong> Harap pilih opsi Gradasi Nilai (BB, A, atau AA) untuk sub komponen ini.</div>
                                                            </div>
                                                        @else
                                                            <div class="alert alert-success py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0 bg-success-subtle text-success" id="gateAlert-{{ $subDashKode }}">
                                                                <i class="bi bi-check-circle-fill fs-6"></i>
                                                                <div><strong>Kriteria 100% Terpenuhi!</strong> Gradasi aktif saat ini: <strong class="badge {{ $currentBadgeClass }} px-2 py-1 ms-1">Gradasi {{ $currentGradasi }} ({{ $activeKoef }}%)</strong>.</div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Visual Guide Rentang Kluster -->
                                                    <div class="p-2 mb-2 rounded bg-light border text-xs">
                                                        <div class="text-muted fw-semibold mb-1 d-flex justify-content-between align-items-center" style="font-size: 0.72rem;">
                                                            <span><i class="bi bi-info-circle me-1"></i> Standar Klusterisasi Koefisien:</span>
                                                            <span class="text-xs">Aktif: <strong class="badge {{ $currentBadgeClass }}" id="guideActiveBadge-{{ $subDashKode }}">{{ $currentGradasi }} ({{ $activeKoef }}%)</strong></span>
                                                        </div>
                                                        <div class="d-flex flex-wrap gap-1 align-items-center" style="font-size: 0.7rem;">
                                                            <span class="badge {{ $currentGradasi === 'E' ? 'bg-secondary text-white' : 'bg-light text-muted border' }}">E: 0% (0)</span>
                                                            <span class="badge {{ $currentGradasi === 'D' ? 'bg-danger text-white' : 'bg-light text-muted border' }}">D: &gt;0-25% (30%)</span>
                                                            <span class="badge {{ $currentGradasi === 'C' ? 'bg-warning text-dark' : 'bg-light text-muted border' }}">C: &gt;25-50% (50%)</span>
                                                            <span class="badge {{ $currentGradasi === 'CC' ? 'bg-warning-subtle text-dark border' : 'bg-light text-muted border' }}">CC: &gt;50-75% (60%)</span>
                                                            <span class="badge {{ $currentGradasi === 'B' ? 'bg-primary-subtle text-primary border' : 'bg-light text-muted border' }}">B: &gt;75-&lt;100% (70%)</span>
                                                            <span class="badge {{ $currentGradasi === 'BB' ? 'bg-primary text-white' : 'bg-light text-muted border' }}">BB: 100% (80%)</span>
                                                        </div>
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
                                @endif

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
                                                        $hasEvaluasi = $eval && (!is_null($eval->parameter_id) || !empty($eval->nilai));
                                                        $evalNilai = $hasEvaluasi ? ($eval->nilai ?? ($eval->skor == 1 ? 'Terpenuhi' : 'Tidak Terpenuhi')) : null;
                                                        $evalSkor = $hasEvaluasi ? (float)$eval->skor : 0.00;
                                                        $evalCatatan = $hasEvaluasi ? $eval->catatan : '';

                                                        // Status Penilaian Mandiri (PM Satker)
                                                        $hasPm = $eval && (!is_null($eval->pm_parameter_id) || !empty($eval->pm_nilai));
                                                        $pmNilai = $hasPm ? ($eval->pm_nilai ?? ($eval->pm_skor == 1 ? 'Terpenuhi' : 'Tidak Terpenuhi')) : null;
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

                                                        @if(!empty($cr->langkah_kerja))
                                                            <div class="mt-2 p-2 bg-light-subtle rounded-2 border border-secondary-subtle">
                                                                <div class="text-xs fw-bold text-secondary mb-1 d-flex align-items-center">
                                                                    <i class="bi bi-list-task text-primary me-1"></i> Langkah Kerja:
                                                                </div>
                                                                <div class="text-xs text-dark" style="white-space: pre-line; line-height: 1.5;">{!! nl2br(e($cr->langkah_kerja)) !!}</div>
                                                            </div>
                                                        @endif

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
                                                                        $isSelected = $hasEvaluasi && ($eval->parameter_id == $p->id || (empty($eval->parameter_id) && $eval->nilai === $p->nilai) || ($eval->nilai === $p->nilai && !empty($eval->nilai)));
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
                                                                        $isSelected = $hasPm && ($eval->pm_parameter_id == $p->id || (empty($eval->pm_parameter_id) && $eval->pm_nilai === $p->nilai) || ($eval->pm_nilai === $p->nilai && !empty($eval->pm_nilai)));
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

                            @if(str_ends_with($sub->kode, '.C'))
                                @php
                                    $compPrefix = explode('.', $sub->kode)[0];
                                    $pairCodeB = "{$compPrefix}.B";
                                    $pairCodeC = "{$compPrefix}.C";
                                    $pairDashKode = "{$compPrefix}-BC";
                                    $pairSafeKode = "{$compPrefix}_BC";

                                    $scoreDataB = $scores['subkomponen'][$pairCodeB] ?? [];
                                    $scoreDataC = $scores['subkomponen'][$pairCodeC] ?? [];

                                    $subTotalCntB = $scoreDataB['total_cnt'] ?? 0;
                                    $subTotalCntC = $scoreDataC['total_cnt'] ?? 0;

                                    $subEvalTerpenuhiB = $scoreDataB['terpenuhi_eval_cnt'] ?? 0;
                                    $subEvalTerpenuhiC = $scoreDataC['terpenuhi_eval_cnt'] ?? 0;

                                    $subPmTerpenuhiB = $scoreDataB['terpenuhi_pm_cnt'] ?? 0;
                                    $subPmTerpenuhiC = $scoreDataC['terpenuhi_pm_cnt'] ?? 0;

                                    $isCompleteEvalB = $scoreDataB['is_100_eval'] ?? false;
                                    $isCompleteEvalC = $scoreDataC['is_100_eval'] ?? false;
                                    $isPairCompleteEval = $isCompleteEvalB && $isCompleteEvalC;

                                    $isCompletePmB = $scoreDataB['is_100_pm'] ?? false;
                                    $isCompletePmC = $scoreDataC['is_100_pm'] ?? false;
                                    $isPairCompletePm = $isCompletePmB && $isCompletePmC;

                                    $currentPairIsComplete = $isModeEvaluasi ? $isPairCompleteEval : $isPairCompletePm;
                                    $currentPairTerpenuhiB = $isModeEvaluasi ? $subEvalTerpenuhiB : $subPmTerpenuhiB;
                                    $currentPairTerpenuhiC = $isModeEvaluasi ? $subEvalTerpenuhiC : $subPmTerpenuhiC;

                                    $currentPairPemenuhanB = $isModeEvaluasi 
                                        ? ($scoreDataB['pemenuhan_eval_persen'] ?? ($subTotalCntB > 0 ? round(($subEvalTerpenuhiB / $subTotalCntB) * 100, 1) : 0))
                                        : ($scoreDataB['pemenuhan_pm_persen'] ?? ($subTotalCntB > 0 ? round(($subPmTerpenuhiB / $subTotalCntB) * 100, 1) : 0));
                                    $currentPairPemenuhanC = $isModeEvaluasi 
                                        ? ($scoreDataC['pemenuhan_eval_persen'] ?? ($subTotalCntC > 0 ? round(($subEvalTerpenuhiC / $subTotalCntC) * 100, 1) : 0))
                                        : ($scoreDataC['pemenuhan_pm_persen'] ?? ($subTotalCntC > 0 ? round(($subPmTerpenuhiC / $subTotalCntC) * 100, 1) : 0));

                                    // Koefisien records: cek record C atau B
                                    $koefDataPair = $subkomponenKoefisien->get($pairCodeC) ?: $subkomponenKoefisien->get($pairCodeB);

                                    $hasKoefEvalPair = $koefDataPair && !is_null($koefDataPair->nilai);
                                    $koefEvalGradasiPair = $scoreDataC['gradasi_eval'] ?? (($koefDataPair && $isPairCompleteEval) ? ($koefDataPair->gradasi_label ?? 'BB') : 'BB');
                                    $koefEvalUraianPair = $koefDataPair ? $koefDataPair->uraian : '';
                                    $koefEvalBuktiPair = $koefDataPair ? ($koefDataPair->bukti_dukung ?? '') : '';
                                    $koefEvalLinkPair = $koefDataPair ? ($koefDataPair->link_bukti_dukung ?? '') : '';

                                    $hasKoefPmPair = $koefDataPair && !is_null($koefDataPair->pm_nilai);
                                    $koefPmGradasiPair = $scoreDataC['gradasi_pm'] ?? (($koefDataPair && $isPairCompletePm) ? ($koefDataPair->pm_gradasi_label ?? 'BB') : 'BB');
                                    $koefPmUraianPair = $koefDataPair ? $koefDataPair->pm_uraian : '';
                                    $koefPmBuktiPair = $koefDataPair ? ($koefDataPair->pm_bukti_dukung ?? '') : '';
                                    $koefPmLinkPair = $koefDataPair ? ($koefDataPair->pm_link_bukti_dukung ?? '') : '';

                                    $currentPairGradasi = $isModeEvaluasi ? $koefEvalGradasiPair : $koefPmGradasiPair;
                                    $currentPairBadgeClass = \App\Models\LkeSubkomponenKoefisien::getGradasiBadgeClass($currentPairGradasi);

                                    $activePairKoef = $isModeEvaluasi 
                                        ? ($scoreDataC['koefisien_evaluasi'] ?? 80)
                                        : ($scoreDataC['koefisien_pm'] ?? 80);

                                    $currentPairUraian = $isModeEvaluasi ? $koefEvalUraianPair : $koefPmUraianPair;
                                    $currentPairHasFile = $isModeEvaluasi ? !empty($koefEvalLinkPair) : !empty($koefPmLinkPair);

                                    $currentPairNeedsGradasi = $isModeEvaluasi 
                                        ? ($isPairCompleteEval && (!$koefDataPair || empty($koefDataPair->nilai)))
                                        : ($isPairCompletePm && (!$koefDataPair || empty($koefDataPair->pm_nilai)));
                                @endphp

                                <!-- SECTION GRADASI NILAI GABUNGAN SUBKOMPONEN X.B & X.C -->
                                <div class="card border border-primary shadow-sm rounded-3 mt-4 mb-2 bg-white" id="cardKoefisienPair-{{ $pairDashKode }}">
                                    <div class="card-header bg-primary-subtle py-2 px-3 d-flex flex-wrap justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-sliders2-vertical text-primary fs-6"></i>
                                            <div>
                                                <strong class="text-xs text-dark">Section Gradasi Nilai Gabungan Subkomponen {{ $pairCodeB }} & {{ $pairCodeC }}</strong>
                                                <div class="text-muted" style="font-size: 0.72rem;" id="rumusInfoPair-{{ $pairDashKode }}">
                                                    Rumus: Koefisien Kluster {{ $activePairKoef }}% × Bobot ({{ $pairCodeB }}: {{ number_format($scoreDataB['bobot'] ?? 0, 1) }} & {{ $pairCodeC }}: {{ number_format($scoreDataC['bobot'] ?? 0, 1) }})
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-1 mt-md-0">
                                            <span class="badge {{ $currentPairBadgeClass }} text-xs rounded-pill px-3 py-1 shadow-sm" id="badgeActiveKoefPair-{{ $pairDashKode }}">
                                                Koefisien Aktif: <strong id="valActiveKoefPair-{{ $pairDashKode }}">{{ $activePairKoef }}%</strong> 
                                                ({{ $currentPairGradasi }})
                                            </span>
                                            <button class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKoefPair-{{ $pairDashKode }}" aria-expanded="true">
                                                <i class="bi bi-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="collapse show" id="collapseKoefPair-{{ $pairDashKode }}">
                                        <div class="card-body p-3">
                                            <!-- Status Pemenuhan Kedua Subkomponen (B & C) -->
                                            <div class="p-2 mb-3 rounded-2 bg-light border d-flex flex-wrap justify-content-between align-items-center gap-2">
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <span class="text-xs fw-semibold text-muted">Status Pemenuhan Kriteria:</span>
                                                    <span class="badge {{ $subEvalTerpenuhiB === $subTotalCntB && $subTotalCntB > 0 ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }} text-xs" id="pairStatusB-{{ $pairDashKode }}">
                                                        {{ $pairCodeB }}: {{ $currentPairTerpenuhiB }}/{{ $subTotalCntB }} Terpenuhi ({{ $currentPairPemenuhanB }}%)
                                                    </span>
                                                    <span class="badge {{ $subEvalTerpenuhiC === $subTotalCntC && $subTotalCntC > 0 ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }} text-xs" id="pairStatusC-{{ $pairDashKode }}">
                                                        {{ $pairCodeC }}: {{ $currentPairTerpenuhiC }}/{{ $subTotalCntC }} Terpenuhi ({{ $currentPairPemenuhanC }}%)
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="badge {{ $currentPairIsComplete ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark border border-warning-subtle' }} text-xs" id="statusGatePair-{{ $pairDashKode }}">
                                                        @if($currentPairIsComplete)
                                                            <i class="bi bi-unlock-fill me-1"></i> Gerbang Terbuka (100% Terpenuhi)
                                                        @else
                                                            <i class="bi bi-lock-fill me-1 text-danger"></i> Gerbang Terkunci (Perlu 100% pada {{ $pairCodeB }} & {{ $pairCodeC }})
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

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
                                                                <span class="badge {{ $koefPmGradasiPair === 'AA' ? 'bg-success' : ($koefPmGradasiPair === 'A' ? 'bg-info text-dark' : 'bg-secondary') }} rounded-pill px-2 py-1 text-xs">
                                                                    Gradasi {{ $koefPmGradasiPair }} ({{ $koefDataPair ? $koefDataPair->effective_pm_koefisien : 80 }}%)
                                                                </span>
                                                            </div>
                                                            <div class="text-xs text-muted mb-2">
                                                                <strong>Status Usulan PM:</strong> {{ $hasKoefPmPair ? 'Gradasi ' . $koefPmGradasiPair . ' (' . ($koefDataPair ? $koefDataPair->effective_pm_koefisien : 80) . '%)' : 'Belum memilih gradasi (Default 80%)' }}
                                                            </div>
                                                            <div class="p-2 rounded bg-light border text-xs text-dark mb-2 font-monospace" style="min-height: 55px; white-space: pre-wrap;">
                                                                {{ $koefPmUraianPair ?: 'Satker tidak mencantumkan uraian penyesuaian gradasi.' }}
                                                            </div>
                                                            @if(!empty($koefPmLinkPair))
                                                                <div class="text-xs">
                                                                    <i class="bi bi-paperclip text-primary me-1"></i> Bukti Dukung PM:
                                                                    <a href="{{ asset($koefPmLinkPair) }}" target="_blank" class="fw-semibold text-primary text-decoration-underline">
                                                                        {{ $koefPmBuktiPair ?: 'Unduh Dokumen Bukti PM' }}
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div class="text-xs text-muted">
                                                                    <i class="bi bi-paperclip me-1"></i> Tidak ada file bukti dukung gradasi PM diunggah.
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <!-- Satker melihat hasil evaluasi gradasi dari Evaluator (JAMWAS / Kejati) -->
                                                        <div class="p-3 rounded-3 border border-success-subtle bg-white h-100">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="text-xs fw-bold text-success text-uppercase">
                                                                    <i class="bi bi-shield-check text-success me-1"></i> Hasil Evaluasi Gradasi ({{ $evaluatorName }}):
                                                                </span>
                                                                <span class="badge {{ $koefEvalGradasiPair === 'AA' ? 'bg-success' : ($koefEvalGradasiPair === 'A' ? 'bg-info text-dark' : 'bg-secondary') }} rounded-pill px-2 py-1 text-xs">
                                                                    Gradasi {{ $koefEvalGradasiPair }} ({{ $koefDataPair ? $koefDataPair->effective_koefisien : 80 }}%)
                                                                </span>
                                                            </div>
                                                            <div class="text-xs text-muted mb-2">
                                                                <strong>Penetapan Evaluator:</strong> {{ $hasKoefEvalPair ? 'Gradasi ' . $koefEvalGradasiPair . ' (' . ($koefDataPair ? $koefDataPair->effective_koefisien : 80) . '%)' : 'Mengikuti kluster pemenuhan kriteria' }}
                                                            </div>
                                                            <div class="p-2 rounded bg-light border text-xs text-dark mb-2 font-monospace" style="min-height: 55px; white-space: pre-wrap;">
                                                                {{ $koefEvalUraianPair ?: 'Evaluator belum memberikan catatan khusus terkait gradasi ini.' }}
                                                            </div>
                                                            @if(!empty($koefEvalLinkPair))
                                                                <div class="text-xs">
                                                                    <i class="bi bi-paperclip text-success me-1"></i> Bukti Evaluasi:
                                                                    <a href="{{ asset($koefEvalLinkPair) }}" target="_blank" class="fw-semibold text-success text-decoration-underline">
                                                                        {{ $koefEvalBuktiPair ?: 'Unduh Dokumen Bukti Evaluasi' }}
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div class="text-xs text-muted">
                                                                    <i class="bi bi-paperclip me-1"></i> Tidak ada file lampiran evaluasi.
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Kolom Kanan: Form Penentuan Gradasi Aktif Pasangan B & C -->
                                                <div class="col-lg-6">
                                                    <div class="p-3 rounded-3 border bg-white h-100 d-flex flex-column justify-content-between">
                                                        <div>
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="text-xs fw-bold text-dark text-uppercase">
                                                                    {{ $isModeEvaluasi ? 'Form Evaluasi Gradasi (' . ($evaluatorType === 'jamwas' ? 'JAMWAS' : 'Kejati') . '):' : 'Form Pengajuan Gradasi Satker (PM):' }}
                                                                </span>
                                                            </div>

                                                            <!-- Alert Status Gerbang Kunci -->
                                                            <div id="gateAlertContainerPair-{{ $pairDashKode }}">
                                                                @if(!$currentPairIsComplete)
                                                                    <div class="alert alert-info py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0 bg-info-subtle text-info-emphasis">
                                                                        <i class="bi bi-diagram-3-fill fs-6 text-primary"></i>
                                                                        <div>
                                                                            <strong>Status Pemenuhan:</strong> Subkomponen <strong>{{ $pairCodeB }}</strong> (<strong>{{ $currentPairTerpenuhiB }}/{{ $subTotalCntB }}</strong>) dan <strong>{{ $pairCodeC }}</strong> (<strong>{{ $currentPairTerpenuhiC }}/{{ $subTotalCntC }}</strong>). Form pengajuan Gradasi Lanjutan (A / AA) terbuka jika <strong>KEDUA</strong> subkomponen 100% Terpenuhi.
                                                                        </div>
                                                                    </div>
                                                                @elseif($currentPairNeedsGradasi)
                                                                    <div class="alert alert-warning py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0">
                                                                        <i class="bi bi-exclamation-triangle-fill text-warning fs-6"></i>
                                                                        <div><strong>Kriteria 100% Terpenuhi!</strong> Harap pilih opsi Gradasi Nilai (BB, A, atau AA) untuk pasangan Subkomponen {{ $pairCodeB }} & {{ $pairCodeC }}.</div>
                                                                    </div>
                                                                @else
                                                                    <div class="alert alert-success py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0 bg-success-subtle text-success">
                                                                        <i class="bi bi-check-circle-fill fs-6"></i>
                                                                        <div><strong>Kriteria 100% Terpenuhi!</strong> Gradasi aktif saat ini: <strong class="badge {{ $currentPairBadgeClass }} px-2 py-1 ms-1">Gradasi {{ $currentPairGradasi }} ({{ $activePairKoef }}%)</strong>.</div>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <!-- Container Pilihan Gradasi -->
                                                            <div id="gradasiFieldsPair-{{ $pairDashKode }}" class="{{ !$currentPairIsComplete ? 'opacity-50 pe-none' : '' }}">
                                                                <div class="text-xs fw-semibold text-muted mb-1">Pilih Gradasi Nilai Gabungan Subkomponen:</div>
                                                                
                                                                <div class="d-flex flex-column gap-2 mb-2">
                                                                    <!-- Opsi BB -->
                                                                    <div class="form-check p-2 rounded border bg-light">
                                                                        <input class="form-check-input radio-gradasi" 
                                                                               type="radio" 
                                                                               name="gradasi_{{ $currentTahap }}_{{ $pairSafeKode }}" 
                                                                               id="gradasi_bb_{{ $currentTahap }}_{{ $pairSafeKode }}" 
                                                                               value="BB" 
                                                                               data-pair="true"
                                                                               data-pair-prefix="{{ $compPrefix }}"
                                                                               data-subkomponen="{{ $compPrefix }}.BC"
                                                                               data-koef="80"
                                                                               {{ ($currentPairGradasi === 'BB' || empty($currentPairGradasi)) ? 'checked' : '' }}
                                                                               {{ !$currentPairIsComplete ? 'disabled' : '' }}>
                                                                        <label class="form-check-label text-xs cursor-pointer d-block" for="gradasi_bb_{{ $currentTahap }}_{{ $pairSafeKode }}">
                                                                            <div class="d-flex align-items-center justify-content-between">
                                                                                <strong class="text-dark"><span class="badge bg-secondary text-xs me-1">BB</span> Koefisien 80%</strong>
                                                                                <span class="badge bg-light text-muted border text-xs">Standar Pemenuhan</span>
                                                                            </div>
                                                                            <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                                                                Jika kualitas seluruh kriteria {{ $pairCodeB }} dan pemanfaatan {{ $pairCodeC }} telah terpenuhi (100%).
                                                                            </div>
                                                                        </label>
                                                                    </div>

                                                                    <!-- Opsi A -->
                                                                    <div class="form-check p-2 rounded border bg-light">
                                                                        <input class="form-check-input radio-gradasi" 
                                                                               type="radio" 
                                                                               name="gradasi_{{ $currentTahap }}_{{ $pairSafeKode }}" 
                                                                               id="gradasi_a_{{ $currentTahap }}_{{ $pairSafeKode }}" 
                                                                               value="A" 
                                                                               data-pair="true"
                                                                               data-pair-prefix="{{ $compPrefix }}"
                                                                               data-subkomponen="{{ $compPrefix }}.BC"
                                                                               data-koef="90"
                                                                               {{ $currentPairGradasi === 'A' ? 'checked' : '' }}
                                                                               {{ !$currentPairIsComplete ? 'disabled' : '' }}>
                                                                        <label class="form-check-label text-xs cursor-pointer d-block" for="gradasi_a_{{ $currentTahap }}_{{ $pairSafeKode }}">
                                                                            <div class="d-flex align-items-center justify-content-between">
                                                                                <strong class="text-info-emphasis"><span class="badge bg-info text-dark text-xs me-1">A</span> Koefisien 90%</strong>
                                                                                <span class="badge bg-info-subtle text-dark border text-xs">Kualitas & Pemanfaatan Teruji</span>
                                                                            </div>
                                                                            <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                                                                Jika kualitas {{ $pairCodeB }} dan pemanfaatan {{ $pairCodeC }} 100% terpenuhi, berkesinambungan, dan didukung bukti kinerja yang teruji.
                                                                            </div>
                                                                        </label>
                                                                    </div>

                                                                    <!-- Opsi AA -->
                                                                    <div class="form-check p-2 rounded border bg-light">
                                                                        <input class="form-check-input radio-gradasi" 
                                                                               type="radio" 
                                                                               name="gradasi_{{ $currentTahap }}_{{ $pairSafeKode }}" 
                                                                               id="gradasi_aa_{{ $currentTahap }}_{{ $pairSafeKode }}" 
                                                                               value="AA" 
                                                                               data-pair="true"
                                                                               data-pair-prefix="{{ $compPrefix }}"
                                                                               data-subkomponen="{{ $compPrefix }}.BC"
                                                                               data-koef="100"
                                                                               {{ $currentPairGradasi === 'AA' ? 'checked' : '' }}
                                                                               {{ !$currentPairIsComplete ? 'disabled' : '' }}>
                                                                        <label class="form-check-label text-xs cursor-pointer d-block" for="gradasi_aa_{{ $currentTahap }}_{{ $pairSafeKode }}">
                                                                            <div class="d-flex align-items-center justify-content-between">
                                                                                <strong class="text-success"><span class="badge bg-success text-xs me-1">AA</span> Koefisien 100%</strong>
                                                                                <span class="badge bg-success-subtle text-success border text-xs">Keunggulan Nasional</span>
                                                                            </div>
                                                                            <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                                                                Jika kualitas dan pemanfaatan 100% terpenuhi serta terdapat <strong>upaya inovatif yang layak menjadi percontohan di tingkat nasional</strong>.
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <!-- Kotak Input Uraian / Penjelasan Gabungan -->
                                                                <div class="mb-2">
                                                                    <label class="text-xs fw-semibold text-muted mb-1">
                                                                        <i class="bi bi-chat-left-text me-1"></i> Uraian / Penjelasan <span class="text-danger req-star-uraian-pair-{{ $pairDashKode }} {{ ($currentPairGradasi === 'BB' || empty($currentPairGradasi)) ? 'd-none' : '' }}">(Wajib diisi untuk A / AA)*</span>:
                                                                    </label>
                                                                    <textarea class="form-control form-control-sm text-sm input-uraian-koef" 
                                                                              id="uraian_koef_{{ $currentTahap }}_{{ $pairSafeKode }}" 
                                                                              rows="2" 
                                                                              placeholder="Tuliskan uraian dan penjelasan pemenuhan kriteria gradasi kualitas & pemanfaatan..." 
                                                                              style="resize: vertical;">{{ $currentPairUraian }}</textarea>
                                                                </div>

                                                                <!-- Input Bukti Dukung (File Upload - Opsional) -->
                                                                <div class="mb-2">
                                                                    <label class="text-xs fw-semibold text-muted mb-1">
                                                                        <i class="bi bi-paperclip me-1"></i> Upload Bukti Dukung Gabungan (Opsional):
                                                                    </label>
                                                                    <input type="file" 
                                                                           class="form-control form-control-sm text-xs mb-1" 
                                                                           id="file_koef_{{ $currentTahap }}_{{ $pairSafeKode }}">
                                                                    @if($currentPairHasFile)
                                                                        <div class="text-xs text-success" id="fileStatusPair-{{ $pairDashKode }}">
                                                                            <i class="bi bi-check-circle-fill me-1"></i> File telah terunggah sebelumnya. Unggah lagi jika ingin mengganti.
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                <div class="text-end">
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-emerald px-3 rounded-pill btn-save-koef" 
                                                                            id="btnSaveKoefPair-{{ $pairDashKode }}"
                                                                            data-pair="true"
                                                                            data-pair-prefix="{{ $compPrefix }}"
                                                                            data-subkomponen="{{ $compPrefix }}.BC" 
                                                                            data-tahap="{{ $currentTahap }}"
                                                                            data-subtype="kualitas_pemanfaatan"
                                                                            data-has-file="{{ $currentPairHasFile ? 'true' : 'false' }}"
                                                                            {{ !$currentPairIsComplete ? 'disabled' : '' }}>
                                                                        <i class="bi bi-save me-1"></i> Simpan Gradasi {{ $pairCodeB }} & {{ $pairCodeC }} ({{ $isModeEvaluasi ? 'Evaluasi' : 'PM' }})
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
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

    // Helper kelas badge Bootstrap berdasarkan gradasi nilai
    function getGradasiBadgeClass(grad) {
        switch (grad) {
            case 'AA': return 'bg-success text-white';
            case 'A':  return 'bg-info text-dark';
            case 'BB': return 'bg-primary text-white';
            case 'B':  return 'bg-primary-subtle text-primary border border-primary-subtle';
            case 'CC': return 'bg-warning-subtle text-dark border border-warning-subtle';
            case 'C':  return 'bg-warning text-dark';
            case 'D':  return 'bg-danger text-white';
            case 'E':  return 'bg-secondary text-white';
            default:   return 'bg-secondary text-white';
        }
    }

    // Helper Predikat AKIP berdasarkan Grand Total Skor
    function getPredikatAkip(score) {
        const val = parseFloat(score);
        if (val > 90.0) return { predikat: 'AA', keterangan: 'Sangat Memuaskan', badge: 'bg-success text-white' };
        if (val > 80.0) return { predikat: 'A',  keterangan: 'Memuaskan', badge: 'bg-info text-dark' };
        if (val > 70.0) return { predikat: 'BB', keterangan: 'Sangat Baik', badge: 'bg-primary text-white' };
        if (val > 60.0) return { predikat: 'B',  keterangan: 'Baik', badge: 'bg-primary-subtle text-primary border border-primary-subtle' };
        if (val > 50.0) return { predikat: 'CC', keterangan: 'Cukup', badge: 'bg-warning-subtle text-dark border border-warning-subtle' };
        if (val > 30.0) return { predikat: 'C',  keterangan: 'Kurang', badge: 'bg-warning text-dark' };
        return { predikat: 'D', keterangan: 'Sangat Kurang', badge: 'bg-danger text-white' };
    }

    // Pembaruan dinamis badge Predikat pada kartu Grand Total
    function updateGrandTotalPredikats(scores) {
        if (!scores) return;
        const valEval = scores.grand_total_evaluasi ?? scores.grand_total_pk;
        const valPm = scores.grand_total_pm;
        
        const badgePredEval = document.getElementById('badgePredikatEvaluasi');
        if (badgePredEval && valEval !== undefined) {
            const pEval = getPredikatAkip(valEval);
            badgePredEval.className = `badge ${pEval.badge} text-xs`;
            badgePredEval.textContent = `Predikat ${pEval.predikat} (${pEval.keterangan})`;
        }

        const badgePredPm = document.getElementById('badgePredikatPm');
        if (badgePredPm && valPm !== undefined) {
            const pPm = getPredikatAkip(valPm);
            badgePredPm.className = `badge ${pPm.badge} text-xs`;
            badgePredPm.textContent = `Predikat ${pPm.predikat} (${pPm.keterangan})`;
        }
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
            const gradVal = this.value;
            const isPair = this.dataset.pair === 'true';
            if (isPair) {
                const pairPrefix = this.dataset.pairPrefix;
                const reqStar = document.querySelector(`.req-star-uraian-pair-${pairPrefix}-BC`);
                if (reqStar) {
                    if (gradVal === 'BB') {
                        reqStar.classList.add('d-none');
                    } else {
                        reqStar.classList.remove('d-none');
                    }
                }
            } else {
                const sub = this.dataset.subkomponen;
                const dashSub = sub ? sub.replace(/\./g, '-') : '';
                const reqStar = document.querySelector(`.req-star-uraian-${dashSub}`);
                if (reqStar) {
                    if (gradVal === 'BB') {
                        reqStar.classList.add('d-none');
                    } else {
                        reqStar.classList.remove('d-none');
                    }
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
            const isPair = this.getAttribute('data-pair') === 'true';
            const pairPrefix = this.getAttribute('data-pair-prefix');
            const subKode = this.getAttribute('data-subkomponen');
            const safeName = isPair ? `${pairPrefix}_BC` : subKode.replace(/\./g, '_');
            const dashName = isPair ? `${pairPrefix}-BC` : subKode.replace(/\./g, '-');
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
                    const higherClass = getGradasiBadgeClass(resGradasi);

                    if (isPair) {
                        const valActivePair = document.getElementById(`valActiveKoefPair-${dashName}`);
                        const badgeActivePair = document.getElementById(`badgeActiveKoefPair-${dashName}`);
                        if (valActivePair) valActivePair.textContent = effKoef + '%';
                        if (badgeActivePair) {
                            badgeActivePair.className = `badge ${higherClass} text-xs rounded-pill px-3 py-1 shadow-sm`;
                            badgeActivePair.innerHTML = `Koefisien Aktif: <strong id="valActiveKoefPair-${dashName}">${effKoef}%</strong> (${resGradasi})`;
                        }

                        // Perbarui badge di kedua header Subkomponen (B dan C)
                        ['B', 'C'].forEach(suf => {
                            const sDash = `${pairPrefix}-${suf}`;
                            const subKoefVal = document.getElementById(`subKoefVal-${sDash}`);
                            const subKoefBadge = document.getElementById(`subKoefBadge-${sDash}`);
                            if (subKoefVal) subKoefVal.textContent = effKoef + '%';
                            if (subKoefBadge) {
                                subKoefBadge.className = `badge ${higherClass} text-xs`;
                                subKoefBadge.innerHTML = `Koef: <strong id="subKoefVal-${sDash}">${effKoef}%</strong> (${resGradasi})`;
                            }
                        });

                        const gateAlertContainer = document.getElementById(`gateAlertContainerPair-${dashName}`);
                        if (gateAlertContainer) gateAlertContainer.innerHTML = '';
                    } else {
                        const badgeVal = document.getElementById(`valActiveKoef-${dashName}`);
                        const badgeEl = document.getElementById(`badgeActiveKoef-${dashName}`);
                        const subKoefVal = document.getElementById(`subKoefVal-${dashName}`);
                        const subKoefBadge = document.getElementById(`subKoefBadge-${dashName}`);

                        if (badgeVal) badgeVal.textContent = effKoef + '%';
                        if (subKoefVal) subKoefVal.textContent = effKoef + '%';

                        const guideActiveBadge = document.getElementById(`guideActiveBadge-${dashName}`);

                        if (badgeEl) {
                            badgeEl.className = `badge ${higherClass} text-xs rounded-pill px-3 py-1 shadow-sm`;
                            badgeEl.innerHTML = `Koefisien Aktif: <strong id="valActiveKoef-${dashName}">${effKoef}%</strong> (${resGradasi})`;
                        }
                        if (subKoefBadge) {
                            subKoefBadge.className = `badge ${higherClass} text-xs`;
                            subKoefBadge.innerHTML = `Koef: <strong id="subKoefVal-${dashName}">${effKoef}%</strong> (${resGradasi})`;
                        }
                        if (guideActiveBadge) {
                            guideActiveBadge.className = `badge ${higherClass}`;
                            guideActiveBadge.textContent = `${resGradasi} (${effKoef}%)`;
                        }

                        const gateAlertContainer = document.getElementById(`gateAlertContainer-${dashName}`);
                        if (gateAlertContainer) {
                            gateAlertContainer.innerHTML = '';
                        }
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

                        // Perbarui badge Predikat AKIP
                        updateGrandTotalPredikats(data.scores);

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

                        // Perbarui badge Predikat AKIP
                        updateGrandTotalPredikats(data.scores);

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
                            const badgeColorClass = getGradasiBadgeClass(gradLabel);
                            const valActiveKoef = document.getElementById(`valActiveKoef-${safeSub}`);
                            const badgeActiveKoef = document.getElementById(`badgeActiveKoef-${safeSub}`);
                            const subKoefVal = document.getElementById(`subKoefVal-${safeSub}`);
                            const subKoefBadge = document.getElementById(`subKoefBadge-${safeSub}`);
                            const rumusInfo = document.getElementById(`rumusInfo-${safeSub}`);
                            const guideActiveBadge = document.getElementById(`guideActiveBadge-${safeSub}`);

                            if (valActiveKoef) valActiveKoef.textContent = `${effKoef}%`;
                            if (subKoefVal) subKoefVal.textContent = `${effKoef}%`;
                            if (badgeActiveKoef) {
                                badgeActiveKoef.className = `badge ${badgeColorClass} text-xs rounded-pill px-3 py-1 shadow-sm`;
                                badgeActiveKoef.innerHTML = `Koefisien Aktif: <strong id="valActiveKoef-${safeSub}">${effKoef}%</strong> (${gradLabel})`;
                            }
                            if (subKoefBadge) {
                                subKoefBadge.className = `badge ${badgeColorClass} text-xs`;
                                subKoefBadge.innerHTML = `Koef: <strong id="subKoefVal-${safeSub}">${effKoef}%</strong> (${gradLabel})`;
                            }
                            if (rumusInfo) {
                                rumusInfo.textContent = `(Rumus: Koefisien Kluster ${effKoef}% × Bobot ${parseFloat(subData.bobot).toFixed(1)})`;
                            }
                            if (guideActiveBadge) {
                                guideActiveBadge.className = `badge ${badgeColorClass}`;
                                guideActiveBadge.textContent = `${gradLabel} (${effKoef}%)`;
                            }

                            // Update Gate UI (locked / unlocked state)
                            const statusGate = document.getElementById(`statusGate-${safeSub}`);
                            const statusGateText = document.getElementById(`statusGateText-${safeSub}`);
                            const gateAlertContainer = document.getElementById(`gateAlertContainer-${safeSub}`);
                            const gradFields = document.getElementById(`gradasiFields-${safeSub}`);
                            const saveKoefBtn = document.getElementById(`btnSaveKoef-${safeSub}`);
                            const pemenuhanPct = (tahap === 'pm') ? (subData.pemenuhan_pm_persen ?? 0) : (subData.pemenuhan_eval_persen ?? 0);

                            if (!isComplete) {
                                // Locked State (Automatic Cluster Mode)
                                if (statusGate) {
                                    statusGate.className = 'badge bg-primary-subtle text-primary border border-primary-subtle';
                                    statusGate.innerHTML = `<i class="bi bi-diagram-3-fill me-1"></i> <span id="statusGateText-${safeSub}">Kluster ${gradLabel} (${effKoef}%) • ${terpCount}/${totCount} Terpenuhi</span>`;
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
                                        <div class="alert alert-info py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0 bg-info-subtle text-info-emphasis" id="gateAlert-${safeSub}">
                                            <i class="bi bi-diagram-3-fill fs-6 text-primary"></i>
                                            <div><strong>Kluster Otomatis:</strong> Pemenuhan <strong>${terpCount}</strong> dari <strong>${totCount}</strong> kriteria (<strong>${pemenuhanPct}%</strong>) masuk ke <strong class="badge ${badgeColorClass} px-2 py-1 ms-1">Gradasi ${gradLabel} (${effKoef}%)</strong>. Form pengajuan Gradasi Lanjutan (A / AA) terbuka jika seluruh kriteria 100% Terpenuhi.</div>
                                        </div>
                                    `;
                                }
                            } else {
                                // Unlocked State (100% Fulfilled)
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
                                        gateAlertContainer.innerHTML = `
                                            <div class="alert alert-success py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0 bg-success-subtle text-success" id="gateAlert-${safeSub}">
                                                <i class="bi bi-check-circle-fill fs-6"></i>
                                                <div><strong>Kriteria 100% Terpenuhi!</strong> Gradasi aktif saat ini: <strong class="badge ${badgeColorClass} px-2 py-1 ms-1">Gradasi ${gradLabel} (${effKoef}%)</strong>.</div>
                                            </div>
                                        `;
                                    }
                                }
                            }
                        }

                        // Update Gate UI & Progress untuk pasangan Subkomponen X.B & X.C
                        if (data.scores.pairs) {
                            for (const [pfx, pairData] of Object.entries(data.scores.pairs)) {
                                const pairDash = `${pfx}-BC`;
                                const pairSafe = `${pfx}_BC`;
                                const isComplete = (tahap === 'pm') ? pairData.is_pair_complete_pm : pairData.is_pair_complete_eval;
                                const bTerp = (tahap === 'pm') ? pairData.b_terpenuhi_pm : pairData.b_terpenuhi_eval;
                                const cTerp = (tahap === 'pm') ? pairData.c_terpenuhi_pm : pairData.c_terpenuhi_eval;
                                const bTot = pairData.b_total;
                                const cTot = pairData.c_total;

                                const statusB = document.getElementById(`pairStatusB-${pairDash}`);
                                if (statusB) {
                                    const bPct = bTot > 0 ? ((bTerp / bTot) * 100).toFixed(1) : 0;
                                    statusB.textContent = `${pfx}.B: ${bTerp}/${bTot} Terpenuhi (${bPct}%)`;
                                    statusB.className = `badge ${bTerp === bTot && bTot > 0 ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary'} text-xs`;
                                }
                                const statusC = document.getElementById(`pairStatusC-${pairDash}`);
                                if (statusC) {
                                    const cPct = cTot > 0 ? ((cTerp / cTot) * 100).toFixed(1) : 0;
                                    statusC.textContent = `${pfx}.C: ${cTerp}/${cTot} Terpenuhi (${cPct}%)`;
                                    statusC.className = `badge ${cTerp === cTot && cTot > 0 ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary'} text-xs`;
                                }

                                const statusGate = document.getElementById(`statusGatePair-${pairDash}`);
                                const gradFields = document.getElementById(`gradasiFieldsPair-${pairDash}`);
                                const saveBtn = document.getElementById(`btnSaveKoefPair-${pairDash}`);
                                const alertContainer = document.getElementById(`gateAlertContainerPair-${pairDash}`);

                                const subCData = data.scores.subkomponen[`${pfx}.C`] || {};
                                const gradLabel = (tahap === 'pm') ? (subCData.gradasi_pm || 'BB') : (subCData.gradasi_eval || 'BB');
                                const effKoef = (tahap === 'pm') ? (subCData.koefisien_pm || 80) : (subCData.koefisien_evaluasi || 80);
                                const badgeColor = getGradasiBadgeClass(gradLabel);

                                const badgeActiveKoefPair = document.getElementById(`badgeActiveKoefPair-${pairDash}`);
                                const valActiveKoefPair = document.getElementById(`valActiveKoefPair-${pairDash}`);
                                if (valActiveKoefPair) valActiveKoefPair.textContent = effKoef + '%';
                                if (badgeActiveKoefPair) {
                                    badgeActiveKoefPair.className = `badge ${badgeColor} text-xs rounded-pill px-3 py-1 shadow-sm`;
                                    badgeActiveKoefPair.innerHTML = `Koefisien Aktif: <strong id="valActiveKoefPair-${pairDash}">${effKoef}%</strong> (${gradLabel})`;
                                }

                                const rumusInfoPair = document.getElementById(`rumusInfoPair-${pairDash}`);
                                if (rumusInfoPair) {
                                    const subBData = data.scores.subkomponen[`${pfx}.B`] || {};
                                    rumusInfoPair.textContent = `Rumus: Koefisien Kluster ${effKoef}% × Bobot (${pfx}.B: ${parseFloat(subBData.bobot || 0).toFixed(1)} & ${pfx}.C: ${parseFloat(subCData.bobot || 0).toFixed(1)})`;
                                }

                                if (!isComplete) {
                                    if (statusGate) {
                                        statusGate.className = 'badge bg-warning-subtle text-dark border border-warning-subtle text-xs';
                                        statusGate.innerHTML = `<i class="bi bi-lock-fill me-1 text-danger"></i> Gerbang Terkunci (Perlu 100% pada ${pfx}.B & ${pfx}.C)`;
                                    }
                                    if (gradFields) gradFields.classList.add('opacity-50', 'pe-none');
                                    if (saveBtn) saveBtn.disabled = true;

                                    document.querySelectorAll(`input[name="gradasi_${tahap}_${pairSafe}"]`).forEach(r => {
                                        r.disabled = true;
                                        if (r.value === 'BB') r.checked = true;
                                    });

                                    if (alertContainer) {
                                        alertContainer.innerHTML = `
                                            <div class="alert alert-info py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0 bg-info-subtle text-info-emphasis">
                                                <i class="bi bi-diagram-3-fill fs-6 text-primary"></i>
                                                <div><strong>Status Pemenuhan:</strong> Subkomponen <strong>${pfx}.B</strong> (<strong>${bTerp}/${bTot}</strong>) dan <strong>${pfx}.C</strong> (<strong>${cTerp}/${cTot}</strong>). Form pengajuan Gradasi Lanjutan (A / AA) terbuka jika <strong>KEDUA</strong> subkomponen 100% Terpenuhi.</div>
                                            </div>
                                        `;
                                    }
                                } else {
                                    if (statusGate) {
                                        statusGate.className = 'badge bg-success-subtle text-success text-xs';
                                        statusGate.innerHTML = `<i class="bi bi-unlock-fill me-1"></i> Gerbang Terbuka (100% Terpenuhi)`;
                                    }
                                    if (gradFields) gradFields.classList.remove('opacity-50', 'pe-none');
                                    if (saveBtn) saveBtn.disabled = false;

                                    document.querySelectorAll(`input[name="gradasi_${tahap}_${pairSafe}"]`).forEach(r => {
                                        r.disabled = false;
                                    });

                                    const needsGrad = (tahap === 'pm') ? subCData.pm_needs_gradasi : subCData.eval_needs_gradasi;
                                    if (alertContainer) {
                                        if (needsGrad) {
                                            alertContainer.innerHTML = `
                                                <div class="alert alert-warning py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0">
                                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-6"></i>
                                                    <div><strong>Kriteria 100% Terpenuhi!</strong> Harap pilih opsi Gradasi Nilai (BB, A, atau AA) untuk pasangan Subkomponen ${pfx}.B & ${pfx}.C.</div>
                                                </div>
                                            `;
                                        } else {
                                            alertContainer.innerHTML = `
                                                <div class="alert alert-success py-2 px-3 text-xs mb-2 d-flex align-items-center gap-2 border-0 bg-success-subtle text-success">
                                                    <i class="bi bi-check-circle-fill fs-6"></i>
                                                    <div><strong>Kriteria 100% Terpenuhi!</strong> Gradasi aktif saat ini: <strong class="badge ${badgeColor} px-2 py-1 ms-1">Gradasi ${gradLabel} (${effKoef}%)</strong>.</div>
                                                </div>
                                            `;
                                        }
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
