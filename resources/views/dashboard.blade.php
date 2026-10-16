@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $satkernama = session('satkernama', 'Nama Satker');
    $idSatker = session('id_satker', 'ID Satker');
    $levelSakip = session('id_sakip_level', 0);
    $tahunUi = session('tahun_ui', $tahun ?? date('Y'));

    $isLkeJamwas = in_array((string)$idSatker, ['888882', 'Pengawasan', 'Panev'])
        || (strtolower($satkernama) === 'pengawasan' && (string)$idSatker !== '419346' && !str_starts_with(strtolower((string)$idSatker), 'was'));
    $isLkePengawasanKejati = str_starts_with(strtolower((string)$idSatker), 'was');
    $isUserPengawasan = $isLkeJamwas || $isLkePengawasanKejati;

    // Hitung persentase kepatuhan
    $kepatuhanItems = [
        ['nama' => 'Renstra', 'status' => $renstraTerisi, 'icon' => 'bi-file-earmark-ruled'],
        ['nama' => 'IKU', 'status' => $ikuTerisi, 'icon' => 'bi-bullseye'],
        ['nama' => 'Renja', 'status' => $renjaTerisi, 'icon' => 'bi-calendar2-check'],
        ['nama' => 'RKAKL', 'status' => $rkaklTerisi, 'icon' => 'bi-calculator'],
        ['nama' => 'DIPA', 'status' => $dipaTerisi, 'icon' => 'bi-cash-coin'],
        ['nama' => 'Rencana Aksi', 'status' => $rencanaAksiTerisi, 'icon' => 'bi-lightning-charge'],
    ];

    $terisiCount = collect($kepatuhanItems)->where('status', true)->count();
    $totalCount = count($kepatuhanItems);
    $persenKepatuhan = round(($terisiCount / $totalCount) * 100);
@endphp

