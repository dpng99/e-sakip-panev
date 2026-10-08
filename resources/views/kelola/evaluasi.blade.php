@extends('layouts.app')

@section('title', 'Evaluasi Akuntabilitas Kinerja')

@section('content')
    @php
        $activeTab = session('active_tab', 'lke');
        $levelSakip = session('id_sakip_level', 0);
        $satkernama = session('satkernama', 'Nama Satker');
    @endphp

    <div class="content" id="content">
        <div class="container-fluid">
            <!-- Page Header Card -->
            <div class="card page-hero-card border-0 mb-4 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-warning text-dark px-2.5 py-1 fw-bold rounded-pill" style="font-size: 0.75rem;">
                                    <i class="bi bi-shield-check me-1"></i>TATA KELOLA AKIP
                                </span>
                                <span class="badge bg-white bg-opacity-25 text-white px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
                                    Tahun Anggaran {{ $tahun }}
                                </span>
                            </div>
                            <h2 class="fw-bold text-white mb-1 d-flex align-items-center gap-2">
                                <i class="bi bi-clipboard2-check-fill text-warning"></i> Evaluasi Akuntabilitas Kinerja
                            </h2>
                            <p class="text-white text-opacity-80 mb-0 small">
                                Pengelolaan Bukti Dukung LKE, Laporan Hasil Evaluasi (LHE), Tindak Lanjut Rekomendasi, dan Monitoring Rencana Aksi secara terpadu.
                            </p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div class="bg-white bg-opacity-10 backdrop-blur rounded-3 px-3 py-2 text-white border border-white border-opacity-20 text-md-end">
                                <small class="d-block text-white-50 text-uppercase fw-semibold" style="font-size: 0.7rem;">Satuan Kerja Aktif</small>
                                <span class="fw-bold fs-6">{{ $satkernama }}</span>
                                <span class="badge bg-warning text-dark ms-1">ID: {{ $idSatker }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card with Navigation Tabs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom p-0 px-3">
                    <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link py-3 px-3 {{ $activeTab == 'lke' ? 'active' : '' }}" id="lke-tab" data-bs-toggle="tab" href="#lke"
                                role="tab" aria-controls="lke" aria-selected="{{ $activeTab == 'lke' ? 'true' : 'false' }}">
                                <i class="bi bi-file-earmark-check-fill text-success me-1"></i> Bukti Dukung LKE
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link py-3 px-3 {{ $activeTab == 'lhe-akip' ? 'active' : '' }}" id="lhe-akip-tab"
                                data-bs-toggle="tab" href="#lhe-akip" role="tab"
                                aria-controls="lhe-akip" aria-selected="{{ $activeTab == 'lhe-akip' ? 'true' : 'false' }}">
                                <i class="bi bi-file-earmark-text-fill text-primary me-1"></i> LHE AKIP
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link py-3 px-3 {{ $activeTab == 'tl-lhe-akip' ? 'active' : '' }}" id="tl-lhe-akip-tab"
                                data-bs-toggle="tab" href="#tl-lhe-akip" role="tab"
                                aria-controls="tl-lhe-akip" aria-selected="{{ $activeTab == 'tl-lhe-akip' ? 'true' : 'false' }}">
                                <i class="bi bi-arrow-repeat text-warning me-1"></i> TL LHE AKIP
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link py-3 px-3 {{ $activeTab == 'monev-renaksi' ? 'active' : '' }}" id="monev-renaksi-tab"
                                data-bs-toggle="tab" href="#monev-renaksi" role="tab"
                                aria-controls="monev-renaksi" aria-selected="{{ $activeTab == 'monev-renaksi' ? 'true' : 'false' }}">
                                <i class="bi bi-graph-up-arrow text-info me-1"></i> Laporan Monev Renaksi
                            </a>
                        </li>
                        @if ($levelSakip == 99)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link py-3 px-3 {{ $activeTab == 'evaluasi-internal' ? 'active' : '' }}" id="evaluasi-internal-tab" data-bs-toggle="tab"
                                href="#evaluasi-internal" role="tab" aria-controls="evaluasi-internal" aria-selected="{{ $activeTab == 'evaluasi-internal' ? 'true' : 'false' }}">
                                <i class="bi bi-diagram-3-fill text-secondary me-1"></i> Evaluasi Internal
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link py-3 px-3 {{ $activeTab == 'evaluasi-rencana' ? 'active' : '' }}" id="evaluasi-rencana-tab" data-bs-toggle="tab"
                                href="#evaluasi-rencana" role="tab" aria-controls="evaluasi-rencana" aria-selected="{{ $activeTab == 'evaluasi-rencana' ? 'true' : 'false' }}">
                                <i class="bi bi-card-checklist text-secondary me-1"></i> Evaluasi Rencana Aksi
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="myTabContent">
                        <!-- 1. TAB BUKTI DUKUNG LKE -->
                        <div class="tab-pane fade {{ $activeTab == 'lke' ? 'show active' : '' }}"
                            id="lke" role="tabpanel" aria-labelledby="lke-tab">
                            @include('kelola.evaluasi.lke')
                        </div>

                        <!-- 2. TAB LHE AKIP -->
                        <div class="tab-pane fade {{ $activeTab == 'lhe-akip' ? 'show active' : '' }}" id="lhe-akip"
                            role="tabpanel" aria-labelledby="lhe-akip-tab">
                            
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1 text-dark">
                                        <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>Laporan Hasil Evaluasi (LHE) AKIP
                                    </h5>
                                    <p class="text-muted text-sm mb-0">Arsip dokumen LHE AKIP Satuan Kerja untuk Tahun Anggaran {{ $tahun }}.</p>
                                </div>
                            </div>

                            @if (session('success-lhe'))
                                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
                                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                    <div>{{ session('success-lhe') }}</div>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="row g-4">
                                <!-- Form Upload LHE AKIP -->
                                <div class="col-lg-4">
                                    <div class="card border shadow-sm rounded-3 h-100">
                                        <div class="card-header bg-light py-3 border-bottom">
                                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                                <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Upload Dokumen LHE AKIP
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <form action="{{ route('upload.lhe_akip') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="lhe_akip_file" class="form-label text-sm fw-semibold">
                                                        Pilih File PDF LHE AKIP
                                                    </label>
                                                    <input type="file" class="form-control" id="lhe_akip_file" name="lhe_akip_file" accept=".pdf" required>
                                                    <div class="form-text text-xs text-muted mt-1">
                                                        <i class="bi bi-info-circle me-1"></i>Format berkas .PDF, ukuran maksimal 4MB.
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                                    <i class="bi bi-cloud-arrow-up me-1"></i> Unggah Dokumen LHE
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Data LHE AKIP -->
                                <div class="col-lg-8">
                                    <div class="card border shadow-sm rounded-3">
                                        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                                <i class="bi bi-archive-fill text-secondary me-2"></i>Daftar Berkas LHE AKIP Terunggah
                                            </h6>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                                                {{ $lheAkipFiles->count() }} Dokumen
                                            </span>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center" style="width: 60px;">No</th>
                                                        <th>Nama File Dokumen</th>
                                                        <th class="text-center" style="width: 100px;">Versi</th>
                                                        <th>Tanggal Upload</th>
                                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($lheAkipFiles as $index => $file)
                                                        <tr>
                                                            <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-4 me-2"></i>
                                                                    <div>
                                                                        <div class="fw-bold text-dark text-sm">LHE AKIP ({{ $tahun }})</div>
                                                                        <div class="text-muted text-xs font-monospace">{{ $file->id_filename }}</div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-light text-dark border">v{{ $file->id_perubahan }}</span>
                                                            </td>
                                                            <td class="text-sm text-muted">
                                                                <i class="bi bi-clock me-1 text-secondary"></i>{{ $file->id_tglupload }}
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="{{ asset('uploads/repository/' . $file->id_satker . '/' . $file->id_filename) }}"
                                                                    target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2.5">
                                                                    <i class="bi bi-eye me-1"></i> Buka File
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4 text-muted">
                                                                <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                                                Belum ada dokumen LHE AKIP yang diunggah.
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

                        <!-- 3. TAB TL LHE AKIP -->
                        <div class="tab-pane fade {{ $activeTab == 'tl-lhe-akip' ? 'show active' : '' }}"
                            id="tl-lhe-akip" role="tabpanel" aria-labelledby="tl-lhe-akip-tab">
                            
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1 text-dark">
                                        <i class="bi bi-arrow-repeat text-warning me-2"></i>Tindak Lanjut Laporan Hasil Evaluasi (TL LHE) AKIP
                                    </h5>
                                    <p class="text-muted text-sm mb-0">Dokumen tindak lanjut dan rekomendasi perbaikan atas evaluasi AKIP.</p>
                                </div>
                            </div>

                            @if (session('success-tllhe'))
                                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
                                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                    <div>{{ session('success-tllhe') }}</div>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="row g-4">
                                <!-- Form Upload TL LHE AKIP -->
                                <div class="col-lg-4">
                                    <div class="card border shadow-sm rounded-3 h-100">
                                        <div class="card-header bg-light py-3 border-bottom">
                                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                                <i class="bi bi-cloud-arrow-up-fill text-warning me-2"></i>Upload Dokumen TL LHE AKIP
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <form action="{{ route('upload.tl_lhe_akip') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="tllhe_file" class="form-label text-sm fw-semibold">
                                                        Pilih File PDF TL LHE AKIP
                                                    </label>
                                                    <input type="file" class="form-control" id="tllhe_file" name="tllhe_file" accept=".pdf" required>
                                                    <div class="form-text text-xs text-muted mt-1">
                                                        <i class="bi bi-info-circle me-1"></i>Format berkas .PDF, ukuran maksimal 4MB.
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-warning text-dark fw-bold w-100 shadow-sm">
                                                    <i class="bi bi-cloud-arrow-up me-1"></i> Unggah Dokumen TL LHE
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Data TL LHE AKIP -->
                                <div class="col-lg-8">
                                    <div class="card border shadow-sm rounded-3">
                                        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                                <i class="bi bi-archive-fill text-secondary me-2"></i>Daftar Dokumen TL LHE Terunggah
                                            </h6>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                                                {{ $tlLheAkipFiles->count() }} Dokumen
                                            </span>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center" style="width: 60px;">No</th>
                                                        <th>Nama File Dokumen</th>
                                                        <th class="text-center" style="width: 100px;">Versi</th>
                                                        <th>Tanggal Upload</th>
                                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($tlLheAkipFiles as $index => $file)
                                                        <tr>
                                                            <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-4 me-2"></i>
                                                                    <div>
                                                                        <div class="fw-bold text-dark text-sm">TL LHE AKIP ({{ $tahun }})</div>
                                                                        <div class="text-muted text-xs font-monospace">{{ $file->id_filename }}</div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-light text-dark border">v{{ $file->id_perubahan }}</span>
                                                            </td>
                                                            <td class="text-sm text-muted">
                                                                <i class="bi bi-clock me-1 text-secondary"></i>{{ $file->id_tglupload }}
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="{{ asset('uploads/repository/' . $file->id_satker . '/' . $file->id_filename) }}"
                                                                    target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2.5">
                                                                    <i class="bi bi-eye me-1"></i> Buka File
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4 text-muted">
                                                                <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                                                Belum ada dokumen TL LHE AKIP yang diunggah.
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

                        <!-- 4. TAB LAPORAN MONEV RENAKSI -->
                        <div class="tab-pane fade {{ $activeTab == 'monev-renaksi' ? 'show active' : '' }}"
                            id="monev-renaksi" role="tabpanel" aria-labelledby="monev-renaksi-tab">
                            
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1 text-dark">
                                        <i class="bi bi-graph-up-arrow text-info me-2"></i>Laporan Monev Rencana Aksi Kinerja
                                    </h5>
                                    <p class="text-muted text-sm mb-0">Laporan pemantauan dan evaluasi berkala pelaksanaan rencana aksi kinerja per triwulan.</p>
                                </div>
                            </div>

                            @if (session('success-monev'))
                                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
                                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                    <div>{{ session('success-monev') }}</div>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="row g-4">
                                <!-- Form Upload Monev Renaksi -->
                                <div class="col-lg-4">
                                    <div class="card border shadow-sm rounded-3 h-100">
                                        <div class="card-header bg-light py-3 border-bottom">
                                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                                <i class="bi bi-cloud-arrow-up-fill text-info me-2"></i>Upload Dokumen Monev Renaksi
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <form action="{{ route('upload.monev_renaksi') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="id_triwulan" class="form-label text-sm fw-semibold">Pilih Periode Triwulan</label>
                                                    <select class="form-select" id="id_triwulan" name="id_triwulan" required>
                                                        <option value="" disabled selected>-- Pilih Triwulan --</option>
                                                        <option value="TW 1">Triwulan 1 (TW 1)</option>
                                                        <option value="TW 2">Triwulan 2 (TW 2)</option>
                                                        <option value="TW 3">Triwulan 3 (TW 3)</option>
                                                        <option value="TW 4">Triwulan 4 (TW 4)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="monev_file" class="form-label text-sm fw-semibold">Upload File PDF (Max: 4MB)</label>
                                                    <input type="file" class="form-control" id="monev_file" name="monev_file" accept=".pdf" required>
                                                </div>
                                                <button type="submit" class="btn btn-info text-white fw-bold w-100 shadow-sm">
                                                    <i class="bi bi-cloud-arrow-up me-1"></i> Unggah Laporan Monev
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Data Monev Renaksi -->
                                <div class="col-lg-8">
                                    <div class="card border shadow-sm rounded-3">
                                        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                                <i class="bi bi-archive-fill text-secondary me-2"></i>Daftar Berkas Monev Renaksi Terunggah
                                            </h6>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                                                {{ $monevRenaksiFiles->count() }} Dokumen
                                            </span>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center" style="width: 60px;">No</th>
                                                        <th>Nama File Dokumen</th>
                                                        <th class="text-center">Triwulan</th>
                                                        <th class="text-center" style="width: 90px;">Versi</th>
                                                        <th>Tanggal Upload</th>
                                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($monevRenaksiFiles as $index => $file)
                                                        <tr>
                                                            <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-4 me-2"></i>
                                                                    <div>
                                                                        <div class="fw-bold text-dark text-sm">Monev Renaksi ({{ $tahun }})</div>
                                                                        <div class="text-muted text-xs font-monospace">{{ $file->id_filename }}</div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-info-subtle text-info border border-info-subtle">{{ $file->id_triwulan }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-light text-dark border">v{{ $file->id_perubahan }}</span>
                                                            </td>
                                                            <td class="text-sm text-muted">
                                                                <i class="bi bi-clock me-1 text-secondary"></i>{{ $file->id_tglupload }}
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="{{ asset('uploads/repository/' . $file->id_satker . '/' . $file->id_filename) }}"
                                                                    target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2.5">
                                                                    <i class="bi bi-eye me-1"></i> Buka File
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center py-4 text-muted">
                                                                <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                                                Belum ada dokumen Monev Renaksi yang diunggah.
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
                        
                        @if ($levelSakip == 99)
                        <!-- 5. TAB EVALUASI INTERNAL -->
                        <div class="tab-pane fade {{ $activeTab == 'evaluasi-internal' ? 'show active' : '' }}"
                            id="evaluasi-internal" role="tabpanel" aria-labelledby="evaluasi-internal-tab">
                            <h5 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-diagram-3-fill text-secondary me-2"></i>Evaluasi Internal AKIP
                            </h5>
                            @include('kelola.components.evaluasi_lke', ['sections' => $sections ?? []])
                        </div>

                        <!-- 6. TAB EVALUASI RENCANA AKSI -->
                        <div class="tab-pane fade {{ $activeTab == 'evaluasi-rencana' ? 'show active' : '' }}"
                            id="evaluasi-rencana" role="tabpanel" aria-labelledby="evaluasi-rencana-tab">
                            <h5 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-card-checklist text-secondary me-2"></i>Evaluasi Rencana Aksi
                            </h5>
                            <div class="p-4 bg-light rounded-3 text-center text-muted">
                                <i class="bi bi-gear-wide-connected fs-1 text-secondary opacity-50 mb-2 d-block"></i>
                                Modul Evaluasi Rencana Aksi dalam pemantauan berkala.
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
