@extends('layouts.app')

@section('title', 'Perencanaan')

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
                                <i class="bi bi-calendar2-range-fill text-warning"></i> Perencanaan Kinerja
                            </h2>
                            <p class="text-white text-opacity-80 mb-0 small">
                                Pengelolaan dokumen Renstra, IKU, Renja, RKAKL, DIPA, Rencana Aksi, dan Perjanjian Kinerja Satuan Kerja.
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

            <div class="card border-0 shadow-sm" style="width: 100%;">
                <div class="card-header bg-white border-bottom p-0 px-3">
                    <!-- Cek tab aktif dari session atau default ke renstra -->
                    @php
                        $activeTab = session('active_tab', 'renstra'); // Default ke renstra
                    @endphp
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'renstra' ? 'active' : '' }}"
                                id="renstra-tab" data-bs-toggle="tab" href="#renstra" role="tab" aria-controls="renstra"
                                aria-selected="{{ $activeTab == 'renstra' ? 'true' : 'false' }}">Renstra</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'iku' ? 'active' : '' }}" id="iku-tab"
                                data-bs-toggle="tab" href="#iku" role="tab" aria-controls="iku"
                                aria-selected="{{ $activeTab == 'iku' ? 'true' : 'false' }}">IKU (Penetapan Target
                                Kinerja)</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'renja' ? 'active' : '' }}" id="renja-tab"
                                data-bs-toggle="tab" href="#renja" role="tab" aria-controls="renja"
                                aria-selected="{{ $activeTab == 'renja' ? 'true' : 'false' }}">Renja</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'rkakl' ? 'active' : '' }}" id="rkakl-tab"
                                data-bs-toggle="tab" href="#rkakl" role="tab" aria-controls="rkakl"
                                aria-selected="{{ $activeTab == 'rkakl' ? 'true' : 'false' }}">RKAKL</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'dipa' ? 'active' : '' }}" id="dipa-tab"
                                data-bs-toggle="tab" href="#dipa" role="tab" aria-controls="dipa"
                                aria-selected="{{ $activeTab == 'dipa' ? 'true' : 'false' }}">DIPA</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'renaksi' ? 'active' : '' }}" id="renaksi-tab"
                                data-bs-toggle="tab" href="#renaksi" role="tab"
                                aria-controls="{{ $activeTab == 'renaksi' ? 'true' : 'false' }}"
                                aria-selected="false">Rencana Aksi</a>
                        </li>
                        <!--|| $levelSakip == 2 || $levelSakip == 3 -->
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab == 'perjanjian-kinerja' ? 'active' : '' }}"
                                id="perjanjian-kinerja-tab" data-bs-toggle="tab" href="#perjanjian-kinerja" role="tab"
                                aria-controls="{{ $activeTab == 'perjanjian-kinerja' ? 'true' : 'false' }}"
                                aria-selected="false">Perjanjian Kinerja</a>
                        </li>
                        {{-- @endif --}}
                        @if ($tahun != 2024)
                            @if ($levelSakip == 99)
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="cetak-pk-tab" data-bs-toggle="tab" href="#cetak-pk"
                                        role="tab" aria-controls="cetak-pk" aria-selected="false">Cetak PK</a>
                                </li>
                            @endif
                        @endif
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="myTabContent">
                        <div class="tab-pane fade {{ $activeTab == 'renstra' ? 'show active' : '' }}" id="renstra"
                            role="tabpanel" aria-labelledby="renstra-tab">
                            <div class="renstra-content">
                                @php
                                    if ($tahun == '2024') {
                                        $id_tahun = '2019 - 2024';
                                    } else {
                                        $id_tahun = '2025 - 2029';
                                    }
                                @endphp
                                <h3><strong>Rencana Strategis (Renstra) Tahun {{ $id_tahun }}</strong></h3>
                                <p class="card-title p-2" style="background-color: #f1e022; color: black;">Rencana Strategis
                                    (Renstra) merupakan dokumen perencanaan yang menetapkan tujuan, sasaran, strategi,
                                    kebijakan, program, dan kegiatan pembangunan dalam jangka waktu lima tahun.</p>

                                <!-- Form Upload File -->
                                <div>
                                    <div class="card shadow-sm">
                                        <div class="card-header text-white" style="background-color: #e6bf3e;">
                                            <h4 class="mb-0">Upload File Renstra</h4>
                                        </div>
                                        <div class="card-body">
                                            <!-- Form Upload File Renstra -->

                                            <form action="{{ route('upload.renstra') }}" method="POST"
                                                enctype="multipart/form-data" class="mb-4">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="renstra_file" class="form-label">Upload File PDF
                                                        Renstra (Max: 2MB)</label>
                                                    <input type="file" class="form-control" id="renstra_file"
                                                        name="renstra_file" accept=".pdf" required>
                                                </div>
                                                <button type="submit" class="btn btn-warning btn-block">Upload
                                                    File</button>
                                            </form>
                                            <!-- Alert for success -->
                                            @if (session('success-renstra'))
                                                <div class="alert alert-success" id="success-alert">
                                                    {{ session('success-renstra') }}
                                                </div>
                                            @endif

                                            <!-- Tabel Renstra -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-warning">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>File Renstra</th>
                                                            <th>Versi</th>
                                                            <th>Tanggal Upload</th>
                                                            {{-- <th>Aksi</th> --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($renstra as $index => $item)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <a href="{{ asset('uploads/repository/' . $item->id_satker . '/renstra_' . $tahun . '_' . $item->id_perubahan . '.pdf') }}"
                                                                        target="_blank"
                                                                        style="text-decoration: none; color: inherit;">
                                                                        {{ $item->id_periode == 'P1' ? 'Periode 2020 - 2024' : 'Periode 2025 - 2029' }}
                                                                    </a>
                                                                </td>

                                                                <td>{{ $item->id_perubahan }}</td>
                                                                <td>{{ $item->id_tglupload }}</td>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $activeTab == 'iku' ? 'show active' : '' }}" id="iku"
                            role="tabpanel" aria-labelledby="iku-tab">
                            <div class="iku-content">
                                <h3><strong>Indikator Kinerja Utama (IKU)</strong></h3>
                                <p class="card-title p-2" style="background-color: #f1e022; color: black;">Indikator
                                    Kinerja Utama (IKU) Kejaksaan adalah ukuran keberhasilan dalam mencapai tujuan dan
                                    sasaran strategis Kejaksaan, yang digunakan sebagai acuan untuk menyusun rencana
                                    kinerja, kerja, anggaran, dan evaluasi kinerja</p>

                                <!-- Form Upload File -->
                                <div>
                                    <div class="card shadow-sm">
                                        <div class="card-header text-white" style="background-color: #e6bf3e;">
                                            <h4 class="mb-0">Upload File IKU</h4>
                                        </div>
                                        <div class="card-body">
                                            <!-- Form Upload File IKU -->
                                            <form action="{{ route('upload.iku') }}" method="POST"
                                                enctype="multipart/form-data" class="mb-4">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="iku_file" class="form-label">Upload File PDF Iku (Max:
                                                        2MB)</label>
                                                    <input type="file" class="form-control" id="iku_file"
                                                        name="iku_file" accept=".pdf" required>
                                                </div>
                                                <button type="submit" class="btn btn-warning btn-block">Upload
                                                    File</button>
                                            </form>
                                            <!-- Alert for success -->
                                            @if (session('success-iku'))
                                                <div class="alert alert-success" id="success-alert">
                                                    {{ session('success-iku') }}
                                                </div>
                                            @endif

                                            <!-- Tabel iku -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-warning">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>File Iku</th>
                                                            <th>Versi</th>
                                                            <th>Tanggal Upload</th>
                                                            {{-- <th>Aksi</th> --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($iku as $index => $item)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <a href="{{ asset('uploads/repository/' . $item->id_satker . '/IKU_' . $tahun . '_' . $item->id_perubahan . '.pdf') }}"
                                                                        target="_blank"
                                                                        style="text-decoration: none; color: inherit;">
                                                                        {{ $item->id_periode }}
                                                                    </a>
                                                                </td>

                                                                <td>{{ $item->id_perubahan }}</td>
                                                                <td>{{ $item->id_tglupload }}</td>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $activeTab == 'renja' ? 'show active' : '' }}" id="renja"
                            role="tabpanel" aria-labelledby="renja-tab">
                            <div class="renja-content">
                                <h3><strong>Rencana Kerja Tahunan</strong></h3>
                                <p class="card-title p-2" style="background-color: #f1e022; color: black;">Rencana Kinerja
                                    Tahunan (RKT) merupakan penjabaran dari sasaran dan program yang telah
                                    ditetapkan dalam Iku, dan akan dilaksanakan oleh satuan organisasi/kerja melalui
                                    berbagai kegiatan tahunan. <br> Rencana Kinerja Tahunan (RKT) adalah dokumen perencanaan
                                    untuk periode 1 (satu) tahun
                                    sebagai penjabaran dari sasaran dan program yang telah ditetapkan dalam Rencana
                                    Startegis (Iku) mencangkup periode tahunan yang sifatnya sangat strategis karena
                                    menjembatani perencanaan strategis jangka menengah dengan perencanaan tahunan. Dengan
                                    demikian, RKT berperan memelihara konsistensi antara capaian tujuan perencanaan
                                    strategis jangka menengah yang tercantum dalam Iku dengan tujuan perencanaan tahunan
                                    pembangunan. Penyusunan rencana kinerja dilakukan seiring dengan agenda penyusunan dan
                                    kebijakan anggaran, serta merupakan komitmen bagi instansi untuk mencapainya dalam tahun
                                    tertentu.</p>

                                <!-- Form Upload File -->
                                <div>
                                    <div class="card shadow-sm">
                                        <div class="card-header text-white" style="background-color: #e6bf3e;">
                                            <h4 class="mb-0">Upload File Renja</h4>
                                        </div>
                                        <div class="card-body">
                                            <!-- Form Upload File Renja -->
                                            <form action="{{ route('upload.renja') }}" method="POST"
                                                enctype="multipart/form-data" class="mb-4">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="renja_file" class="form-label">Upload File PDF
                                                        Renja (Max: 2MB)</label>
                                                    <input type="file" class="form-control" id="renja_file"
                                                        name="renja_file" accept=".pdf" required>
                                                </div>
                                                <button type="submit" class="btn btn-warning btn-block">Upload
                                                    File</button>
                                            </form>
                                            <!-- Alert for success -->
                                            @if (session('success-renja'))
                                                <div class="alert alert-success" id="success-alert">
                                                    {{ session('success-renja') }}
                                                </div>
                                            @endif

                                            <!-- Tabel renja -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-warning">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>File Renja</th>
                                                            <th>Versi</th>
                                                            <th>Tanggal Upload</th>
                                                            {{-- <th>Aksi</th> --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($renja as $index => $item)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <a href="{{ asset('uploads/repository/' . $item->id_satker . '/renja_' . $tahun . '_' . $item->id_perubahan . '.pdf') }}"
                                                                        target="_blank"
                                                                        style="text-decoration: none; color: inherit;">
                                                                        {{ $item->id_periode }}
                                                                    </a>
                                                                </td>

                                                                <td>{{ $item->id_perubahan }}</td>
                                                                <td>{{ $item->id_tglupload }}</td>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $activeTab == 'rkakl' ? 'show active' : '' }}" id="rkakl"
                            role="tabpanel" aria-labelledby="rkakl-tab">
                            <div class="rkakl-content">
                                <h3><strong>Rencana Kerja Anggaran Kementerian atau Lembaga</strong></h3>
                                <p class="card-title p-2" style="background-color: #f1e022; color: black;">Data Kebutuhan
                                    Riil (Periode Awal tahun dengan rumus -1 TA) silahkan masukkan data
                                    kebutuhan RIIL satker anda.
                                    Rencana Kerja Anggaran (RKA) bertujuan untuk merencanakan penganggaran kebutuhan dana
                                    dari berbagai program dan kegiatan di masa yang akan datang. Dengan Penyusunan RKA dapat
                                    merencanakan penggunaan dana agar bisa seefisien mungkin. program-program yang
                                    direncanakan dan akan dilaksanakan menghasilkan output dan outcome yang bermanfaat bagi
                                    kepentingan publik </p>

                                <!-- Form Upload File -->
                                <div>
                                    <div class="card shadow-sm">
                                        <div class="card-header text-white" style="background-color: #e6bf3e;">
                                            <h4 class="mb-0">UPLOAD File RKAKL</h4>
                                        </div>
                                        <div class="card-body">
                                            <!-- Form Upload File Rkakl -->
                                            <form action="{{ route('upload.rkakl') }}" method="POST"
                                                enctype="multipart/form-data" class="mb-4">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="rkakl_file" class="form-label">Upload File PDF
                                                        RKAKL (Max: 2MB)</label>
                                                    <input type="file" class="form-control" id="rkakl_file"
                                                        name="rkakl_file" accept=".pdf" required>
                                                </div>
                                                <button type="submit" class="btn btn-warning btn-block">Upload
                                                    File</button>
                                            </form>
                                            <!-- Alert for success -->
                                            @if (session('success-rkakl'))
                                                <div class="alert alert-success" id="success-alert">
                                                    {{ session('success-rkakl') }}
                                                </div>
                                            @endif

                                            <!-- Tabel rkakl -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-warning">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>File RKAKL</th>
                                                            <th>Versi</th>
                                                            <th>Tanggal Upload</th>
                                                            {{-- <th>Aksi</th> --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($rkakl as $index => $item)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <a href="{{ asset('uploads/repository/' . $item->id_satker . '/rkakl_' . $tahun . '_' . $item->id_perubahan . '.pdf') }}"
                                                                        target="_blank"
                                                                        style="text-decoration: none; color: inherit;">
                                                                        {{ $item->id_periode }}
                                                                    </a>
                                                                </td>

                                                                <td>{{ $item->id_perubahan }}</td>
                                                                <td>{{ $item->id_tglupload }}</td>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ session('active_tab') == 'dipa' ? 'show active' : '' }}"
                            id="dipa" role="tabpanel" aria-labelledby="dipa-tab">
                            <div class="dipa-content">
                                <h3><strong>Daftar Isian Pelaksanaan Anggaran (DIPA)</strong></h3>
                                <p class="card-title p-2" style="background-color: #f1e022; color: black;">
                                    Daftar Isian Pelaksanaan Anggaran (DIPA) Kejaksaan menjadi dasar bagi Satuan Kerja
                                    (Satker) Kejaksaan untuk melaksanakan kegiatan yang telah direncanakan.
                                </p>

                                <!-- Alert Notifikasi -->
                                @if (session('success-dipa'))
                                    <div class="alert alert-success">{{ session('success-dipa') }}</div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                <!-- Form Upload File -->
                                <div class="card shadow-sm">
                                    <div class="card-header text-white" style="background-color: #e6bf3e;">
                                        <h4 class="mb-0">UPLOAD DIPA SATKER ANDA</h4>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('upload.dipa') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="dipa_file" class="form-label">Upload File PDF DIPA (Max:
                                                    2MB)</label>
                                                <input type="file" class="form-control" id="dipa_file"
                                                    name="dipa_file" accept=".pdf" required>
                                                @error('dipa_file')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="id_pagu" class="form-label">Total Pagu</label>
                                                <input type="text" class="form-control format-number" id="id_pagu"
                                                    name="id_pagu_formatted" required>
                                                <input type="hidden" id="id_pagu_hidden" name="id_pagu">
                                            </div>

                                            <div class="mb-3">
                                                <label for="id_gakyankum" class="form-label">Program Penegakan dan
                                                    Pelayanan Hukum</label>
                                                <input type="text" class="form-control format-number"
                                                    id="id_gakyankum" name="id_gakyankum_formatted" required>
                                                <input type="hidden" id="id_gakyankum_hidden" name="id_gakyankum">
                                            </div>

                                            <div class="mb-3">
                                                <label for="id_dukman" class="form-label">Program Dukungan
                                                    Manajemen</label>
                                                <input type="text" class="form-control format-number" id="id_dukman"
                                                    name="id_dukman_formatted" required>
                                                <input type="hidden" id="id_dukman_hidden" name="id_dukman">
                                            </div>

                                            <button type="submit" class="btn btn-warning btn-block">Upload
                                                File</button>
                                        </form>
                                    </div>

                                    <!-- Tabel DIPA -->
                                    <div class="card-body">
                                        <div class="table-responsive mt-4">
                                            <table class="table table-bordered table-striped">
                                                <thead class="table-warning">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>File DIPA</th>
                                                        <th>Total Pagu</th>
                                                        <th>Program Penegakan dan Pelayanan Hukum</th>
                                                        <th>Program Dukungan Manajemen</th>
                                                        <th>Versi</th>
                                                        <th>Tanggal Upload</th>
                                                        {{-- <th>Aksi</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($dipa as $index => $item)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>
                                                                <a href="{{ asset('uploads/repository/' . $item->id_satker . '/dipa_' . $tahun . '_' . $item->id_perubahan . '.pdf') }}"
                                                                    target="_blank"
                                                                    style="text-decoration: none; color: inherit;">
                                                                    {{ $item->id_periode }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                Rp.
                                                                @if (strpos($item->id_pagu, '.') === false)
                                                                    {{ number_format((float) $item->id_pagu, 0, ',', '.') }}
                                                                @else
                                                                    {{ $item->id_pagu }}
                                                                @endif
                                                            </td>

                                                            <td>
                                                                Rp.
                                                                @if (strpos($item->id_gakyankum, '.') === false)
                                                                    {{ number_format((float) $item->id_gakyankum, 0, ',', '.') }}
                                                                @else
                                                                    {{ $item->id_gakyankum }}
                                                                @endif
                                                            </td>

                                                            <td>
                                                                Rp.
                                                                @if (strpos($item->id_dukman, '.') === false)
                                                                    {{ number_format((float) $item->id_dukman, 0, ',', '.') }}
                                                                @else
                                                                    {{ $item->id_dukman }}
                                                                @endif
                                                            </td>

                                                            <td>{{ $item->id_perubahan }}</td>
                                                            <td>{{ $item->id_tglupload }}</td>
                                                            {{-- <td>
                                @if (!empty($item->id_filename) && file_exists(public_path('uploads/repository/dipa/' . $item->id_filename)))
                                    <a href="{{ asset('uploads/repository/dipa/' . $item->id_filename) }}" class="btn btn-primary btn-sm" target="_blank">Download</a>
                                @else
                                    <span class="text-danger">Tidak Ada File</span>
                                @endif
                            </td> --}}
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center">Belum ada data DIPA
                                                                yang
                                                                diunggah.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- JavaScript untuk memformat input angka -->
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                let inputs = document.querySelectorAll('.format-number');

                                inputs.forEach(function(input) {
                                    input.addEventListener('input', function() {
                                        let value = this.value.replace(/\D/g, ''); // Hapus semua karakter non-angka
                                        let formattedValue = new Intl.NumberFormat('id-ID').format(value);

                                        this.value = formattedValue; // Tampilkan format dengan titik
                                        document.getElementById(this.id + "_hidden").value =
                                            value; // Simpan nilai asli tanpa titik
                                    });
                                });
                            });
                        </script>


                        <div class="tab-pane fade {{ $activeTab == 'renaksi' ? 'show active' : '' }}" id="renaksi"
                            role="tabpanel" aria-labelledby="renaksi-tab">
                            <div class="renaksi-content">
                                <h3><strong>Rencana Kerja Anggaran Kementerian atau Lembaga</strong></h3>
                                <p class="card-title p-2" style="background-color: #f1e022; color: black;">Data Kebutuhan
                                    Riil (Periode Awal tahun dengan rumus -1 TA) silahkan masukkan data
                                    kebutuhan RIIL satker anda.
                                    Rencana Kerja Anggaran (RKA) bertujuan untuk merencanakan penganggaran kebutuhan dana
                                    dari berbagai program dan kegiatan di masa yang akan datang. Dengan Penyusunan RKA dapat
                                    merencanakan penggunaan dana agar bisa seefisien mungkin. program-program yang
                                    direncanakan dan akan dilaksanakan menghasilkan output dan outcome yang bermanfaat bagi
                                    kepentingan publik </p>

                                <!-- Form Upload File -->
                                <div>
                                    <div class="card shadow-sm">
                                        <div class="card-header text-white" style="background-color: #e6bf3e;">
                                            <h4 class="mb-0">UPLOAD File RENCANA AKSI</h4>
                                        </div>
                                        <div class="card-body">
                                            <!-- Form Upload File renaksi -->
                                            <form action="{{ route('upload.renaksi') }}" method="POST"
                                                enctype="multipart/form-data" class="mb-4">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="renaksi_file" class="form-label">Upload File PDF
                                                        Rencana Aksi (Max: 2MB)</label>
                                                    <input type="file" class="form-control" id="renaksi_file"
                                                        name="renaksi_file" accept=".pdf" required>
                                                </div>
                                                <button type="submit" class="btn btn-warning btn-block">Upload
                                                    File</button>
                                            </form>
                                            <!-- Alert for success -->
                                            @if (session('success-renaksi'))
                                                <div class="alert alert-success" id="success-alert">
                                                    {{ session('success-renaksi') }}
                                                </div>
                                            @endif

                                            <!-- Tabel renaksi -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-warning">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>File Rencana Aksi</th>
                                                            <th>Versi</th>
                                                            <th>Tanggal Upload</th>
                                                            {{-- <th>Aksi</th> --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($renaksi as $index => $item)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <a href="{{ asset('uploads/repository/' . $item->id_satker . '/renaksi_' . $tahun . '_' . $item->id_perubahan . '.pdf') }}"
                                                                        target="_blank"
                                                                        style="text-decoration: none; color: inherit;">
                                                                        Renaksi Tahun {{ $item->id_periode }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $item->id_perubahan }}</td>
                                                                <td>{{ $item->id_tglupload }}</td>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container mt-4"></div>

                        <div class="tab-pane  fade {{ $activeTab == 'perjanjian-kinerja' ? 'show active' : '' }}"
                            id="perjanjian-kinerja" role="tabpanel" aria-labelledby="perjanjian-kinerja-tab">
                            <!-- Card untuk Bidang Kajari -->
                            <!-- Alert for success -->
                            @if (session('success-pk'))
                                <div class="alert alert-success" id="success-alert">
                                    {{ session('success-pk') }}
                                </div>
                            @endif
                            <h3><strong>Perjanjian Kinerja</strong></h3>
                            <p class="card-title p-2" style="background-color: #f1e022; color: black;">Pengisian Target
                                Perjanjian Kinerja</p>
                            <div class="card shadow-sm">
                                <div class="card-header text-white" style="background-color: #e6bf3e;">
                                    <h4 class="mb-0">UPLOAD File Perjanjian Kinerja</h4>
                                </div>
                                <div class="card-body">
                                    <!-- Form Upload File PK -->
                                    <form action="{{ route('upload.pk') }}" method="POST" enctype="multipart/form-data"
                                        class="mb-4">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="pk_file" class="form-label">Upload File PDF Perjanjian Kinerja
                                                (Max: 5MB)</label>
                                            <input type="file" class="form-control" id="pk_file" name="pk_file"
                                                accept=".pdf" required>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-block">Upload File</button>
                                    </form>

                                    <!-- Alert for success -->
                                    @if (session('success-pk-file'))
                                        <div class="alert alert-success" id="success-alert">
                                            {{ session('success-pk-file') }}
                                        </div>
                                    @endif

                                    <!-- Tabel Perjanjian Kinerja -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-warning">
                                                <tr>
                                                    <th>No</th>
                                                    <th>File Perjanjian Kinerja</th>
                                                    <th>Versi</th>
                                                    {{-- <th>Nama File</th> --}}
                                                    <th>Tanggal Upload</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pk as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <a href="{{ asset('uploads/repository/' . $item->id_satker . '/' . $item->id_filename) }}"
                                                                target="_blank"
                                                                style="text-decoration: none; color: inherit;">
                                                                PK Tahun {{ $item->id_periode }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $item->id_perubahan }}</td>
                                                        {{-- <td>{{ $item->id_filename }}</td> --}}
                                                        <td>{{ $item->id_tglupload }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow-sm">
                                <div class="card-header text-white" style="background-color: #e6bf3e;">
                                    <h4 class="mb-0">Input Target Perjanjian Kinerja</h4>
                                </div>
                                <div class="card-body">
                                    @if ($levelSakip == 1 || in_array(session('id_satker'), ['admin', '999999']) || $levelSakip == 99)
                                        {{-- ===== MODE LEVEL 1 (KEJAKSAAN AGUNG): INPUT TARGET IKP ===== --}}
                                        <div class="row">
                                            <!-- Sidebar Bidang Pengampu -->
                                            <div class="col-md-3">
                                                <div class="card shadow-sm border-0 mb-3">
                                                    <div class="card-header bg-warning text-dark font-weight-bold">
                                                        <strong>📌 Bidang Pengampu</strong>
                                                    </div>
                                                    <div class="card-body p-2" id="sidebar-target-bidang-container">
                                                        @php
                                                            $satkerBidangs = \App\Http\Controllers\PengukuranIkpController::getFilteredBidangs();
                                                        @endphp
                                                        @foreach ($satkerBidangs as $sb)
                                                            <button type="button"
                                                                class="btn btn-outline-warning text-dark w-100 mb-2 text-start btn-satker-target-ikp"
                                                                data-satker-id="{{ $sb['id'] }}"
                                                                data-satker-nama="{{ $sb['nama'] }}">
                                                                <i class="bi bi-folder2-open me-2 text-warning"></i>
                                                                <span class="bidang-nama-text">{{ $sb['nama'] }}</span>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Content Input Target IKP -->
                                            <div class="col-md-9">
                                                <div class="card shadow-sm border-0">
                                                    <div class="card-header bg-warning text-dark d-flex flex-wrap justify-content-between align-items-center gap-2">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="fw-bold" id="current-target-bidang-title">📋 Target Perjanjian Kinerja IKP</span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2" id="target-accordion-action-buttons" style="display:none !important;">
                                                            <button type="button" class="btn btn-sm btn-outline-dark" id="btn-target-expand-all" title="Buka Semua Sasaran Program">
                                                                <i class="bi bi-arrows-expand me-1"></i> Buka Semua
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-dark" id="btn-target-collapse-all" title="Tutup Semua Sasaran Program">
                                                                <i class="bi bi-arrows-collapse me-1"></i> Tutup Semua
                                                            </button>
                                                            <button type="button" class="btn btn-dark btn-sm" id="btn-save-target-top">
                                                                <i class="bi bi-save me-1"></i> Simpan Target
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body" id="target-ikp-container">
                                                        <div class="text-center py-5 text-muted" id="placeholder-target-message">
                                                            <i class="bi bi-arrow-left-circle display-4"></i>
                                                            <p class="mt-3 fs-5">Silakan pilih bidang di sebelah kiri untuk menginput target IKP.</p>
                                                        </div>

                                                        <div id="target-ikp-alert-container"></div>

                                                        <form id="form-target-ikp" style="display: none;">
                                                            @csrf
                                                            <input type="hidden" name="id_satker_bidang" id="input-target-satker-id" value="">
                                                            
                                                            <div id="target-ikp-tables-wrapper"></div>

                                                            <div class="text-end mt-4">
                                                                <button type="submit" class="btn btn-warning btn-lg text-dark fw-bold px-4" id="btn-save-target-bottom">
                                                                    <i class="bi bi-check2-circle me-1"></i> Simpan Target Perjanjian Kinerja
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- ===== MODE LEVEL SATKER (EXISTING) ===== --}}
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

                                        //kurang rumah sakit dan atase where $level=5

                                    @endphp

                                    @foreach ($bidangs as $index => $bidang)
                                        @php
                                            $khusus = session('tw4_khusus', false) ? 1 : 0;
                                            $indikators = \App\Models\Indikator::where('link', $bidang->rumpun)
                                                ->where(function ($query) use ($tahun) {
                                                    $query->where('tahun', 'LIKE', "%$tahun%"); // cocokkan sebagian tahun
                                                })
                                                ->where('khusus', $khusus)
                                                ->where(function ($query) use ($level) {
                                                    if ($level == 1) {
                                                        $query->whereIn('lingkup', [0, 1]);
                                                    } elseif ($level == 2) {
                                                        $query->whereIn('lingkup', [0, 2, 5, 7]);
                                                    } elseif ($level == 3) {
                                                        $query->whereIn('lingkup', [0, 3, 5, 6, 7]);
                                                    } elseif ($level == 4) {
                                                        $query->whereIn('lingkup', [0, 4, 6, 7]);
                                                    }
                                                })
                                                ->get();
                                        @endphp


                                        @if ($tahun != 2024)
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
                                            <div class="card mb-2">
                                                <div class="card-header d-flex justify-content-between align-items-center"
                                                    style="background-color: #e6bf3e; color: white;">
                                                    {{ $namaBidang }}
                                                    <a data-bs-toggle="collapse"
                                                        href="#collapseBidang{{ $index }}" role="button"
                                                        aria-expanded="false"
                                                        aria-controls="collapseBidang{{ $index }}"
                                                        class="collapse-toggle d-flex align-items-center">
                                                        <i class="bi bi-chevron-down text-white rotate-icon"></i>
                                                    </a>
                                                </div>

                                                <div class="collapse" id="collapseBidang{{ $index }}">
                                                    <div class="card-body">
                                                        @if ($indikators->isNotEmpty())
                                                            <div class="row">
                                                                <form method="POST" action="{{ route('target.store') }}">
    @csrf

    <div class="row">
        @foreach ($indikators as $key => $indikator)
            <div class="col-md-6">
                <div class="card mb-2">
                    <div class="card-body">

                        <h5 class="text-center fw-bold">
                            {{ $indikator->indikator_nama }}
                        </h5>

                        {{-- kirim banyak indikator --}}
                        <input type="hidden" name="indikator_id[]" value="{{ $indikator->id }}">

                        <div class="mb-2">
                            <label class="form-label">Target Pertahun (%)</label>
                            <input type="number"
                                class="form-control"
                                name="target_tahun[{{ $indikator->id }}]"
                                value="{{ $target[$indikator->id]->target_tahun ?? '' }}">
                        </div>

                    </div>
                </div>
            </div>

            @if (($key + 1) % 2 == 0 && !$loop->last)
                </div><div class="row">
            @endif
        @endforeach
    </div>

    {{-- 🔥 tombol simpan SEKALI --}}
    <button type="submit" class="btn btn-success w-100 mt-3">
        Simpan Semua Target
    </button>