<div class="content" id="content">
    <div class="container-fluid px-0">

        <!-- ========================================== -->
        <!-- EXECUTIVE WELCOME HERO BANNER              -->
        <!-- ========================================== -->
        <div class="card page-hero-card mb-4 border-0">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge bg-warning text-dark px-2 py-1 fw-bold text-xs">
                                <i class="bi bi-shield-check me-1"></i>Sistem Akuntabilitas Kinerja
                            </span>
                            <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 text-xs">
                                Tahun Anggaran: {{ $tahunUi }}
                            </span>
                        </div>
                        <h2 class="fw-bold mb-2 text-white">
                            Selamat Datang di PROSAKIP Kejaksaan RI
                        </h2>
                        <p class="text-white-50 mb-3 text-sm" style="max-width: 620px;">
                            Satuan Kerja: <strong class="text-white">{{ $satkernama }}</strong> (ID: {{ $idSatker }}). Kelola perencanaan, pelaporan, dan evaluasi akuntabilitas kinerja secara terpadu.
                        </p>
                        @if ($isUserPengawasan)
                            <div class="d-flex flex-wrap gap-2 pt-1">
                                <a href="{{ route('lke.evaluasi.index') }}" class="btn-cta-gold">
                                    <i class="bi bi-clipboard2-check me-1"></i> Evaluasi LKE AKIP
                                </a>
                                <a href="{{ route('evaluasi') }}" class="btn-cta-secondary">
                                    <i class="bi bi-upload me-1"></i> Upload Dokumen LHE AKIP
                                </a>
                            </div>
                        @else
                            <div class="d-flex flex-wrap gap-2 pt-1">
                                <a href="{{ route('perencanaan') }}" class="btn-cta-gold">
                                    <i class="bi bi-calendar2-range me-1"></i> Mulai Perencanaan
                                </a>
                                <a href="{{ route('pelaporan') }}" class="btn-cta-secondary">
                                    <i class="bi bi-file-earmark-bar-graph me-1"></i> Pelaporan Kinerja
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        @if ($isUserPengawasan)
                            <div class="d-inline-block kepatuhan-stat-box p-3 text-center text-white">
                                <div class="small text-white-50 text-uppercase fw-semibold mb-1 text-xs">Peran Akun</div>
                                <h4 class="fw-bold mb-0 text-warning">Pengawasan / Evaluator</h4>
                                <div class="small text-white-50 mt-1 text-xs">Evaluasi LKE & LHE AKIP</div>
                            </div>
                        @else
                            <div class="d-inline-block kepatuhan-stat-box p-3 text-center text-white">
                                <div class="small text-white-50 text-uppercase fw-semibold mb-1 text-xs">Kepatuhan Input</div>
                                <h2 class="fw-bold mb-0 text-warning">{{ $persenKepatuhan }}%</h2>
                                <div class="small text-white-50 mt-1 text-xs">{{ $terisiCount }} dari {{ $totalCount }} Terisi</div>
                                <div class="progress mt-2" style="height: 6px; background-color: rgba(255,255,255,0.2);">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $persenKepatuhan }}%;" aria-valuenow="{{ $persenKepatuhan }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris Utama (Kiri: Pengumuman & Kepatuhan; Kanan: Dokumen SAKIP) -->
        <div class="row g-4">

            <!-- ========================================== -->
            <!-- KOLOM KIRI                                 -->
            <!-- ========================================== -->
            <div class="col-lg-6">

                <!-- 1. Kepatuhan Dokumen SAKIP -->
                @if ($levelSakip != 0 && !$isUserPengawasan)
                    <div class="card mb-4">
                        <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 p-2 text-white bg-emerald">
                                    <i class="bi bi-check2-circle fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-dark text-md">Status Kepatuhan Dokumen</h5>
                                    <small class="text-muted text-xs">Kelengkapan pengisian dokumen perencanaan satker</small>
                                </div>
                            </div>
                            <span class="badge {{ $persenKepatuhan == 100 ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3 py-1_5 fw-bold text-xs">
                                {{ $persenKepatuhan == 100 ? 'Lengkap' : $terisiCount . '/' . $totalCount . ' Terisi' }}
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                @foreach ($kepatuhanItems as $item)
                                    <div class="col-sm-6">
                                        <div class="kpi-card {{ $item['status'] ? 'status-done' : 'status-pending' }} h-100">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="kpi-icon-circle">
                                                    <i class="bi {{ $item['status'] ? 'bi-check-lg' : 'bi-exclamation-triangle-fill' }}"></i>
                                                </div>
                                                <div class="overflow-hidden flex-grow-1">
                                                    <div class="fw-bold text-dark text-truncate text-sm">
                                                        Pengisian {{ $item['nama'] }}
                                                    </div>
                                                    <small class="d-block text-truncate {{ $item['status'] ? 'text-success' : 'text-danger' }} fw-semibold text-xs">
                                                        {{ $item['status'] ? 'Sudah Dilakukan' : 'Belum Dilakukan' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 2. Pengumuman Resmi -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 p-2 text-white bg-gold-dark">
                                <i class="bi bi-megaphone-fill fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark text-md">Pengumuman & Pemberitahuan</h5>
                                <small class="text-muted text-xs">Informasi terbaru dari Biro Perencanaan Kejaksaan RI</small>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark border px-2 py-1 text-xs">
                            {{ count($pengumuman) }} Pengumuman
                        </span>
                    </div>
                    <div class="card-body p-3">
                        @forelse ($pengumuman as $item)
                            <div class="announcement-item">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="mb-0 fw-bold text-danger d-flex align-items-center gap-1 text-sm">
                                        <i class="bi bi-pin-angle-fill text-warning"></i> {{ $item->judul }}
                                    </h6>
                                </div>
                                <p class="text-muted mb-0 text-sm" style="line-height: 1.5;">
                                    {{ $item->isi }}
                                </p>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                Belum ada pengumuman saat ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                @if (!$isUserPengawasan)
                <!-- 3. Akses Cepat: Sumber Aturan & FAQ -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="card h-100 border-0 shadow-sm quick-action-card-gold">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="rounded-3 p-2 bg-warning bg-opacity-25 text-warning-emphasis">
                                            <i class="bi bi-book-half fs-5"></i>
                                        </div>
                                        <h6 class="mb-0 fw-bold text-dark">Sumber Aturan</h6>
                                    </div>
                                    <p class="text-muted small mb-3">Kumpulan dasar hukum, juklak, dan regulasi SAKIP Kejaksaan RI.</p>
                                </div>
                                <a href="{{ route('aturan') }}" class="btn btn-yellow btn-sm w-100">
                                    <i class="bi bi-folder2-open me-1"></i> Buka Regulasi
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card h-100 border-0 shadow-sm quick-action-card-emerald">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="rounded-3 p-2 bg-success bg-opacity-25 text-success">
                                            <i class="bi bi-question-circle-fill fs-5"></i>
                                        </div>
                                        <h6 class="mb-0 fw-bold text-dark">Pusat FAQ</h6>
                                    </div>
                                    <p class="text-muted small mb-3">Tanya-jawab kendala dan panduan teknis pengisian SAKIP.</p>
                                </div>
                                <a href="{{ route('faq') }}" class="btn btn-emerald btn-sm w-100">
                                    <i class="bi bi-info-circle me-1"></i> Buka FAQ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- 4. Gambaran Alur SAKIP -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 p-2 text-white bg-emerald">
                                <i class="bi bi-diagram-3-fill fs-5"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark text-md">Gambaran Alur SAKIP</h5>
                        </div>
                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#imageModal">
                            <i class="bi bi-arrows-fullscreen me-1"></i> Perbesar
                        </button>
                    </div>
                    <div class="card-body text-center p-3">
                        <div class="position-relative overflow-hidden rounded-3 border bg-light cursor-pointer" role="button" data-bs-toggle="modal" data-bs-target="#imageModal">
                            <img src="{{ asset('gambar/sakip.png') }}" class="img-fluid zoom-on-hover" alt="Alur SAKIP Kejaksaan RI" style="max-height: 280px; object-fit: contain;">
                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-50 text-white py-1 small">
                                <i class="bi bi-zoom-in me-1"></i> Klik gambar untuk melihat ukuran penuh
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- AKHIR KOLOM KIRI -->

            <!-- ========================================== -->
            <!-- KOLOM KANAN: DOKUMEN & TEMPLATE SAKIP      -->
            <!-- ========================================== -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 p-2 text-white bg-gold-dark">
                                <i class="bi bi-collection-fill fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark text-md">Dokumen & Pedoman SAKIP</h5>
                                <small class="text-muted text-xs">Format resmi, pedoman JA, dan template dokumen kinerja</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3">

                        <!-- Pedoman & Aturan Pokok -->
                        @php $pedoman = $dokumenSakip->where('kategori', 'pedoman_ketentuan'); @endphp
                        @if($pedoman->count() > 0)
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary text-uppercase small mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i> Pedoman & Ketentuan
                            </h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach($pedoman as $item)
                                <a href="{{ $item->url }}" target="_blank" class="btn-linktree">
                                    <div class="doc-icon"><i class="bi {{ $item->icon }}"></i></div>
                                    <div class="overflow-hidden flex-grow-1">
                                        <div class="fw-bold text-truncate">{{ $item->judul }}</div>
                                        @if($item->deskripsi)
                                        <small class="text-muted text-truncate d-block">{{ $item->deskripsi }}</small>
                                        @endif
                                    </div>
                                    <i class="bi bi-box-arrow-up-right text-muted ms-auto"></i>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Template Tahun Berjalan -->
                        @php $template = $dokumenSakip->where('kategori', 'template_tahun_berjalan'); @endphp
                        @if($template->count() > 0)
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold text-secondary text-uppercase small mb-0 d-flex align-items-center gap-2">
                                    <i class="bi bi-folder-check text-success"></i> Template Dokumen SAKIP Tahun Aktif
                                </h6>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0">Tahun Aktif</span>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @foreach($template as $item)
                                <a href="{{ $item->url }}" target="_blank" class="btn-linktree">
                                    <div class="doc-icon"><i class="bi {{ $item->icon }}"></i></div>
                                    <div class="overflow-hidden flex-grow-1">
                                        <div class="fw-bold text-truncate">{{ $item->judul }}</div>
                                        @if($item->deskripsi)
                                        <small class="text-muted text-truncate d-block">{{ $item->deskripsi }}</small>
                                        @endif
                                    </div>
                                    <i class="bi bi-box-arrow-up-right text-muted ms-auto"></i>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Arsip Template -->
                        @php $arsip = $dokumenSakip->where('kategori', 'arsip_template'); @endphp
                        @if($arsip->count() > 0)
                        <div>
                            <h6 class="fw-bold text-secondary text-uppercase small mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-archive-fill text-muted"></i> Arsip Template Dokumen SAKIP
                            </h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach($arsip as $item)
                                <a href="{{ $item->url }}" target="_blank" class="btn-linktree">
                                    <div class="doc-icon"><i class="bi {{ $item->icon }}"></i></div>
                                    <div class="overflow-hidden flex-grow-1">
                                        <div class="fw-bold text-truncate">{{ $item->judul }}</div>
                                        @if($item->deskripsi)
                                        <small class="text-muted text-truncate d-block">{{ $item->deskripsi }}</small>
                                        @endif
                                    </div>
                                    <i class="bi bi-box-arrow-up-right text-muted ms-auto"></i>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($dokumenSakip->count() == 0)
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            Belum ada dokumen yang tersedia.
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            <!-- AKHIR KOLOM KANAN -->

        </div> <!-- Akhir Baris Utama -->

    </div>
</div>

<!-- Modal Zoom Alur SAKIP -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 overflow-hidden shadow-lg">
            <div class="modal-header text-white card-header-emerald">
                <h5 class="modal-title fw-bold" id="imageModalLabel">
                    <i class="bi bi-diagram-3-fill me-2 text-warning"></i>Gambaran Alur Penyelenggaraan SAKIP Kejaksaan RI
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-light">
                <img src="{{ asset('gambar/sakip.png') }}" class="img-fluid rounded-3 shadow-sm" alt="Alur SAKIP Ukuran Penuh">
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection
