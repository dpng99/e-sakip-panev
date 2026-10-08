@extends('layouts.app')

@section('title', 'Pelaporan')

@section('content')
    @php
        $levelSakip = session('id_sakip_level', 0);
    @endphp
    <div class="content" id="content">
        <div class="container-fluid">
            <!-- Page Header Hero Card -->
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
                                <i class="bi bi-file-earmark-bar-graph-fill text-warning"></i> Pelaporan Kinerja
                            </h2>
                            <p class="text-white text-opacity-80 mb-0 small">
                                Pengelolaan Laporan Kinerja Instansi Pemerintah (LKJiP), Capaian Kinerja Triwulan, dan Rapat Staf EKA.
                            </p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div class="bg-white bg-opacity-10 backdrop-blur rounded-3 px-3 py-2 text-white border border-white border-opacity-20 text-md-end">
                                <small class="d-block text-white-50 text-uppercase fw-semibold" style="font-size: 0.7rem;">Satuan Kerja Aktif</small>
                                <span class="fw-bold fs-6">{{ session('satkernama', 'Nama Satker') }}</span>
                                <span class="badge bg-warning text-dark ms-1">ID: {{ session('id_satker') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom p-0 px-3">
                    @php
                        $activeTab = session('active_tab', 'lkjip');
                    @endphp
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs border-0" id="myTab" role="tablist">

                        @if ($levelSakip == 99)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $activeTab == 'triwulan2' ? 'active' : '' }}" id="triwulan2-tab"
                                    data-bs-toggle="tab" href="#triwulan2" role="tab"
                                    aria-controls="{{ $activeTab == 'triwulan2' ? 'true' : 'false' }}"
                                    aria-selected="false">Capaian Triwulan II</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $activeTab == 'triwulan3' ? 'active' : '' }}" id="triwulan3-tab"
                                    data-bs-toggle="tab" href="#triwulan3" role="tab"
                                    aria-controls="{{ $activeTab == 'triwulan3' ? 'true' : 'false' }}"
                                    aria-selected="false">Capaian Triwulan III</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $activeTab == 'triwulan4' ? 'active' : '' }}" id="triwulan4-tab"
                                    data-bs-toggle="tab" href="#triwulan4" role="tab"
                                    aria-controls="{{ $activeTab == 'triwulan4' ? 'true' : 'false' }}"
                                    aria-selected="false">Capaian Triwulan IV</a>
                            </li>
                        @endif

                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'lkjip' ? 'active' : '' }}" id="lkjip-tab"
                                data-bs-toggle="tab" href="#lkjip" role="tab"
                                aria-controls="{{ $activeTab == 'lkjip' ? 'true' : 'false' }}" aria-selected="false">Laporan
                                Kinerja (LKJiP)</a>
                        </li>

                        @if ($tahun != 2024)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $activeTab == 'capaian' ? 'active' : '' }}" id="capaian-tab"
                                    data-bs-toggle="tab" href="#capaian" role="tab"
                                    aria-controls="{{ $activeTab == 'capaian' ? 'true' : 'false' }}"
                                    aria-selected="true">Capaian Kinerja</a>
                            </li>
                        @endif

                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'rapat-staff-eka' ? 'active' : '' }}"
                                id="rapat-staff-eka-tab" data-bs-toggle="tab" href="#rapat-staff-eka" role="tab"
                                aria-controls="{{ $activeTab == 'rapat-staff-eka' ? 'true' : 'false' }}"
                                aria-selected="false">Rapat Staff EKA</a>
                        </li>


                        @if ($levelSakip == 99)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $activeTab == 'validasi-apip' ? 'active' : '' }}"
                                    id="validasi-apip-tab" data-bs-toggle="tab" href="#validasi-apip" role="tab"
                                    aria-controls="{{ $activeTab == 'validasi-apip' ? 'true' : 'false' }}"
                                    aria-selected="false">Validasi APIP</a>
                            </li>
                        @endif
                    </ul>

                    @php
                        use App\Models\Bidang;
                    @endphp
                    <!-- Tabs Content -->
                    <div class="tab-content mt-3" id="myTabContent">
                        {{-- @if ($levelSakip == 99) --}}
                        <div class="tab-pane fade {{ $activeTab == 'capaian' ? 'show active' : '' }}" id="capaian"
                            role="tabpanel" aria-labelledby="capaian-tab">
                            <h5>Capaian Kinerja</h5>
                            @if ($levelSakip == 1)
                                {{-- ===== MODE LEVEL 1: Capaian Kinerja IKP Kejaksaan Agung ===== --}}
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-header bg-warning">
                                                <strong>📌 Daftar Bidang</strong>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                    $satkerBidangs = \App\Http\Controllers\PengukuranIkpController::getFilteredBidangs();
                                                @endphp
                                                @foreach ($satkerBidangs as $sb)
                                                    <button class="btn btn-outline-success text-black w-100 mb-2 text-start bidang-ikp-item"
                                                        data-satker-id="{{ $sb['id'] }}"
                                                        data-satker-nama="{{ $sb['nama'] }}">
                                                        <i class="bi bi-folder2-open me-2 text-success"></i>
                                                        <span class="bidang-nama-text">{{ $sb['nama'] }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-9">
                                        <div class="card" id="ikp-controls-wrapper" style="display:none;">
                                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span>📋 Indikator Kinerja Program</span>
                                                    <select id="triwulan-ikp" class="form-select w-auto">
                                                        <option value="1" selected>Triwulan 1</option>
                                                        <option value="2">Triwulan 2</option>
                                                        <option value="3">Triwulan 3</option>
                                                        <option value="4">Triwulan 4</option>
                                                    </select>
                                                    <button id="reloadIkpBtn" class="btn btn-success btn-sm">Pilih</button>
                                                </div>
                                                <div class="d-flex align-items-center gap-2" id="pelaporan-accordion-actions">
                                                    <button type="button" class="btn btn-sm btn-outline-dark" id="btn-pelaporan-expand-all" title="Buka Semua Sasaran Program">
                                                        <i class="bi bi-arrows-expand me-1"></i> Buka Semua
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-dark" id="btn-pelaporan-collapse-all" title="Tutup Semua Sasaran Program">
                                                        <i class="bi bi-arrows-collapse me-1"></i> Tutup Semua
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body" id="ikp-subindikator-wrapper">
                                                <strong>(Indikator Kinerja Program)</strong>
                                                <div class="ikp-wrapper">Pilih Bidang Terlebih Dahulu</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- ===== MODE LEVEL SATKER (Kejati/Kejari): Existing Capaian Kinerja ===== --}}
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-header bg-warning">
                                                <strong>📌 Daftar Bidang</strong>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                    $level = session('id_sakip_level');
                                                    $satkernama = session('satkernama') ?? '';
                                                    $kataTerakhir = strtolower(strrchr(' ' . $satkernama, ' '));
                                                    if ($level == 0) {
                                                        // Admin atau superuser: ambil semua bidang
                                                        $bidangs = \App\Models\Bidang::whereNotNull('bidang_level')
                                                            ->where('hide', 0)
                                                            ->orderBy('bidang_lokasi', 'asc')
                                                            ->orderBy('bidang_level', 'asc')
                                                            ->get();
                                                    } elseif ($level == 1) {
                                                        $bidangs = \App\Models\Bidang::where('bidang_lokasi', $level)
                                                            ->where('hide', 0)
                                                            ->whereRaw("LOWER(REPLACE(bidang_nama, '_', ' ')) LIKE ?", [
                                                                '%' . strtolower(trim($kataTerakhir)),
                                                            ])
                                                            ->whereNotNull('bidang_level')
                                                            ->orderBy('bidang_level', 'asc')
                                                            ->get();
                                                    } elseif (str_starts_with(strtoupper($satkernama), 'CABJARI')) {
                                                        $bidangs = \App\Models\Bidang::where('bidang_lokasi', $level)
                                                            ->whereNotNull('bidang_level')
                                                            ->orderBy('bidang_level', 'asc')
                                                            ->get();

                                                        if (
                                                            $bidangs->isNotEmpty() &&
                                                            stripos($bidangs[0]->bidang_nama, 'kepala') === 0
                                                        ) {
                                                            $bidangs[0]->bidang_nama = 'Kepala Cabang Kejaksaan Negeri';
                                                        }
                                                    } elseif ($level > 1) {
                                                        $bidangs = \App\Models\Bidang::where('bidang_lokasi', $level)
                                                            ->whereNotNull('bidang_level')
                                                            ->orderBy('bidang_level', 'asc')
                                                            ->get();
                                                    }
                                                @endphp
                                                @foreach ($bidangs as $bidang)
                                                    @php
                                                        $namaBidang = $bidang->bidang_nama;
                                                        $upperName = strtoupper(trim($namaBidang));
                                                        if (str_starts_with($upperName, 'ASISTEN ')) {
                                                            $namaBidang = 'Bidang ' . ucwords(strtolower(trim(substr($namaBidang, 8))));
                                                        } elseif (str_starts_with($upperName, 'KEPALA SEKSI ')) {
                                                            $namaBidang = 'Bidang ' . ucwords(strtolower(trim(substr($namaBidang, 13))));
                                                        } elseif (str_starts_with($upperName, 'KASI ')) {
                                                            $namaBidang = 'Bidang ' . ucwords(strtolower(trim(substr($namaBidang, 5))));
                                                        }
                                                    @endphp
                                                    <button class="btn btn-outline-success text-black w-100 mb-2 bidang-item"
                                                        data-rumpun="{{ $bidang->rumpun }}">
                                                        {{ $namaBidang }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-9">
                                        <div class="card" id="controls-wrapper" class="mb-3" style="display:none;">
                                            <div class="card-header d-flex align-items-center gap-2">
                                                <span>📋 Indikator</span>
                                                @php
                                                    $tahun = session('tahun_terpilih');
                                                    $khusus = session('tw4_khusus');
                                                @endphp

                                                <select id="triwulan" class="form-select w-auto">
                                                    @if ($tahun == 2025)
                                                        @if ($khusus == 1)
                                                            <option value="4" selected>Triwulan 4</option>
                                                        @else
                                                            <option value="1" selected>Triwulan 1</option>
                                                            <option value="2">Triwulan 2</option>
                                                            <option value="3">Triwulan 3</option>
                                                        @endif
                                                    @else
                                                        <option value="1" selected>Triwulan 1</option>
                                                        <option value="2">Triwulan 2</option>
                                                        <option value="3">Triwulan 3</option>
                                                        <option value="4">Triwulan 4</option>
                                                    @endif
                                                </select>

                                                <button id="reloadBtn" class="btn btn-success">Pilih</button>
                                            </div>
                                            <div class="card-body" id="subindikator-wrapper">
                                                <form method="POST" action="#">
                                                    <strong>(Sub Indikator)</strong>
                                                    <div class="subindikator-wrapper">Pilih Bidang Terlebih dahulu</div>

                                                    <div class="text-end mt-4">
                                                        <button type="submit" class="btn btn-success" id="btn-simpan"
                                                            style="background-color: #198754; color: white; display: none;">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        {{-- @endif --}}
                        @if ($levelSakip == 99)
                            <div class="tab-pane fade {{ $activeTab == 'triwulan2' ? 'show active' : '' }}"
                                id="triwulan2" role="tabpanel" aria-labelledby="triwulan2-tab">
                                <h5>Capaian Triwulan II</h5>
                                <p>Content for Capaian Triwulan II goes here.</p>
                            </div>
                            <div class="tab-pane fade {{ $activeTab == 'triwulan3' ? 'show active' : '' }}"
                                id="triwulan3" role="tabpanel" aria-labelledby="triwulan3-tab">
                                <h5>Capaian Triwulan III</h5>
                                <p>Content for Capaian Triwulan III goes here.</p>
                            </div>
                            <div class="tab-pane fade {{ $activeTab == 'triwulan4' ? 'show active' : '' }}"
                                id="triwulan4" role="tabpanel" aria-labelledby="triwulan4-tab">
                                <h5>Capaian Triwulan IV</h5>
                                <p>Content for Capaian Triwulan IV goes here.</p>
                            </div>
                        @endif

                        <div class="tab-pane fade {{ $activeTab == 'lkjip' ? 'show active' : '' }}" id="lkjip"
                            role="tabpanel" aria-labelledby="lkjip-tab">
                            <h5>Laporan Kinerja (LKJiP)</h5>

                            <!-- Alert for success -->
                            @if (session('success-lkjip'))
                                <div class="alert alert-success" id="success-alert">
                                    {{ session('success-lkjip') }}
                                </div>
                            @endif

                            <!-- Form Upload File -->
                            <div class="card shadow-sm mb-3">
                                <div class="card-header text-white" style="background-color: #e6bf3e;">
                                    <h6 class="mb-0">Upload Dokumen LKJiP</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('upload.lkjip') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="id_triwulan" class="form-label">Pilih Triwulan</label>
                                            <select class="form-control" id="id_triwulan" name="id_triwulan" required>
                                                <option value="" disabled selected>Pilih Triwulan</option>
                                                <option value="TW 1">Triwulan 1</option>
                                                <option value="TW 2">Triwulan 2</option>
                                                <option value="TW 3">Triwulan 3</option>
                                                <option value="TW 4">Triwulan 4</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="lkjip_file" class="form-label">Upload File PDF (Max:
                                                4MB)</label>
                                            <input type="file" class="form-control" id="lkjip_file" name="lkjip_file"
                                                accept=".pdf" required>
                                        </div>
                                        <button type="submit" class="btn btn-block"
                                            style="background-color: #e6bf3e;">Upload File</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Tabel Data LKJiP -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-warning"> <!-- Mengubah warna header tabel menjadi kuning -->
                                        <tr>
                                            <th>No</th>
                                            <th>Nama File</th>
                                            <th>Triwulan</th>
                                            <th>Versi</th>
                                            <th>Tanggal Upload</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lkjipFiles as $index => $file)
                                            <tr class="bg-warning bg-opacity-25">
                                                <!-- Mengubah warna isi tabel menjadi kuning muda -->
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @php
                                                        if ($tahun == 2024) {
                                                            if (!empty($file->id_triwulan)) {
                                                                // 2024 tapi ada triwulan
                                                                $finalPath =
                                                                    'uploads/repository/' .
                                                                    $file->id_satker .
                                                                    '/lkjip_' .
                                                                    $tahun .
                                                                    '_' .
                                                                    $file->id_perubahan .
                                                                    '_' .
                                                                    $file->id_triwulan .
                                                                    '.pdf';
                                                            } else {
                                                                // 2024 tanpa triwulan
                                                                $finalPath =
                                                                    'uploads/repository/' .
                                                                    $file->id_satker .
                                                                    '/lkjip_' .
                                                                    $tahun .
                                                                    '_' .
                                                                    $file->id_perubahan .
                                                                    '.pdf';
                                                            }
                                                        } else {
                                                            // tahun selain 2024 langsung pakai triwulan
                                                            $finalPath =
                                                                'uploads/repository/' .
                                                                $file->id_satker .
                                                                '/lkjip_' .
                                                                $tahun .
                                                                '_' .
                                                                $file->id_perubahan .
                                                                '_' .
                                                                $file->id_triwulan .
                                                                '.pdf';
                                                        }
                                                    @endphp

                                                    <a href="{{ asset($finalPath) }}" target="_blank">
                                                        LKJIP ({{ $tahun }}) - Triwulan {{ $file->id_triwulan }}
                                                    </a>

                                                </td>
                                                <td>Triwulan {{ $file->id_triwulan }}</td>
                                                <td>{{ $file->id_perubahan }}</td>
                                                <td>{{ $file->id_tglupload }}</td>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <!-- Rapat Staff EKA Section -->
                        <div class="tab-pane fade {{ $activeTab == 'rapat-staff-eka' ? 'show active' : '' }}"
                            id="rapat-staff-eka" role="tabpanel" aria-labelledby="rapat-staff-eka-tab">
                            <h5>Rapat Staff EKA</h5>

                            <!-- Alert for success -->
                            @if (session('success-rastaff'))
                                <div class="alert alert-success" id="success-alert">
                                    {{ session('success-rastaff') }}
                                </div>
                            @endif

                            <!-- Form Upload File -->
                            <div class="card shadow-sm mb-3">
                                <div class="card-header text-white" style="background-color: #e6bf3e;">
                                    <h6 class="mb-0">Upload Dokumen Rapat Staff EKA</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('upload.rapat_staff_eka') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="id_triwulan" class="form-label">Pilih Triwulan</label>
                                            <select class="form-control" id="id_triwulan" name="id_triwulan" required>
                                                <option value="" disabled selected>Pilih Triwulan</option>
                                                <option value="TW 1">Triwulan 1</option>
                                                <option value="TW 2">Triwulan 2</option>
                                                <option value="TW 3">Triwulan 3</option>
                                                <option value="TW 4">Triwulan 4</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="rapat_file" class="form-label">Upload File PDF (Max:
                                                4MB)</label>
                                            <input type="file" class="form-control" id="rapat_file" name="rapat_file"
                                                accept=".pdf" required>
                                        </div>
                                        <button type="submit" class="btn btn-block"
                                            style="background-color: #e6bf3e;">Upload File</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Tabel Data Rapat Staff EKA -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-warning">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama File</th>
                                            <th>Triwulan</th>
                                            <th>Versi</th>
                                            <th>Tanggal Upload</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rapatStaffEkaFiles as $index => $file)
                                            <tr class="bg-warning bg-opacity-25">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <a href="{{ asset('uploads/repository/' . $file->id_satker . '/rastaff_' . $tahun . '_' . $file->id_perubahan . '_' . $file->id_triwulan . '.pdf') }}"
                                                        target="_blank">
                                                        Rapat Staff EKA
                                                        ({{ $tahun }})
                                                        -
                                                        {{ $file->id_triwulan }}
                                                    </a>
                                                </td>
                                                <td>{{ $file->id_triwulan }}</td>
                                                <td>{{ $file->id_perubahan }}</td>
                                                <td>{{ $file->id_tglupload }}</td>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if ($levelSakip == 99)
                            <div class="tab-pane fade {{ $activeTab == 'validasi-apip' ? 'show active' : '' }}"
                                id="validasi-apip" role="tabpanel" aria-labelledby="validasi-apip-tab">
                                <h2>Validasi APIP</h2>

                                <!-- Alert for success -->
                                @if (session('success-validasi'))
                                    <div class="alert alert-success" id="success-alert">
                                        {{ session('success-validasi') }}
                                    </div>
                                @endif

                                <p>Content for IKU goes here...</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Modal Notifikasi -->
            <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="notifModalLabel">Peringatan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body text-center">
                            <p id="notifModalMessage" class="fs-5"></p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('styles')
    <style>
        .nav-tabs .nav-link {
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            margin-right: -1px;
        }

        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .nav-tabs {
            border-bottom: 1px solid #ddd;
        }

        .tab-content {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            background-color: #f8f9fa;
        }

        .tab-pane {
            min-height: 200px;
            /* Adjust as needed */
        }
    </style>
@endsection

@section('scripts')
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- JavaScript untuk auto-hide alert -->
        <script>
            setTimeout(function() {
                document.getElementById('success-alert')?.remove();
            }, 3000); // Alert hilang setelah 3 detik
        </script>
        <script>
            $(document).ready(function() {
                let selectedRumpun = null;
                let indikator = {
                    indikator_penghitungan: "{{ $indikator->indikator_penghitungan ?? '' }}"
                };
                let labelPenghitungan = ['Ditangani', 'Diselesaikan'];
                if (indikator.indikator_penghitungan) {
                    labelPenghitungan = indikator.indikator_penghitungan.split(',').map(s => s.trim());
                }
                // console.log(labelPenghitungan);

                // Ketika klik bidang
                $('.bidang-item').on('click', function() {
                    // pastikan value triwulan sesuai option blade
                $('#triwulan').trigger('change');

                    $(".bidang-item").removeClass("active");
                    $(this).addClass("active");

                    selectedRumpun = $(this).data('rumpun');
                    $("#controls-wrapper").show();
                    let triwulan = $('#triwulan').val();
                    loadSubIndikator(selectedRumpun, triwulan);
                });

                // Ketika ganti triwulan
                $('#triwulan').on('change', function() {
                    let triwulan = $(this).val();
                    console.log('Triwulan ganti:', triwulan, 'Rumpun:', selectedRumpun);
                    if (selectedRumpun) {
                        loadSubIndikator(selectedRumpun, triwulan);
                    }
                });

                // Klik tombol reload
                $(document).on('click', '#reloadBtn', function() {
                    $(this).addClass("active");

                    let triwulan = $('#triwulan').val();
                    loadSubIndikator(selectedRumpun, triwulan);
                });


                function loadSubIndikator(rumpun, triwulan) {
                    $('#subindikator-wrapper').html('<p>Loading...</p>');

                    $.ajax({
                        url: `/pelaporan/subindikator/${rumpun}`,
                        data: {
                            triwulan
                        },
                        success: function(data) {
                            let html = '';

                            if (!data || data.length === 0) {
                                html = '<p class="text-muted">Tidak ada indikator ditemukan.</p>';
                            }

                            data.forEach((item) => {
                                if (!item.indikator_nama) return; // skip data kosong

                                let labelPenghitungan = ['Ditangani', 'Diselesaikan'];
                                if (item.indikator_penghitungan) {
                                    let labels = item.indikator_penghitungan.split(',').map(s => s
                                        .trim());
                                    if (labels.length === 2 && labels[0] && labels[1]) {
                                        labelPenghitungan = labels;
                                    }
                                }

                                html += `
                <div class="mb-4 p-3 border rounded shadow-sm">
                    <strong>${item.indikator_nama}</strong>
                    <table class="table table-bordered align-middle mt-3">
                        <thead class="text-center bg-warning">
                            <tr>
                                <th>Persentase Penyelesaian</th>
                                <th>Target PK Tahunan</th>
                                <th>Capaian Target PK Tahunan</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr>
                                <td>${item.persentase || 0}${['indeks', 'nilai', 'opini', 'level', 'rasio'].some(w => item.indikator_nama && item.indikator_nama.toLowerCase().includes(w)) ? '' : '%'}</td>
                                <td>${item.target_pk || 0}${['indeks', 'nilai', 'opini', 'level', 'rasio'].some(w => item.indikator_nama && item.indikator_nama.toLowerCase().includes(w)) ? '' : '%'}</td>
                                <td>${item.capaian_pk || 0}${['indeks', 'nilai', 'opini', 'level', 'rasio'].some(w => item.indikator_nama && item.indikator_nama.toLowerCase().includes(w)) ? '' : '%'}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mb-2">
                        <label><strong>Faktor-Faktor:</strong></label>
                        <textarea class="form-control faktor-input" rows="2"
                            data-indikator-id="${item.indikator_id}">${item.faktor || ''}</textarea>
                    </div>

                    <div class="mb-2">
                        <label><strong>Upaya Optimalisasi:</strong></label>
                        <textarea class="form-control langkah-input" rows="2"
                            data-indikator-id="${item.indikator_id}">${item.langkah || ''}</textarea>
                    </div>

                    <button class="btn btn-sm btn-primary btn-simpan-faktor"
                        data-indikator-id="${item.indikator_id}">Simpan</button>
                </div>
                `;
                            });

                            $('#subindikator-wrapper').html(html);
                        },
                        error: function() {
                            $('#subindikator-wrapper').html(
                                '<p class="text-danger">Gagal memuat data.</p>');
                        }
                    });
                }


                $(document).on('click', '.btn-simpan-faktor', function() {
                    const indikatorId = $(this).data('indikator-id');
                    // console.log('Indikator ID:', indikatorId); // Pastikan indikatorId tidak undefined

                    const faktor = $(`.faktor-input[data-indikator-id='${indikatorId}']`).val();
                    const langkah = $(`.langkah-input[data-indikator-id='${indikatorId}']`).val();
                    const triwulan = $('#triwulan').val();
                    const btn = $(this);

                    if (!indikatorId) {
                        alert('Indikator ID tidak ditemukan.');
                        return;
                    }

                    btn.prop('disabled', true).text('Menyimpan...');

                    // console.log('ID:', indikatorId, 'Faktor:', faktor, 'Langkah:', langkah, 'Triwulan:',
                    //     triwulan);

                    $.ajax({
                        url: '/pelaporan/simpan-keterangan',
                        method: 'POST',
                        data: {
                            indikator_id: indikatorId,
                            faktor: faktor,
                            langkah: langkah,
                            triwulan: triwulan,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            showNotifModal('Berhasil disimpan!', 'Sukses');

                            btn.prop('disabled', false).text('Simpan');
                        },

                        error: function(err) {
                            console.log(err);
                            if (err.status === 404 && err.responseJSON?.message) {
                                showNotifModal(err.responseJSON.message ??
                                    'Terjadi kesalahan saat menyimpan data.', 'Peringatan');

                            } else {
                                showNotifModal('Terjadi kesalahan saat menyimpan data.');
                            }
                            btn.prop('disabled', false).text('Simpan');
                        }


                    });
                });

                // === LOGIKA PELAPORAN IKP (LEVEL 1 KEJAKSAAN AGUNG) ===
                let selectedIkpSatkerId = null;

                $('.bidang-ikp-item').on('click', function() {
                    $(".bidang-ikp-item").removeClass("active btn-success text-white").addClass("btn-outline-success text-black");
                    $(this).removeClass("btn-outline-success text-black").addClass("active btn-success text-white");

                    selectedIkpSatkerId = $(this).data('satker-id');
                    $("#ikp-controls-wrapper").show();
                    let triwulan = $('#triwulan-ikp').val();
                    loadIkpData(selectedIkpSatkerId, triwulan);
                });

                // Otomatis pilih bidang jika hanya ada 1 bidang (misal login sebagai JAMWAS)
                if ($('.bidang-ikp-item').length === 1) {
                    $('.bidang-ikp-item').first().trigger('click');
                }

                $('#triwulan-ikp').on('change', function() {
                    let triwulan = $(this).val();
                    if (selectedIkpSatkerId) {
                        loadIkpData(selectedIkpSatkerId, triwulan);
                    }
                });

                $(document).on('click', '#reloadIkpBtn', function() {
                    let triwulan = $('#triwulan-ikp').val();
                    if (selectedIkpSatkerId) {
                        loadIkpData(selectedIkpSatkerId, triwulan);
                    }
                });

                // Expand All / Collapse All untuk Pelaporan IKP Accordion
                $(document).on('click', '#btn-pelaporan-expand-all', function() {
                    $('#accordionPelaporanIkp .accordion-collapse').addClass('show');
                    $('#accordionPelaporanIkp .accordion-button').removeClass('collapsed').attr('aria-expanded', 'true');
                });

                $(document).on('click', '#btn-pelaporan-collapse-all', function() {
                    $('#accordionPelaporanIkp .accordion-collapse').removeClass('show');
                    $('#accordionPelaporanIkp .accordion-button').addClass('collapsed').attr('aria-expanded', 'false');
                });

                function loadIkpData(satkerId, triwulan) {
                    $('#ikp-subindikator-wrapper').html('<div class="text-center py-4"><div class="spinner-border text-success" role="status"></div><p class="mt-2">Memuat data IKP...</p></div>');

                    $.ajax({
                        url: `/pelaporan/ikp-data/${satkerId}`,
                        data: {
                            triwulan: triwulan
                        },
                        success: function(data) {
                            let html = '';

                            if (!data || data.length === 0) {
                                html = '<p class="text-muted">Tidak ada Sasaran Program & IKP ditemukan untuk bidang ini.</p>';
                                $('#ikp-subindikator-wrapper').html(html);
                                return;
                            }

                            html = '<div class="accordion" id="accordionPelaporanIkp">';

                            data.forEach((sp, idx) => {
                                html += `
                                <div class="accordion-item mb-3 border border-warning shadow-sm rounded overflow-hidden">
                                    <h2 class="accordion-header" id="heading-pelaporan-sp-${idx}">
                                        <button class="accordion-button bg-light text-dark fw-bold py-3"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse-pelaporan-sp-${idx}"
                                            aria-expanded="true"
                                            aria-controls="collapse-pelaporan-sp-${idx}">
                                            <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                                <div class="text-start">
                                                    <span class="badge bg-warning text-dark me-2">${sp.kode_sp}</span>
                                                    <span class="text-dark">${sp.nama_sp}</span>
                                                </div>
                                                <div>
                                                    <span class="badge bg-info text-dark">${sp.ikps.length} IKP</span>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse-pelaporan-sp-${idx}" class="accordion-collapse collapse show" aria-labelledby="heading-pelaporan-sp-${idx}">
                                        <div class="accordion-body bg-white p-3">`;

                                if (sp.ikps && sp.ikps.length > 0) {
                                    sp.ikps.forEach((ikp) => {
                                        html += `
                                        <div class="mb-4 p-3 border rounded shadow-sm bg-light">
                                            <strong class="text-primary fs-6">${ikp.kode_ikp}: ${ikp.nama_ikp}</strong>
                                            <table class="table table-bordered align-middle mt-3 bg-white">
                                                <thead class="text-center bg-warning">
                                                    <tr>
                                                        <th>Persentase Penyelesaian</th>
                                                        <th>Target PK Tahunan</th>
                                                        <th>Capaian Target PK Tahunan</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                    <tr>
                                                        <td class="fw-bold text-success">${ikp.persentase || 0}${['indeks', 'nilai', 'opini', 'level', 'rasio'].some(w => ikp.nama_ikp && ikp.nama_ikp.toLowerCase().includes(w)) ? '' : '%'}</td>
                                                        <td>${ikp.target_pk || 0}${['indeks', 'nilai', 'opini', 'level', 'rasio'].some(w => ikp.nama_ikp && ikp.nama_ikp.toLowerCase().includes(w)) ? '' : '%'}</td>
                                                        <td class="fw-bold text-primary">${ikp.capaian_pk || 0}${['indeks', 'nilai', 'opini', 'level', 'rasio'].some(w => ikp.nama_ikp && ikp.nama_ikp.toLowerCase().includes(w)) ? '' : '%'}</td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div class="mb-2">
                                                <label class="form-label mb-1"><strong>Faktor-Faktor:</strong></label>
                                                <textarea class="form-control ikp-faktor-input" rows="2"
                                                    data-ikp-id="${ikp.ikp_id}" placeholder="Faktor-faktor yang mempengaruhi pencapaian kinerja...">${ikp.faktor || ''}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label mb-1"><strong>Upaya Optimalisasi:</strong></label>
                                                <textarea class="form-control ikp-langkah-input" rows="2"
                                                    data-ikp-id="${ikp.ikp_id}" placeholder="Upaya atau rencana tindak lanjut optimalisasi kinerja...">${ikp.langkah || ''}</textarea>
                                            </div>

                                            <button class="btn btn-sm btn-primary btn-simpan-ikp"
                                                data-ikp-id="${ikp.ikp_id}">
                                                <i class="bi bi-save me-1"></i> Simpan
                                            </button>
                                        </div>
                                        `;
                                    });
                                } else {
                                    html += '<p class="text-muted ms-2 my-2">Belum ada IKP di bawah sasaran program ini.</p>';
                                }

                                html += `
                                        </div>
                                    </div>
                                </div>`;
                            });

                            html += '</div>';

                            $('#ikp-subindikator-wrapper').html(html);
                        },
                        error: function() {
                            $('#ikp-subindikator-wrapper').html(
                                '<p class="text-danger">Gagal memuat data IKP.</p>');
                        }
                    });
                }

                $(document).on('click', '.btn-simpan-ikp', function() {
                    const ikpId = $(this).data('ikp-id');
                    const faktor = $(`.ikp-faktor-input[data-ikp-id='${ikpId}']`).val();
                    const langkah = $(`.ikp-langkah-input[data-ikp-id='${ikpId}']`).val();
                    const triwulan = $('#triwulan-ikp').val();
                    const btn = $(this);

                    if (!selectedIkpSatkerId) {
                        alert('Pilih bidang terlebih dahulu.');
                        return;
                    }

                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                    $.ajax({
                        url: '/pelaporan/simpan-ikp-keterangan',
                        method: 'POST',
                        data: {
                            ikp_id: ikpId,
                            id_satker: selectedIkpSatkerId,
                            faktor: faktor,
                            langkah: langkah,
                            triwulan: triwulan,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            showNotifModal('Berhasil disimpan!', 'Sukses');
                            btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                        },
                        error: function(err) {
                            console.log(err);
                            showNotifModal(err.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.', 'Peringatan');
                            btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan');
                        }
                    });
                });

                function showNotifModal(pesan, judul = 'Peringatan') {
                    $('#notifModalLabel').text(judul);
                    $('#notifModalMessage').text(pesan);
                    const notifModal = new bootstrap.Modal(document.getElementById('notifModal'));
                    notifModal.show();
                }
            });
        </script>
    @endpush

<style>
.bidang-nama-text {
    line-height: 1.3;
    display: inline-block;
    word-break: break-word;
}
.bidang-ikp-item {
    white-space: normal;
    text-align: left;
    padding: 0.6rem 0.75rem;
}
#accordionPelaporanIkp .accordion-button:not(.collapsed) {
    background-color: #fff8e1 !important;
    color: #000 !important;
    box-shadow: none;
}
#accordionPelaporanIkp .accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,0,0,.125);
}
</style>

@endsection
