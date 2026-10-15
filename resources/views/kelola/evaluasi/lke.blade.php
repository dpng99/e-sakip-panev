<!-- Tab Bukti Dukung LKE - Modernized Accordion & Card Layout matching Penilaian Mandiri (PM) -->
<div class="lke-evidence-wrapper">
    <!-- 1. SINKRONISASI & PANDUAN BANNER -->
    <div class="alert alert-light border shadow-sm rounded-3 p-3 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-circle p-2 bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; flex-shrink: 0;">
                <i class="bi bi-arrow-repeat fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    Sinkronisasi Bukti Dukung LKE & Penilaian Mandiri (PM)
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill text-xs px-2">Real-Time Sync</span>
                </h6>
                <p class="text-muted text-xs mb-0" style="line-height: 1.5;">
                    Dokumen versi terakhir yang diupload pada modul <strong>Perencanaan</strong> & <strong>Pelaporan</strong> dapat ditarik via tombol <strong>Verif Otomatis</strong>. Berkas khusus dapat diunggah via <strong>Unggah Berkas</strong>. Seluruh bukti dukung langsung tersinkronisasi dan tampil identik di menu <strong>Penilaian Mandiri (PM)</strong>.
                </p>
            </div>
        </div>
        <div>
            <a href="{{ route('lke.evaluasi.index') }}" class="btn btn-emerald btn-sm rounded-pill px-3 shadow-sm text-nowrap">
                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Penilaian Mandiri (PM)
            </a>
        </div>
    </div>

    <!-- 2. METRIC KPI CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs text-uppercase fw-semibold">Total Kriteria LKE</span>
                    <span class="badge bg-light text-dark border rounded-circle p-1" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-list-check"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark mb-0">{{ $totalKriteriaLke ?? 0 }}</div>
                <small class="text-muted text-xs">Indikator kriteria penilaian</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs text-uppercase fw-semibold">Bukti Terpenuhi</span>
                    <span class="badge bg-success-subtle text-success rounded-circle p-1" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-check-circle-fill"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-success mb-0">{{ $lengkapCount ?? 0 }}</div>
                <small class="text-success text-xs fw-semibold">Kriteria memiliki dokumen lengkap</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs text-uppercase fw-semibold">Belum Lengkap</span>
                    <span class="badge bg-danger-subtle text-danger rounded-circle p-1" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-danger mb-0">{{ $belumCount ?? 0 }}</div>
                <small class="text-danger text-xs fw-semibold">Perlu verifikasi / unggah</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted text-xs text-uppercase fw-semibold">Kelengkapan Bukti</span>
                    <span class="badge bg-warning-subtle text-dark rounded-circle p-1" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-pie-chart-fill"></i>
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark mb-1">{{ $persenLengkap ?? 0 }}%</div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persenLengkap ?? 0 }}%;" aria-valuenow="{{ $persenLengkap ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. FILTER & SEARCH TOOLBAR -->
    <div class="card border shadow-sm rounded-3 p-3 mb-4 bg-light">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="lkeSearchInput" class="form-control border-start-0 ps-0 text-sm" placeholder="Cari kode kriteria (misal: 1.B.1), uraian kriteria, atau berkas...">
                </div>
            </div>
            <div class="col-md-4">
                <select id="lkeKomponenFilter" class="form-select text-sm">
                    <option value="">Semua Komponen (1 s.d 4)</option>
                    @if(isset($komponenList))
                        @foreach ($komponenList as $kItem)
                            <option value="komponen-{{ $kItem->id }}">Komponen {{ $kItem->no ?? $kItem->id }}: {{ $kItem->nama }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <select id="lkeStatusFilter" class="form-select text-sm">
                    <option value="">Semua Status Kelengkapan</option>
                    <option value="status-lengkap">Lengkap (100%)</option>
                    <option value="status-belum">Belum Lengkap (&lt; 100%)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- 4. HIERARCHICAL ACCORDION & KRITERIA CARDS (Mirroring Penilaian Mandiri & Evaluasi) -->
    <div class="accordion" id="accordionBuktiLke">
        @if(isset($komponenList) && $komponenList->isNotEmpty())
            @foreach($komponenList as $komp)
                @php
                    $kompCriteria = $komp->subkomponen->flatMap(fn($s) => $s->kriteria);
                    $kompTotal = $kompCriteria->count();
                    $kompTerpenuhi = 0;
                    foreach($kompCriteria as $kCr) {
                        $kReq = $kCr->bukti_dukung_ids;
                        if(empty($kReq)) {
                            $kompTerpenuhi++;
                            continue;
                        }
                        $kEvs = $satkerBukti->get($kCr->kode, collect());
                        $kAllMet = true;
                        foreach($kReq as $bId) {
                            $hasDoc = $kEvs->first(function($item) use ($bId) {
                                return $item->buktidukung_id == $bId 
                                    || $item->kode_bukti == $bId 
                                    || \App\Helpers\LkeEvidenceHelper::matchesBuktiId($item->link_bukti_dukung, (int)$bId);
                            });
                            if(!$hasDoc) {
                                $kAllMet = false;
                                break;
                            }
                        }
                        if($kAllMet) $kompTerpenuhi++;
                    }
                    $kompPersen = $kompTotal > 0 ? round(($kompTerpenuhi / $kompTotal) * 100) : 0;
                @endphp

                <div class="accordion-item border-0 shadow-sm rounded-3 mb-3 overflow-hidden lke-komponen-accordion-item komponen-{{ $komp->id }}" data-komponen="komponen-{{ $komp->id }}">
                    <h2 class="accordion-header" id="headingKomp-{{ $komp->id }}">
                        <button class="accordion-button bg-light text-dark fw-bold py-3 {{ $loop->first ? '' : 'collapsed' }}"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseKomp-{{ $komp->id }}"
                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success me-1 text-sm">Komponen {{ $komp->no ?? $komp->id }}</span>
                                    <span class="text-md fw-bold text-dark">{{ $komp->nama }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $kompPersen == 100 ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3 py-1.5 text-xs">
                                        <i class="bi {{ $kompPersen == 100 ? 'bi-check-circle-fill' : 'bi-clock-history' }} me-1"></i>
                                        {{ $kompTerpenuhi }}/{{ $kompTotal }} Kriteria Lengkap ({{ $kompPersen }}%)
                                    </span>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseKomp-{{ $komp->id }}"
                         class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                         data-bs-parent="#accordionBuktiLke">
                        <div class="accordion-body p-3 bg-white">
                            @foreach($komp->subkomponen as $sub)
                                @php
                                    $subDashKode = str_replace('.', '-', $sub->kode);
                                @endphp
                                <!-- Subkomponen Header Box -->
                                <div class="lke-subkomp-box mb-3 d-flex flex-wrap justify-content-between align-items-center p-2.5 rounded-3 bg-light border sub-box-{{ $subDashKode }}">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <span class="badge bg-dark font-monospace text-xs">{{ $sub->kode }}</span>
                                        <strong class="text-dark text-sm">{{ $sub->nama }}</strong>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-secondary-subtle text-secondary text-xs">
                                            {{ $sub->kriteria->count() }} Kriteria
                                        </span>
                                    </div>
                                </div>

                                <!-- Kriteria Cards under this Subkomponen -->
                                <div class="mb-4 lke-subkomp-criteria-list">
                                    @foreach($sub->kriteria as $cr)
                                        @php
                                            $requiredBuktiIds = $cr->bukti_dukung_ids;
                                            $totalRequired = count($requiredBuktiIds);
                                            $evidences = $satkerBukti->get($cr->kode, collect());
                                            $dashKode = str_replace('.', '-', $cr->kode);

                                            // Hitung dokumen yang sudah ada
                                            $uploadedCount = 0;
                                            if(!empty($requiredBuktiIds)) {
                                                foreach($requiredBuktiIds as $bId) {
                                                    $has = $evidences->first(function($item) use ($bId) {
                                                        return $item->buktidukung_id == $bId
                                                            || $item->kode_bukti == $bId
                                                            || \App\Helpers\LkeEvidenceHelper::matchesBuktiId($item->link_bukti_dukung, (int)$bId);
                                                    });
                                                    if($has) $uploadedCount++;
                                                }
                                                $isLengkap = ($uploadedCount >= $totalRequired);
                                                $kriteriaPersen = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 100;
                                            } else {
                                                $isLengkap = true;
                                                $kriteriaPersen = 100;
                                            }

                                            $rowStatusClass = $isLengkap ? 'status-lengkap' : 'status-belum';

                                            // Formats dari mapping kriteria
                                            $formats = $formatMap[$cr->kode] ?? [];
                                            $verifFormats = collect($formats)->filter(fn($f) => stripos($f, '(perubahan terakhir)') !== false)->values();
                                            $uploadFormats = collect($formats)->filter(fn($f) => stripos($f, '(perubahan terakhir)') === false)->values();
                                        @endphp

                                        <div class="card lke-kriteria-card mb-3 border shadow-xs rounded-3 {{ $rowStatusClass }}"
                                             id="cardKriteria-{{ $dashKode }}"
                                             data-kode="{{ strtolower($cr->kode) }}"
                                             data-nama="{{ strtolower($cr->nama) }}"
                                             data-komponen="komponen-{{ $komp->id }}"
                                             data-subkomponen="sub-{{ $subDashKode }}"
                                             data-status="{{ $rowStatusClass }}">
                                            <div class="card-body p-3">
                                                <div class="row g-3 align-items-stretch">
                                                    <!-- Left Column: Kriteria Info & Bukti Dukung yang Diperlukan (col-lg-7) -->
                                                    <div class="col-lg-7 d-flex flex-column justify-content-between">
                                                        <div>
                                                            <div class="d-flex align-items-start mb-2">
                                                                <span class="badge bg-dark font-monospace text-xs me-2 px-2.5 py-1">{{ $cr->kode }}</span>
                                                                <div class="fw-bold text-dark text-sm" style="line-height: 1.4;">{{ $cr->nama }}</div>
                                                            </div>

                                                            <!-- Required Evidence Box -->
                                                            <div class="mt-2 p-2.5 bg-light rounded-3 border">
                                                                <div class="text-xs fw-bold text-muted mb-2 d-flex justify-content-between align-items-center">
                                                                    <span><i class="bi bi-file-earmark-check me-1 text-success"></i> Bukti Dukung yang Diperlukan:</span>
                                                                    <span class="badge bg-white text-secondary border text-xs">{{ $totalRequired }} Berkas</span>
                                                                </div>

                                                                @if(!empty($requiredBuktiIds))
                                                                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1.5">
                                                                        @foreach($requiredBuktiIds as $bId)
                                                                            @php
                                                                                $docName = $masterBukti[$bId] ?? ('Dokumen #'.$bId);
                                                                                $hasUploaded = $evidences->first(function ($item) use ($bId) {
                                                                                    return $item->buktidukung_id == $bId 
                                                                                        || $item->kode_bukti == $bId
                                                                                        || \App\Helpers\LkeEvidenceHelper::matchesBuktiId($item->link_bukti_dukung, (int)$bId);
                                                                                });
                                                                            @endphp
                                                                            <li class="d-flex align-items-center justify-content-between p-1.5 px-2 rounded-2 {{ $hasUploaded ? 'bg-white border border-success-subtle' : 'bg-white border border-danger-subtle' }}">
                                                                                <div class="text-xs d-flex align-items-center gap-1.5 me-2 text-truncate" title="{{ $docName }}">
                                                                                    <span class="badge {{ $hasUploaded ? 'bg-success' : 'bg-secondary' }} rounded-pill text-2xs px-1.5">#{{ $bId }}</span>
                                                                                    <span class="fw-semibold {{ $hasUploaded ? 'text-dark' : 'text-muted' }} text-truncate">{{ $docName }}</span>
                                                                                </div>

                                                                                @if($hasUploaded)
                                                                                    @php
                                                                                        $rawLink = $hasUploaded->link_bukti_dukung;
                                                                                        $fileUrl = str_starts_with($rawLink, 'http')
                                                                                            ? $rawLink
                                                                                            : asset('uploads/repository/' . $idSatker . '/' . basename($rawLink));
                                                                                    @endphp
                                                                                    <a href="{{ $fileUrl }}" target="_blank" class="badge bg-success-subtle text-success text-decoration-none border border-success-subtle hover-shadow py-1 px-2 text-nowrap">
                                                                                        <i class="bi bi-eye me-1"></i> Ada File
                                                                                    </a>
                                                                                @else
                                                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2 text-nowrap">
                                                                                        <i class="bi bi-x-circle me-1"></i> Belum Diupload
                                                                                    </span>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <span class="text-muted text-xs fst-italic">Tidak memerlukan bukti dukung khusus.</span>
                                                                @endif
                                                            </div>

                                                            @if(!empty($cr->langkah_kerja))
                                                                <div class="mt-2 p-2.5 bg-light-subtle rounded-3 border border-secondary-subtle">
                                                                    <div class="text-xs fw-bold text-secondary mb-1 d-flex align-items-center">
                                                                        <i class="bi bi-list-task text-primary me-1"></i> Langkah Kerja:
                                                                    </div>
                                                                    <div class="text-xs text-dark" style="white-space: pre-line; line-height: 1.5;">{!! nl2br(e($cr->langkah_kerja)) !!}</div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Right Column: Status Pemenuhan & Aksi (col-lg-5) -->
                                                    <div class="col-lg-5 d-flex flex-column justify-content-between">
                                                        <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                                            <!-- Status Header -->
                                                            <div>
                                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                                    <span class="text-xs text-uppercase fw-semibold text-muted">Status Pemenuhan</span>
                                                                    @if($isLengkap)
                                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 text-xs">
                                                                            <i class="bi bi-check-circle-fill me-1"></i> Lengkap (100%)
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2.5 py-1 text-xs">
                                                                            <i class="bi bi-exclamation-circle-fill text-warning me-1"></i> {{ $uploadedCount }}/{{ $totalRequired }} Dokumen
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="progress mb-3" style="height: 6px;">
                                                                    <div class="progress-bar {{ $isLengkap ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $kriteriaPersen }}%;" aria-valuenow="{{ $kriteriaPersen }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>

                                                            <!-- Action Buttons -->
                                                            <div class="d-flex flex-column gap-2 pt-2 border-top">
                                                                @if($verifFormats->isNotEmpty())
                                                                    <button type="button" class="btn btn-sm btn-outline-success btn-verif rounded-pill px-3 py-1.5 text-xs fw-semibold w-100 d-flex align-items-center justify-content-center shadow-xs"
                                                                            data-id-kriteria="{{ $cr->kode }}"
                                                                            data-id-komponen="{{ $komp->id }}"
                                                                            data-id-subkomponen="{{ $sub->kode }}"
                                                                            data-formats='@json($verifFormats)'
                                                                            title="Tarik dokumen versi terakhir dari modul Perencanaan / Pelaporan">
                                                                        <i class="bi bi-arrow-repeat me-1.5 fs-6"></i> Verif Otomatis
                                                                    </button>
                                                                @endif

                                                                @if($uploadFormats->isNotEmpty())
                                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-upload rounded-pill px-3 py-1.5 text-xs fw-semibold w-100 d-flex align-items-center justify-content-center shadow-xs"
                                                                            data-id-kriteria="{{ $cr->kode }}"
                                                                            data-id-komponen="{{ $komp->id }}"
                                                                            data-id-subkomponen="{{ $sub->kode }}"
                                                                            data-kriteria-nama="{{ $cr->nama }}"
                                                                            data-formats='@json($uploadFormats)'
                                                                            title="Unggah berkas bukti dukung baru">
                                                                        <i class="bi bi-cloud-arrow-up-fill me-1.5 fs-6"></i> Unggah Berkas
                                                                    </button>
                                                                @endif

                                                                @if($verifFormats->isEmpty() && $uploadFormats->isEmpty() && !empty($requiredBuktiIds))
                                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-upload rounded-pill px-3 py-1.5 text-xs fw-semibold w-100 d-flex align-items-center justify-content-center shadow-xs"
                                                                            data-id-kriteria="{{ $cr->kode }}"
                                                                            data-id-komponen="{{ $komp->id }}"
                                                                            data-id-subkomponen="{{ $sub->kode }}"
                                                                            data-kriteria-nama="{{ $cr->nama }}"
                                                                            data-formats='@json(["Dokumen_Bukti_{$cr->kode}"])'
                                                                            title="Unggah berkas bukti dukung baru">
                                                                        <i class="bi bi-cloud-arrow-up-fill me-1.5 fs-6"></i> Unggah Berkas
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- Modal Upload Dokumen -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('upload.dokumen') }}" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-3">
            @csrf
            <div class="modal-header bg-light py-3 border-bottom">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="uploadModalLabel">
                    <i class="bi bi-cloud-arrow-up-fill text-primary"></i> Unggah Bukti Dukung LKE
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-info-subtle py-2 px-3 text-xs mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill text-info fs-5"></i>
                    <div>
                        Unggah berkas dalam format <strong>PDF</strong> (Maksimal 4MB). Berkas akan otomatis tersinkronisasi dan tampil di <strong>Penilaian Mandiri (PM)</strong>.
                    </div>
                </div>
                <div id="modalUploadFields">
                    <!-- Dinamis lewat JS -->
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-3 border-top">
                <input type="hidden" name="id_kriteria">
                <input type="hidden" name="id_komponen">
                <input type="hidden" name="id_subkomponen">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" id="btnSubmitUpload">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Simpan & Sinkronkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Feedback Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="lkeToast" class="toast align-items-center border-0 text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="lkeToastMessage">
                <i class="bi bi-info-circle-fill"></i> Pesan status...
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Helper Toast function
            function showToast(message, isSuccess = true) {
                const toastEl = document.getElementById('lkeToast');
                const toastMsg = document.getElementById('lkeToastMessage');
                if (toastEl && toastMsg) {
                    toastEl.className = 'toast align-items-center border-0 text-white ' + (isSuccess ? 'bg-success' : 'bg-danger');
                    toastMsg.innerHTML = '<i class="bi ' + (isSuccess ? 'bi-check-circle-fill' : 'bi-x-circle-fill') + ' me-1"></i> ' + message;
                    const bsToast = new bootstrap.Toast(toastEl, { delay: 4000 });
                    bsToast.show();
                }
            }

            // Live Search & Filter Logic
            const searchInput = document.getElementById('lkeSearchInput');
            const komponenFilter = document.getElementById('lkeKomponenFilter');
            const statusFilter = document.getElementById('lkeStatusFilter');
            const cards = document.querySelectorAll('.lke-kriteria-card');
            const komponenAccordionItems = document.querySelectorAll('.lke-komponen-accordion-item');

            function filterCards() {
                const searchVal = (searchInput ? searchInput.value : '').toLowerCase().trim();
                const kompVal = (komponenFilter ? komponenFilter.value : '').trim();
                const statVal = (statusFilter ? statusFilter.value : '').trim();

                cards.forEach(card => {
                    const kode = card.dataset.kode || '';
                    const nama = card.dataset.nama || '';
                    const komp = card.dataset.komponen || '';
                    const stat = card.dataset.status || '';
                    const text = card.textContent.toLowerCase();

                    const matchesSearch = !searchVal || kode.includes(searchVal) || nama.includes(searchVal) || text.includes(searchVal);
                    const matchesKomp = !kompVal || komp === kompVal;
                    const matchesStat = !statVal || stat === statVal;

                    if (matchesSearch && matchesKomp && matchesStat) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Update subkomponen boxes & accordion items visibility
                komponenAccordionItems.forEach(item => {
                    const visibleCardsInKomp = item.querySelectorAll('.lke-kriteria-card:not([style*="display: none"])');
                    item.style.display = (visibleCardsInKomp.length > 0) ? '' : 'none';

                    // Update subkomponen boxes inside this item
                    const subBoxes = item.querySelectorAll('.lke-subkomp-box');
                    subBoxes.forEach(box => {
                        const nextList = box.nextElementSibling;
                        if (nextList && nextList.classList.contains('lke-subkomp-criteria-list')) {
                            const visibleCardsInSub = nextList.querySelectorAll('.lke-kriteria-card:not([style*="display: none"])');
                            box.style.display = (visibleCardsInSub.length > 0) ? '' : 'none';
                        }
                    });
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterCards);
            if (komponenFilter) komponenFilter.addEventListener('change', filterCards);
            if (statusFilter) statusFilter.addEventListener('change', filterCards);

            // Handler Tombol Verifikasi Otomatis
            document.querySelectorAll('.btn-verif').forEach(btn => {
                btn.addEventListener('click', function() {
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menarik berkas...';
                    this.disabled = true;

                    let formats = [];
                    try {
                        formats = JSON.parse(this.dataset.formats);
                    } catch (e) {
                        formats = [];
                    }

                    let data = {
                        id_kriteria: this.dataset.idKriteria,
                        id_komponen: this.dataset.idKomponen,
                        id_subkomponen: this.dataset.idSubkomponen,
                        formats: formats
                    };

                    fetch("{{ route('verifikasi.dokumen') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            showToast('Verifikasi berhasil! Dokumen berhasil ditarik dan disinkronkan.', true);
                            setTimeout(() => {
                                location.reload();
                            }, 800);
                        } else {
                            showToast('Verifikasi gagal: ' + (res.message || 'Tidak ada dokumen yang ditemukan.'), false);
                            this.innerHTML = originalHtml;
                            this.disabled = false;
                        }
                    })
                    .catch(err => {
                        showToast('Terjadi kesalahan saat memverifikasi dokumen.', false);
                        this.innerHTML = originalHtml;
                        this.disabled = false;
                    });
                });
            });

            // Handler Tombol Upload Manual
            document.querySelectorAll('.btn-upload').forEach(btn => {
                btn.addEventListener('click', function() {
                    let formats = [];
                    try {
                        formats = JSON.parse(this.dataset.formats);
                    } catch (e) {
                        formats = [];
                    }

                    const modalFields = document.getElementById('modalUploadFields');
                    modalFields.innerHTML = '';

                    if (formats.length === 0) {
                        formats = ['Dokumen Bukti'];
                    }

                    formats.forEach((f, i) => {
                        if (!f.includes('(perubahan terakhir)')) {
                            const fieldHtml = `
                                <div class="mb-3">
                                    <label class="form-label text-sm fw-semibold text-dark mb-1">
                                        <i class="bi bi-file-earmark-arrow-up text-primary me-1"></i>${f}
                                    </label>
                                    <input type="file" name="files[]" class="form-control text-sm" accept=".pdf" required data-format="${f}">
                                </div>`;
                            modalFields.innerHTML += fieldHtml;
                        }
                    });

                    // Set hidden inputs
                    document.querySelector('#uploadModal input[name=id_kriteria]').value = this.dataset.idKriteria;
                    document.querySelector('#uploadModal input[name=id_komponen]').value = this.dataset.idKomponen;
                    document.querySelector('#uploadModal input[name=id_subkomponen]').value = this.dataset.idSubkomponen;

                    // Tampilkan modal via standard Bootstrap API
                    const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
                    uploadModal.show();
                });
            });
        });
    </script>
@endpush
