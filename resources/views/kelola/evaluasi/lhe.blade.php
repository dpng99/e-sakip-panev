@php
    $role = $evaluatorRole ?? 'satker';
    $isPengawasanKejati = ($role === 'pengawasan_kejati');
    $isJamwas = ($role === 'jamwas');
    $isAdmin = ($role === 'admin');
    $isEvaluator = $isPengawasanKejati || $isJamwas || $isAdmin;
    $scoreData = $myLheScore ?? [];
    $grandTotal = $scoreData['grand_total'] ?? 0;
    $predikatInfo = $scoreData['predikat_evaluasi'] ?? ['predikat' => '-', 'keterangan' => 'Belum Dievaluasi', 'badge' => 'bg-secondary text-white'];
    $komponenScores = $scoreData['komponen'] ?? [];
    $subkomponenScores = $scoreData['subkomponen'] ?? [];
@endphp

<div class="lhe-akip-container">
    <!-- 1. Header Informasi & Status Hak Akses -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center">
                <i class="bi bi-file-earmark-text-fill text-primary me-2 fs-4"></i>
                Laporan Hasil Evaluasi (LHE) AKIP Tahun {{ $tahun }}
            </h5>
            <p class="text-muted text-sm mb-0">
                Penyajian hasil evaluasi akuntabilitas kinerja dan arsip dokumen resmi LHE AKIP.
            </p>
        </div>
        <div class="mt-2 mt-md-0">
            @if ($isJamwas)
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-shield-lock-fill me-1"></i> JAMWAS 888882 - Evaluator Pusat (Kejati & Eselon I)
                </span>
            @elseif ($isAdmin)
                <span class="badge bg-dark-subtle text-dark border border-dark-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-person-fill-gear me-1"></i> Admin Pusat - Evaluator Nasional
                </span>
            @elseif ($isPengawasanKejati)
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-diagram-3-fill me-1"></i> Pengawasan Kejati - Evaluator Wilayah (Kejari & Cabjari)
                </span>
            @else
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-building me-1"></i> Satker Binaan - Penilaian Mandiri (PM)
                </span>
            @endif
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if (session('success-lhe'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success-lhe') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error-lhe'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error-lhe') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (!$isEvaluator)
        <!-- Info Banner untuk Satker Binaan (419346, Kejati reguler, Kejari, Cabjari) -->
        <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4 d-flex align-items-start gap-3" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);">
            <i class="bi bi-info-circle-fill text-primary fs-3 mt-1"></i>
            <div>
                <h6 class="fw-bold text-dark mb-1">Informasi Penerbitan Dokumen LHE AKIP</h6>
                <p class="text-secondary text-sm mb-0">
                    Dokumen Laporan Hasil Evaluasi (LHE) AKIP resmi diterbitkan dan diunggah langsung oleh Tim Evaluator yang berwenang:
                    <br>&bull; <strong>Kejaksaan Tinggi & Unit Eselon I:</strong> dievaluasi dan diunggah oleh <strong>JAMWAS Pusat (888882)</strong>.
                    <br>&bull; <strong>Kejaksaan Negeri & Cabang Kejaksaan Negeri:</strong> dievaluasi dan diunggah oleh <strong>Pengawasan Kejaksaan Tinggi</strong> wilayah hukum masing-masing.
                    <br>Satker Anda berhak memantau rincian nilai evaluasi LKE serta mengunduh berkas LHE resmi yang telah diunggah di bawah ini.
                </p>
            </div>
        </div>
    @endif

    <!-- 2. SECTION UTAMA: HASIL EVALUASI & DOKUMEN LHE SATUAN KERJA SENDIRI -->
    @if (!$isPengawasanKejati && !$isJamwas)
    <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-award-fill text-white"></i>
                </span>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">
                        @if ($isPengawasanKejati || (int)session('id_sakip_level') == 2)
                            Hasil Evaluasi & Dokumen LHE AKIP Kejati (Evaluasi JAMWAS)
                        @else
                            Hasil Evaluasi & Dokumen LHE AKIP Satuan Kerja
                        @endif
                    </h6>
                    <small class="text-muted">Tahun Anggaran {{ $tahun }} &bull; {{ session('satkernama') }}</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted text-xs text-uppercase fw-semibold">Predikat AKIP:</span>
                <span class="badge {{ $predikatInfo['badge'] ?? 'bg-secondary' }} px-3 py-1.5 rounded-pill fs-7 fw-bold shadow-sm">
                    {{ $predikatInfo['predikat'] ?? '-' }} &bull; {{ $predikatInfo['keterangan'] ?? 'Belum Dievaluasi' }}
                </span>
            </div>
        </div>

        <div class="card-body p-4 bg-light bg-opacity-25">
            <!-- Nilai Akhir & Score Card -->
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card border-0 rounded-3 shadow-sm h-100 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2.5 py-1 text-xs mb-2">
                                    <i class="bi bi-clipboard-data-fill me-1"></i> Nilai Evaluasi AKIP
                                </span>
                                <h2 class="display-5 fw-bold mb-1 text-white">{{ number_format($grandTotal, 2) }}</h2>
                                <p class="text-white text-opacity-80 text-sm mb-0">Total Nilai Akuntabilitas Kinerja (Skala 0 - 100)</p>
                            </div>
                            <div class="mt-4 pt-3 border-top border-white border-opacity-20">
                                <div class="d-flex justify-content-between align-items-center mb-1 text-xs">
                                    <span>Pencapaian Nilai</span>
                                    <span class="fw-bold">{{ number_format($grandTotal, 1) }}%</span>
                                </div>
                                <div class="progress bg-white bg-opacity-25" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min(100, max(0, $grandTotal)) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4 Komponen Utama Grid -->
                <div class="col-lg-8">
                    <div class="row g-3 h-100">
                        @php
                            $komponenIcons = [
                                1 => ['icon' => 'bi-calendar-check', 'color' => 'primary'],
                                2 => ['icon' => 'bi-speedometer2', 'color' => 'success'],
                                3 => ['icon' => 'bi-file-earmark-bar-graph', 'color' => 'info'],
                                4 => ['icon' => 'bi-shield-check', 'color' => 'warning'],
                            ];
                        @endphp
                        @foreach ($komponenScores as $kId => $kData)
                            @php
                                $cfg = $komponenIcons[$kData['no'] ?? $kId] ?? ['icon' => 'bi-folder', 'color' => 'secondary'];
                                $kSkor = $kData['evaluasi_skor'] ?? 0;
                                $kMax = $kData['max_skor'] ?? 1;
                                $kPersen = $kData['evaluasi_persentase'] ?? 0;
                            @endphp
                            <div class="col-sm-6">
                                <div class="card border shadow-sm rounded-3 h-100 p-3 bg-white">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-{{ $cfg['color'] }}-subtle text-{{ $cfg['color'] }} rounded-2 p-2">
                                                <i class="bi {{ $cfg['icon'] }} fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark text-sm">{{ $kData['nama'] }}</div>
                                                <small class="text-muted">Bobot: {{ $kMax }}%</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-dark border fw-bold">
                                            {{ number_format($kSkor, 2) }}
                                        </span>
                                    </div>
                                    <div class="mt-auto pt-2">
                                        <div class="d-flex justify-content-between text-xs text-muted mb-1">
                                            <span>Capaian Bobot</span>
                                            <span class="fw-semibold text-dark">{{ number_format($kPersen, 1) }}%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-{{ $cfg['color'] }}" role="progressbar" style="width: {{ min(100, max(0, $kPersen)) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Accordion Rincian 12 Subkomponen -->
            @if (!empty($subkomponenScores))
                <div class="accordion mb-4" id="accordionSubkomponenMySatker">
                    <div class="accordion-item border rounded-3 overflow-hidden shadow-sm">
                        <h2 class="accordion-header" id="headingSubkomponenMySatker">
                            <button class="accordion-button collapsed bg-white fw-semibold text-dark py-2.5" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSubkomponenMySatker" aria-expanded="false" aria-controls="collapseSubkomponenMySatker">
                                <i class="bi bi-list-nested text-primary me-2"></i>
                                Lihat Rincian 12 Subkomponen & Gradasi Nilai Evaluasi AKIP
                            </button>
                        </h2>
                        <div id="collapseSubkomponenMySatker" class="accordion-collapse collapse" aria-labelledby="headingSubkomponenMySatker" data-bs-parent="#accordionSubkomponenMySatker">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3" style="width: 80px;">Kode</th>
                                                <th>Subkomponen</th>
                                                <th class="text-center" style="width: 90px;">Bobot</th>
                                                <th class="text-center" style="width: 100px;">Gradasi</th>
                                                <th class="text-center" style="width: 100px;">Nilai Skor</th>
                                                <th class="text-center pe-3" style="width: 110px;">Capaian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($subkomponenScores as $subKode => $subItem)
                                                <tr>
                                                    <td class="ps-3 fw-bold text-secondary font-monospace">{{ $subKode }}</td>
                                                    <td>
                                                        <div class="fw-semibold text-dark text-sm">{{ $subItem['nama'] }}</div>
                                                    </td>
                                                    <td class="text-center text-muted">{{ number_format($subItem['bobot'], 2) }}</td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $subItem['badge_eval'] ?? 'bg-secondary text-white' }} px-2 py-1 rounded">
                                                            {{ $subItem['gradasi_eval'] ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center fw-bold text-dark">
                                                        {{ number_format($subItem['evaluasi_skor'] ?? 0, 2) }}
                                                    </td>
                                                    <td class="text-center pe-3">
                                                        <span class="badge bg-light text-dark border">
                                                            {{ number_format($subItem['evaluasi_persentase'] ?? 0, 1) }}%
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tabel Arsip Dokumen LHE AKIP Satuan Kerja -->
            <div class="card border rounded-3 bg-white shadow-sm">
                <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark text-sm d-flex align-items-center">
                        <i class="bi bi-archive-fill text-secondary me-2"></i>Berkas Dokumen LHE AKIP Terunggah
                    </h6>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                        {{ $lheAkipFiles->count() }} Berkas Tersimpan
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-xs text-uppercase text-secondary">
                            <tr>
                                <th class="text-center ps-3" style="width: 50px;">No</th>
                                <th>Nama Dokumen LHE</th>
                                <th class="text-center" style="width: 90px;">Versi</th>
                                <th>Tanggal Upload</th>
                                <th class="text-center pe-3" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lheAkipFiles as $idx => $f)
                                <tr>
                                    <td class="text-center ps-3 fw-semibold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-3 me-2"></i>
                                            <div>
                                                <div class="fw-bold text-dark text-sm">LHE AKIP Tahun {{ $tahun }}</div>
                                                <div class="text-muted text-xs font-monospace">{{ $f->id_filename }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">v{{ $f->id_perubahan }}</span>
                                    </td>
                                    <td class="text-sm text-muted">
                                        <i class="bi bi-clock me-1 text-secondary"></i>{{ $f->id_tglupload }}
                                    </td>
                                    <td class="text-center pe-3">
                                        <a href="{{ asset('uploads/repository/' . $f->id_satker . '/' . $f->id_filename) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> Buka File
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                        Belum ada dokumen LHE AKIP resmi yang diunggah untuk Satker ini pada Tahun {{ $tahun }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 3. SECTION ACCORDION KHUSUS: UPLOAD & KELOLA LHE AKIP SATKER WILAYAH / TARGET (HANYA UNTUK PENGAWASAN KEJATI & JAMWAS 888882 / ADMIN) -->
    @if ($isEvaluator)
        <div class="accordion mb-4 shadow-sm" id="accordionKelolaLheWilayah">
            <!-- Accordion Item: Form Upload Dokumen LHE -->
            <div class="accordion-item border rounded-3 overflow-hidden mb-3">
                <h2 class="accordion-header" id="headingUploadLhe">
                    <button class="accordion-button bg-light fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUploadLhe" aria-expanded="true" aria-controls="collapseUploadLhe">
                        <i class="bi bi-cloud-arrow-up-fill text-primary me-2 fs-5"></i>
                        @if ($isPengawasanKejati)
                            Form Unggah Dokumen LHE AKIP Kejari / Cabjari Wilayah Hukum
                        @else
                            Form Unggah Dokumen LHE AKIP Kejati & Para Eselon 1
                        @endif
                    </button>
                </h2>
                <div id="collapseUploadLhe" class="accordion-collapse collapse show" aria-labelledby="headingUploadLhe" data-bs-parent="#accordionKelolaLheWilayah">
                    <div class="accordion-body p-4 bg-white">
                        <form action="{{ route('upload.lhe_akip') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="target_satker_id" class="form-label text-sm fw-semibold">
                                        Pilih Satuan Kerja Tujuan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="target_satker_id" name="target_satker_id" required>
                                        <option value="" disabled selected>-- Pilih Satuan Kerja Sasaran LHE --</option>
                                        @if ($isPengawasanKejati)
                                            @foreach ($subordinateSatkers as $stk)
                                                <option value="{{ $stk->id_satker }}">
                                                    [{{ $stk->id_satker }}] {{ str_replace('_', ' ', $stk->satkernama) }}
                                                    @if ($stk->id_sakip_level == 4)
                                                        (Cabjari)
                                                    @else
                                                        (Kejari)
                                                    @endif
                                                </option>
                                            @endforeach
                                        @else
                                            @foreach ($kejatiList as $stk)
                                                <option value="{{ $stk->id_satker }}">
                                                    [{{ $stk->id_satker }}] {{ str_replace('_', ' ', $stk->satkernama) }}
                                                    @if ($stk->id_sakip_level == 1)
                                                        (Eselon I)
                                                    @else
                                                        (Kejati)
                                                    @endif
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="form-text text-xs text-muted mt-1">
                                        <i class="bi bi-info-circle me-1"></i>
                                        @if ($isPengawasanKejati)
                                            Hanya menampilkan Kejaksaan Negeri dan Cabang Kejaksaan Negeri di wilayah hukum Kejaksaan Tinggi Anda.
                                        @else
                                            Menampilkan seluruh Kejaksaan Tinggi dan Unit Eselon I yang dievaluasi oleh JAMWAS 888882 / Kejaksaan Agung.
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="lhe_akip_file" class="form-label text-sm fw-semibold">
                                        Pilih File PDF LHE AKIP <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="lhe_akip_file" name="lhe_akip_file" accept=".pdf" required>
                                    <div class="form-text text-xs text-muted mt-1">
                                        <i class="bi bi-info-circle me-1"></i>Format berkas .PDF, ukuran maksimal 4MB.
                                    </div>
                                </div>

                                <div class="col-12 text-end pt-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm">
                                        <i class="bi bi-cloud-arrow-up me-1"></i> Unggah Dokumen LHE AKIP
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Accordion Item: Tabel Rekapitulasi & Pemantauan LHE Satker -->
            <div class="accordion-item border rounded-3 overflow-hidden">
                <h2 class="accordion-header" id="headingRekapLhe">
                    <button class="accordion-button bg-light fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRekapLhe" aria-expanded="true" aria-controls="collapseRekapLhe">
                        <i class="bi bi-table text-primary me-2 fs-5"></i>
                        @if ($isPengawasanKejati)
                            Rekapitulasi Hasil Evaluasi & Berkas LHE Kejari / Cabjari Wilayah Hukum
                        @else
                            Rekapitulasi Hasil Evaluasi & Berkas LHE Kejati & Para Eselon 1
                        @endif
                        <span class="badge bg-primary-subtle text-primary ms-2 rounded-pill px-2.5 py-1 text-xs">
                            @if ($isPengawasanKejati)
                                {{ $subordinateSatkers->count() }} Satker
                            @else
                                {{ $kejatiList->count() }} Satker
                            @endif
                        </span>
                    </button>
                </h2>
                <div id="collapseRekapLhe" class="accordion-collapse collapse show" aria-labelledby="headingRekapLhe" data-bs-parent="#accordionKelolaLheWilayah">
                    <div class="accordion-body p-4 bg-white">
                        <!-- Quick Search Filter -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <div class="input-group" style="max-width: 350px;">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="filterSatkerLheTable" class="form-control border-start-0 ps-0" placeholder="Cari nama atau ID satker...">
                            </div>
                            <div class="text-muted text-xs">
                                <i class="bi bi-info-circle me-1"></i>Klik <strong>Lihat Rincian</strong> untuk melihat detail 4 komponen & 12 subkomponen.
                            </div>
                        </div>

                        <div class="table-responsive rounded border">
                            <table class="table table-hover align-middle mb-0" id="tableRekapLheWilayah">
                                <thead class="table-light text-xs text-uppercase text-secondary">
                                    <tr>
                                        <th class="text-center ps-3" style="width: 50px;">No</th>
                                        <th>Satuan Kerja</th>
                                        <th class="text-center" style="width: 110px;">Nilai AKIP</th>
                                        <th class="text-center" style="width: 120px;">Predikat</th>
                                        <th class="text-center" style="width: 150px;">Status LHE</th>
                                        <th style="width: 160px;">Tanggal Dokumen</th>
                                        <th class="text-center pe-3" style="width: 180px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $targetList = $isPengawasanKejati ? $subordinateSatkers : $kejatiList;
                                    @endphp
                                    @forelse ($targetList as $index => $item)
                                        @php
                                            $hasLhe = !empty($item->latestLhe);
                                            $lheItem = $item->latestLhe;
                                            $itemScore = $item->score ?? 0;
                                            $itemPredikat = $item->predikat ?? ['predikat' => '-', 'keterangan' => 'Belum Dievaluasi', 'badge' => 'bg-secondary text-white'];
                                        @endphp
                                        <tr class="satker-row">
                                            <td class="text-center ps-3 text-muted fw-semibold">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark text-sm satker-name">
                                                    {{ str_replace('_', ' ', $item->satkernama) }}
                                                </div>
                                                <div class="text-muted text-xs font-monospace satker-id">
                                                    ID: {{ $item->id_satker }}
                                                    @if (isset($item->id_sakip_level) && $item->id_sakip_level == 4)
                                                        <span class="badge bg-light text-secondary border ms-1">Cabjari</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold fs-6 text-dark">
                                                    {{ number_format($itemScore, 2) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $itemPredikat['badge'] ?? 'bg-secondary text-white' }} px-2.5 py-1 rounded-pill fw-semibold text-xs">
                                                    {{ $itemPredikat['predikat'] ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($hasLhe)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 text-xs">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Terunggah (v{{ $lheItem->id_perubahan }})
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 text-xs">
                                                        <i class="bi bi-clock-history me-1"></i> Belum Ada
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-sm text-muted">
                                                @if ($hasLhe)
                                                    <small><i class="bi bi-clock me-1"></i>{{ $lheItem->id_tglupload }}</small>
                                                @else
                                                    <span class="text-muted text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center pe-3">
                                                <div class="d-inline-flex gap-1 align-items-center">
                                                    <!-- Tombol Detail Nilai Modal -->
                                                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 text-xs btn-detail-nilai"
                                                        data-id-satker="{{ $item->id_satker }}"
                                                        data-nama-satker="{{ str_replace('_', ' ', $item->satkernama) }}"
                                                        title="Lihat Rincian Nilai & Dokumen">
                                                        <i class="bi bi-graph-up-arrow me-1"></i> Rincian
                                                    </button>

                                                    <!-- Tombol Buka File -->
                                                    @if ($hasLhe)
                                                        <a href="{{ asset('uploads/repository/' . $item->id_satker . '/' . $lheItem->id_filename) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 text-xs"
                                                            title="Buka Berkas LHE PDF">
                                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                                        </a>

                                                        <!-- Tombol Hapus Berkas -->
                                                        <form action="{{ route('delete.lhe_akip', $lheItem->id) }}" method="POST" class="d-inline"
                                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas dokumen LHE AKIP (v{{ $lheItem->id_perubahan }}) untuk {{ str_replace('_', ' ', $item->satkernama) }}?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 26px; height: 26px;" title="Hapus Berkas LHE">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                                Tidak ada data satuan kerja terkait yang ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- 4. MODAL INTERAKTIF: RINCIAN NILAI & DOKUMEN SATUAN KERJA TERPILIH -->
<div class="modal fade" id="modalDetailNilaiLhe" tabindex="-1" aria-labelledby="modalDetailNilaiLheLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3 border-bottom">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center" id="modalDetailNilaiLheLabel">
                        <i class="bi bi-award text-primary me-2 fs-5"></i>
                        <span id="modalSatkerTitle">Rincian Evaluasi AKIP Satuan Kerja</span>
                    </h6>
                    <small class="text-muted" id="modalSatkerSubtitle">Tahun Anggaran {{ $tahun }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalDetailNilaiBody">
                <div class="text-center py-4" id="modalDetailLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat data...</span>
                    </div>
                    <div class="text-muted text-sm mt-2">Mengambil rincian nilai komponen dan subkomponen...</div>
                </div>
                <div id="modalDetailContent" style="display: none;">
                    <!-- Konten dinamis akan dimuat oleh JavaScript -->
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Client-side Search Table Rekapitulasi
    const searchInput = document.getElementById('filterSatkerLheTable');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#tableRekapLheWilayah tbody tr.satker-row');
            rows.forEach(row => {
                const nameEl = row.querySelector('.satker-name');
                const idEl = row.querySelector('.satker-id');
                const text = ((nameEl ? nameEl.textContent : '') + ' ' + (idEl ? idEl.textContent : '')).toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // 2. Modal Detail Nilai AJAX Handler
    const modalEl = document.getElementById('modalDetailNilaiLhe');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const loadingEl = document.getElementById('modalDetailLoading');
    const contentEl = document.getElementById('modalDetailContent');
    const titleEl = document.getElementById('modalSatkerTitle');
    const subtitleEl = document.getElementById('modalSatkerSubtitle');

    document.querySelectorAll('.btn-detail-nilai').forEach(btn => {
        btn.addEventListener('click', function () {
            const idSatker = this.getAttribute('data-id-satker');
            const namaSatker = this.getAttribute('data-nama-satker');

            if (titleEl) titleEl.textContent = namaSatker;
            if (subtitleEl) subtitleEl.textContent = 'ID: ' + idSatker + ' • Tahun Anggaran {{ $tahun }}';

            if (loadingEl) loadingEl.style.display = 'block';
            if (contentEl) {
                contentEl.style.display = 'none';
                contentEl.innerHTML = '';
            }

            if (modal) modal.show();

            fetch('{{ route('lhe.detail_nilai') }}?id_satker=' + encodeURIComponent(idSatker), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (loadingEl) loadingEl.style.display = 'none';
                if (!data.success) {
                    if (contentEl) {
                        contentEl.innerHTML = '<div class="alert alert-danger mb-0">' + (data.message || 'Gagal memuat data.') + '</div>';
                        contentEl.style.display = 'block';
                    }
                    return;
                }

                const scores = data.scores || {};
                const grandTotal = parseFloat(scores.grand_total || 0).toFixed(2);
                const predikat = scores.predikat_evaluasi || { predikat: '-', keterangan: 'Belum Dievaluasi', badge: 'bg-secondary' };
                const komponen = scores.komponen || {};
                const subkomponen = scores.subkomponen || {};
                const lheFiles = data.lhe_files || [];

                let html = `
                    <!-- Score Card Header -->
                    <div class="card border-0 rounded-3 shadow-sm text-white mb-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                        <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <small class="text-white-50 text-uppercase fw-semibold text-xs">Nilai Evaluasi AKIP</small>
                                <div class="display-6 fw-bold mb-0">${grandTotal}</div>
                            </div>
                            <div class="text-end">
                                <small class="text-white-50 d-block text-xs">Predikat AKIP</small>
                                <span class="badge ${predikat.badge || 'bg-secondary'} px-3 py-1.5 rounded-pill fs-6 fw-bold">
                                    ${predikat.predikat || '-'} &bull; ${predikat.keterangan || ''}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 4 Komponen Cards -->
                    <h6 class="fw-bold text-dark mb-2 text-sm">Rincian 4 Komponen Evaluasi:</h6>
                    <div class="row g-2 mb-4">
                `;

                for (const [kId, kData] of Object.entries(komponen)) {
                    const skor = parseFloat(kData.evaluasi_skor || 0).toFixed(2);
                    const max = kData.max_skor || 0;
                    const persen = parseFloat(kData.evaluasi_persentase || 0).toFixed(1);
                    html += `
                        <div class="col-sm-6">
                            <div class="border rounded-3 p-2.5 bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold text-dark text-xs">${kData.nama}</div>
                                    <span class="badge bg-white text-dark border text-xs">${skor} / ${max}</span>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-primary" style="width: ${Math.min(100, Math.max(0, persen))}%"></div>
                                </div>
                                <div class="text-end text-muted mt-1" style="font-size: 0.7rem;">Capaian: ${persen}%</div>
                            </div>
                        </div>
                    `;
                }

                html += `
                    </div>

                    <!-- Tabel 12 Subkomponen -->
                    <h6 class="fw-bold text-dark mb-2 text-sm">Rincian Subkomponen & Gradasi:</h6>
                    <div class="table-responsive rounded border mb-4">
                        <table class="table table-sm table-hover align-middle mb-0 text-xs">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">Kode</th>
                                    <th>Subkomponen</th>
                                    <th class="text-center" style="width: 60px;">Bobot</th>
                                    <th class="text-center" style="width: 70px;">Gradasi</th>
                                    <th class="text-center" style="width: 70px;">Skor</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                for (const [subKode, sub] of Object.entries(subkomponen)) {
                    const subSkor = parseFloat(sub.evaluasi_skor || 0).toFixed(2);
                    const subBobot = parseFloat(sub.bobot || 0).toFixed(1);
                    html += `
                        <tr>
                            <td class="fw-bold text-secondary font-monospace">${subKode}</td>
                            <td>${sub.nama}</td>
                            <td class="text-center text-muted">${subBobot}</td>
                            <td class="text-center">
                                <span class="badge ${sub.badge_eval || 'bg-secondary'} px-1.5 py-0.5 rounded text-2xs">
                                    ${sub.gradasi_eval || '-'}
                                </span>
                            </td>
                            <td class="text-center fw-bold">${subSkor}</td>
                        </tr>
                    `;
                }

                html += `
                            </tbody>
                        </table>
                    </div>

                    <!-- Dokumen LHE AKIP -->
                    <h6 class="fw-bold text-dark mb-2 text-sm">Dokumen LHE AKIP Tersimpan:</h6>
                `;

                if (lheFiles && lheFiles.length > 0) {
                    html += `<ul class="list-group list-group-flush border rounded-3 text-xs mb-0">`;
                    lheFiles.forEach(f => {
                        const fileUrl = '{{ asset('uploads/repository') }}/' + f.id_satker + '/' + f.id_filename;
                        html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <span class="fw-bold text-dark">LHE AKIP Tahun {{ $tahun }}</span>
                                    <span class="badge bg-light text-dark border ms-1">v${f.id_perubahan}</span>
                                    <small class="text-muted d-block">${f.id_filename} &bull; ${f.id_tglupload}</small>
                                </div>
                                <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 text-xs">
                                    <i class="bi bi-eye me-1"></i> Buka File
                                </a>
                            </li>
                        `;
                    });
                    html += `</ul>`;
                } else {
                    html += `<div class="alert alert-light border text-muted text-xs py-2 mb-0">Belum ada berkas dokumen LHE AKIP yang diunggah untuk Satker ini.</div>`;
                }

                if (contentEl) {
                    contentEl.innerHTML = html;
                    contentEl.style.display = 'block';
                }
            })
            .catch(err => {
                if (loadingEl) loadingEl.style.display = 'none';
                if (contentEl) {
                    contentEl.innerHTML = '<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat rincian data: ' + err + '</div>';
                    contentEl.style.display = 'block';
                }
            });
        });
    });
});
</script>