</form>
                                </div>
                            @else
                                <p><i>Tidak ada indikator terkait</i></p>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Ensure the container and card take full width */
        .container {
            max-width: 100%;
        }

        /* Full-width tabs with 100% width card */
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        /* Styling for the tabs */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .nav-tabs .nav-item {
            flex: 1 1 auto;
            text-align: center;
        }

        .nav-tabs .nav-link {
            width: 100%;
            white-space: nowrap;
            padding: 0.6rem 1rem;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
            transition: all 0.2s ease-in-out;
        }

        .nav-tabs .nav-link:hover {
            color: #0d6efd;
            background-color: #e9ecef;
            border-color: #ced4da;
        }

        /* Active tab styling */
        .nav-tabs .nav-link.active {
            color: #fff !important;
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.25);
        }

        /* Styling for tab content */
        .tab-content {
            border-top: 1px solid #ddd;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #fff;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .rotate-icon {
            transition: transform 0.3s ease;
        }

        .rotate-icon.rotate {
            transform: rotate(180deg);
        }

        .btn-satker-target-ikp.active {
            background-color: #ffc107 !important;
            color: #212529 !important;
            font-weight: 700 !important;
            border-color: #ffc107 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Menghilangkan alert setelah 5 detik
        setTimeout(function() {
            let successAlert = document.getElementById('success-alert');
            if (successAlert) {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 500); // Hapus elemen setelah transisi selesai
            }
        }, 5000); // 5 detik

        document.addEventListener('DOMContentLoaded', function() {
            // Event ketika collapse dibuka
            document.querySelectorAll('.collapse').forEach(function(collapse) {
                collapse.addEventListener('show.bs.collapse', function() {
                    const icon = this.previousElementSibling?.querySelector('.rotate-icon');
                    if (icon) icon.classList.add('rotate');
                });

                // Event ketika collapse ditutup
                collapse.addEventListener('hide.bs.collapse', function() {
                    const icon = this.previousElementSibling?.querySelector('.rotate-icon');
                    if (icon) icon.classList.remove('rotate');
                });
            });
        });

        // === LOGIKA TARGET PERJANJIAN KINERJA IKP (LEVEL 1 KEJAKSAAN AGUNG) ===
        $(document).ready(function() {
            let currentTargetSatkerId = null;

            $('.btn-satker-target-ikp').on('click', function() {
                $('.btn-satker-target-ikp').removeClass('active bg-warning text-dark font-weight-bold shadow-sm');
                $(this).addClass('active bg-warning text-dark font-weight-bold shadow-sm');

                currentTargetSatkerId = $(this).data('satker-id');
                const satkerNama = $(this).data('satker-nama');

                $('#current-target-bidang-title').html(`📋 Target Perjanjian Kinerja IKP: <strong>${satkerNama}</strong>`);
                $('#input-target-satker-id').val(currentTargetSatkerId);
                loadTargetIkpData(currentTargetSatkerId);
            });

            // Otomatis pilih bidang jika hanya ada 1 bidang (misal login sebagai JAMWAS)
            if ($('.btn-satker-target-ikp').length === 1) {
                $('.btn-satker-target-ikp').first().trigger('click');
            }

            // Juga trigger jika berpindah ke tab Perjanjian Kinerja
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                if ($(e.target).attr('href') === '#perjanjian-kinerja') {
                    if ($('.btn-satker-target-ikp').length === 1 && !$('#input-target-satker-id').val()) {
                        $('.btn-satker-target-ikp').first().trigger('click');
                    }
                }
            });

            function loadTargetIkpData(satkerId) {
                $('#placeholder-target-message').hide();
                $('#form-target-ikp').hide();
                $('#target-accordion-action-buttons').attr('style', 'display: none !important');
                $('#target-ikp-alert-container').empty();
                $('#target-ikp-tables-wrapper').html('<div class="text-center py-4"><div class="spinner-border text-warning" role="status"></div><p class="mt-2 text-muted">Memuat data Sasaran Program & IKP...</p></div>');
                $('#form-target-ikp').show();

                $.ajax({
                    url: `/perencanaan/target-ikp/data/${satkerId}`,
                    method: 'GET',
                    success: function(data) {
                        if (!data || data.length === 0) {
                            $('#target-ikp-tables-wrapper').html(`
                                <div class="alert alert-info text-center my-4">
                                    <i class="bi bi-info-circle me-1"></i> Belum ada data Sasaran Program & IKP yang terdaftar untuk bidang ini.
                                </div>
                            `);
                            $('#target-accordion-action-buttons').attr('style', 'display: none !important');
                            $('#btn-save-target-bottom').hide();
                            return;
                        }

                        let html = '<div class="accordion" id="accordionTargetIkp">';

                        data.forEach(function(sp, idx) {
                            html += `
                            <div class="accordion-item mb-3 border border-warning shadow-sm rounded overflow-hidden">
                                <h2 class="accordion-header" id="heading-target-sp-${idx}">
                                    <button class="accordion-button bg-light text-dark fw-bold py-3"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse-target-sp-${idx}"
                                        aria-expanded="true"
                                        aria-controls="collapse-target-sp-${idx}">
                                        <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                            <div class="text-start">
                                                <span class="badge bg-warning text-dark me-2">${sp.kode_sp}</span>
                                                <span class="text-dark">${sp.nama_sp}</span>
                                            </div>
                                            <div>
                                                <span class="badge bg-secondary text-white">${sp.ikps.length} IKP</span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-target-sp-${idx}" class="accordion-collapse collapse show" aria-labelledby="heading-target-sp-${idx}">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover align-middle mb-0 text-center">
                                                <thead class="table-warning text-dark align-middle">
                                                    <tr>
                                                        <th style="min-width: 280px;" class="text-start">Indikator Kinerja Program (IKP)</th>
                                                        <th style="width: 130px;">Target Thn</th>
                                                        <th style="width: 110px;">Target TW 1</th>
                                                        <th style="width: 110px;">Target TW 2</th>
                                                        <th style="width: 110px;">Target TW 3</th>
                                                        <th style="width: 110px;">Target TW 4</th>
                                                        <th style="width: 50px;" title="Salin Target Tahunan ke TW 1-4">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>`;

                            sp.ikps.forEach(function(ikp) {
                                let sifatBadge = '';
                                if (ikp.sifat_node) {
                                    let sifatClass = 'bg-secondary';
                                    let sifatText = ikp.sifat_node.toUpperCase();
                                    if (ikp.sifat_node.toLowerCase() === 'lead') sifatClass = 'bg-info text-dark';
                                    else if (ikp.sifat_node.toLowerCase() === 'lag') sifatClass = 'bg-primary';
                                    else if (ikp.sifat_node.toLowerCase() === 'crosscutting') sifatClass = 'bg-success';
                                    sifatBadge = `<span class="badge ${sifatClass} ms-1" style="font-size: 0.7rem;">${sifatText}</span>`;
                                }

                                const valTahun = (ikp.target_tahun !== null && ikp.target_tahun !== undefined) ? ikp.target_tahun : '';
                                const valTw1 = (ikp.target_tw1 !== null && ikp.target_tw1 !== undefined) ? ikp.target_tw1 : valTahun;
                                const valTw2 = (ikp.target_tw2 !== null && ikp.target_tw2 !== undefined) ? ikp.target_tw2 : valTahun;
                                const valTw3 = (ikp.target_tw3 !== null && ikp.target_tw3 !== undefined) ? ikp.target_tw3 : valTahun;
                                const valTw4 = (ikp.target_tw4 !== null && ikp.target_tw4 !== undefined) ? ikp.target_tw4 : valTahun;

                                html += `
                                    <tr>
                                        <td class="text-start">
                                            <div class="fw-bold text-primary">${ikp.kode_ikp} ${sifatBadge}</div>
                                            <div class="small text-dark fw-medium mt-1">${ikp.nama_ikp}</div>
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control form-control-sm text-center fw-bold input-target-tahun"
                                                name="targets[${ikp.ikp_id}][target_tahun]"
                                                value="${valTahun}"
                                                data-ikp="${ikp.ikp_id}"
                                                placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control form-control-sm text-center input-target-tw input-tw1-${ikp.ikp_id}"
                                                name="targets[${ikp.ikp_id}][target_tw1]"
                                                value="${valTw1}"
                                                placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control form-control-sm text-center input-target-tw input-tw2-${ikp.ikp_id}"
                                                name="targets[${ikp.ikp_id}][target_tw2]"
                                                value="${valTw2}"
                                                placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control form-control-sm text-center input-target-tw input-tw3-${ikp.ikp_id}"
                                                name="targets[${ikp.ikp_id}][target_tw3]"
                                                value="${valTw3}"
                                                placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control form-control-sm text-center input-target-tw input-tw4-${ikp.ikp_id}"
                                                name="targets[${ikp.ikp_id}][target_tw4]"
                                                value="${valTw4}"
                                                placeholder="0">
                                        </td>
                                        <td>
                                            <button type="button"
                                                class="btn btn-outline-warning btn-sm btn-copy-tw-row"
                                                data-ikp="${ikp.ikp_id}"
                                                title="Salin Target Tahunan ke TW 1-4">
                                                <i class="bi bi-arrow-right-square text-dark"></i>
                                            </button>
                                        </td>
                                    </tr>`;
                            });

                            html += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        });

                        html += '</div>';

                        $('#target-ikp-tables-wrapper').html(html);
                        $('#target-accordion-action-buttons').removeAttr('style');
                        $('#btn-save-target-bottom').show();
                    },
                    error: function() {
                        $('#target-ikp-tables-wrapper').html('<div class="alert alert-danger">Gagal memuat data Target IKP. Silakan coba lagi.</div>');
                    }
                });
            }

            // Quick Copy to TW 1-4 for a single row
            $(document).on('click', '.btn-copy-tw-row', function() {
                const ikpId = $(this).data('ikp');
                const valTahun = $(`input[name="targets[${ikpId}][target_tahun]"]`).val();
                if (valTahun !== '') {
                    $(`.input-tw1-${ikpId}`).val(valTahun);
                    $(`.input-tw2-${ikpId}`).val(valTahun);
                    $(`.input-tw3-${ikpId}`).val(valTahun);
                    $(`.input-tw4-${ikpId}`).val(valTahun);
                }
            });

            // Live auto-fill: when user types Target Tahunan, if TW inputs are empty, auto-fill
            $(document).on('input', '.input-target-tahun', function() {
                const ikpId = $(this).data('ikp');
                const val = $(this).val();
                const tw1 = $(`.input-tw1-${ikpId}`);
                const tw2 = $(`.input-tw2-${ikpId}`);
                const tw3 = $(`.input-tw3-${ikpId}`);
                const tw4 = $(`.input-tw4-${ikpId}`);

                if (tw1.val() === '' || tw1.val() === null) tw1.val(val);
                if (tw2.val() === '' || tw2.val() === null) tw2.val(val);
                if (tw3.val() === '' || tw3.val() === null) tw3.val(val);
                if (tw4.val() === '' || tw4.val() === null) tw4.val(val);
            });

            // Expand All / Collapse All buttons
            $(document).on('click', '#btn-target-expand-all', function() {
                $('#accordionTargetIkp .accordion-collapse').addClass('show');
                $('#accordionTargetIkp .accordion-button').removeClass('collapsed').attr('aria-expanded', 'true');
            });

            $(document).on('click', '#btn-target-collapse-all', function() {
                $('#accordionTargetIkp .accordion-collapse').removeClass('show');
                $('#accordionTargetIkp .accordion-button').addClass('collapsed').attr('aria-expanded', 'false');
            });

            // Top save button triggers form submit
            $('#btn-save-target-top').on('click', function() {
                $('#form-target-ikp').submit();
            });

            // Form Submit via AJAX
            $('#form-target-ikp').on('submit', function(e) {
                e.preventDefault();
                const btnSaveBottom = $('#btn-save-target-bottom');
                const btnSaveTop = $('#btn-save-target-top');

                btnSaveBottom.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...');
                btnSaveTop.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...');

                $.ajax({
                    url: '{{ route("perencanaan.targetIkp.store") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        btnSaveBottom.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Simpan Target Perjanjian Kinerja');
                        btnSaveTop.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Target');

                        $('#target-ikp-alert-container').html(`
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i> ${res.message || 'Target Perjanjian Kinerja IKP berhasil disimpan!'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);

                        // Scroll smoothly to alert
                        $('html, body').animate({
                            scrollTop: $("#target-ikp-alert-container").offset().top - 100
                        }, 300);

                        setTimeout(function() {
                            $('#target-ikp-alert-container .alert').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 5000);
                    },
                    error: function(xhr) {
                        btnSaveBottom.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Simpan Target Perjanjian Kinerja');
                        btnSaveTop.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Target');

                        let errMsg = 'Terjadi kesalahan saat menyimpan target IKP.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }

                        $('#target-ikp-alert-container').html(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> ${errMsg}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                    }
                });
            });
        });
    </script>
@endpush
