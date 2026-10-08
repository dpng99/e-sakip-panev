@extends('layouts.app')

@section('title', 'Pengukuran')

@section('content')
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
                                    Tahun Anggaran {{ session('tahun_ui', date('Y')) }}
                                </span>
                            </div>
                            <h2 class="fw-bold text-white mb-1 d-flex align-items-center gap-2">
                                <i class="bi bi-graph-up-arrow text-warning"></i> Pengukuran Kinerja
                            </h2>
                            <p class="text-white text-opacity-80 mb-0 small">
                                Pengukuran capaian indikator kinerja per bidang secara bulanan dan triwulanan.
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
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <strong>📌 Daftar Bidang</strong>
                                </div>
                                <div class="card-body">
                                    @php
                                        use App\Models\Bidang;
                                        $level = session('id_sakip_level');
                                        $satkernama = session('satkernama') ?? '';
                                        // $bidangs = [];
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
                                        <button class="btn btn-outline-warning text-black w-100 mb-2 bidang-item"
                                            data-rumpun="{{ $bidang->rumpun }}">
                                            {{ $namaBidang }}
                                        </button>
                                    @endforeach


                                </div>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header bg-warning text-black">📋 Indikator</div>
                                <div class="card-body" id="subindikator-wrapper">

                                    <form action="{{ route('pengukuran.store') }}" method="POST">
                                        @csrf
                                        <div id="indikator-section">
                                            @if (session('success'))
                                                <div class="alert alert-success">{{ session('success') }}</div>
                                            @endif
                                            <p>Pilih bidang untuk melihat indikator.</p>
                                        </div>
                                        <div class="text-end mt-4">
                                            <button type="submit" class="btn btn-success" id="btn-simpan"
                                                style="display: none;">Simpan</button>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="tahunTerpilih" value="{{ session('tahun_terpilih') }}">
<input type="hidden" id="tw4Khusus" value="{{ session('tw4_khusus', 0) }}">
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const bulanList = [
            'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
            'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'
        ];

        function formatRibuan(angka) {
    if (angka === null || angka === undefined || angka === '') return '';

    // pastikan string
    angka = angka.toString();

    // ganti titik desimal internasional jadi koma
    angka = angka.replace('.', ',');

    // pisahkan bagian ribuan dan desimal
    let parts = angka.split(',');
    let number = parts[0].replace(/\D/g, ''); // hanya angka di bagian ribuan
    let decimal = parts[1] || '';

    // format ribuan
    let formatted = number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    return decimal ? `${formatted},${decimal}` : formatted;
}



        
        // $.getJSON('/get-subindikator/' + rumpun, function(response) {
        //     response.forEach(function(indikator) {
        //         let labelPenghitungan = ['Ditangani', 'Diselesaikan'];

        //         if (indikator.indikator_penghitungan) {
        //             let labels = indikator.indikator_penghitungan.split(',');
        //             if (labels.length === 2) {
        //                 labelPenghitungan = labels.map(s => s.trim());
        //             }
        //         }

        //         // Sekarang labelPenghitungan[0] dan labelPenghitungan[1] adalah label yang bisa kamu tampilkan
        //         console.log("Label:", labelPenghitungan[0], labelPenghitungan[1]);

        //         // Gunakan label ini untuk menampilkan di header tabel atau placeholder input
        //         // Tapi name="ditangani" dan name="diselesaikan" tetap
        //     });
        // });

        $(document).on('input', '.angka-format', function() {
            let val = $(this).val();
            let parts = val.replace(/[^0-9,]/g, '').split(',');
            let number = parts[0];
            let decimal = parts[1] ?? '';
            let formatted = number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            $(this).val(val.includes(',') ? `${formatted},${decimal}` : formatted);
        });

        $(document).ready(function() {
            $('.bidang-item').on('click', function() {
                // Hilangkan class aktif dari semua tombol bidang
                $('.bidang-item').removeClass('active-bidang');

                // Tambahkan class aktif ke tombol yang diklik
                $(this).addClass('active-bidang');

                // Lanjutkan dengan AJAX...
            });
 
            $('.bidang-item').on('click', function() {
                var rumpun = $(this).data('rumpun');
                $.ajax({
                    url: '/get-subindikator/' + rumpun,
                    method: 'GET',
                    success: function(response) {

                        let indikatorSection = $('#indikator-section');
                        indikatorSection.empty();

                        if (response.length === 0) {
                            indikatorSection.html(
                                '<p>Tidak ada Indikator untuk bidang ini.</p>');
                            return;
                        }

                        response.forEach(indikator => {
                            if (!indikator.sub_indikator) return;

                            let subIndikators = indikator.sub_indikator.split(',').map(
                                s => s.trim());

                            // Ambil label dari indikator_penghitungan
                            let labelPenghitungan = ['Ditangani',
                                'Diselesaikan'
                            ]; // default
                            if (indikator.indikator_penghitungan) {
                                let labels = indikator.indikator_penghitungan.split(',')
                                    .map(s => s.trim());
                                // kalau hanya ada 1 label â†’ jadikan array 1 elemen
                                if (labels.length >= 1) {
                                    labelPenghitungan = labels;
                                }
                            }
 
                            subIndikators.forEach(sub => {
    let table = '';
    let indikator_penjelasan = indikator.indikator_penjelasan || '';

    if (labelPenghitungan.length === 1) {
        // ================= MODE TRIWULAN =================
        let triwulanList = ['TW1', 'TW2', 'TW3', 'TW4'];

        table = `
        <div class="table-responsive mb-4">
            <strong>${sub}</strong>
            <p>${indikator_penjelasan}</p>

            <input type="hidden" name="indikator_id[${sub}]" value="${indikator.id}">
            <input type="hidden" name="sub_indikator_list[]" value="${sub}">

            <table class="table table-bordered text-center mt-2">
                <thead>
                    <tr>
                        <th>Label</th>
                        ${triwulanList.map(tw => `<th>${tw}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${labelPenghitungan[0]}</td>
                        ${triwulanList.map(tw => `
                            <td>
                               <input type="text"
    class="form-control angka-format input-triwulan text-center mx-auto"
    data-tw="${tw}"
    name="${labelPenghitungan[0].toLowerCase()}[${sub}][${tw}]"
    placeholder="-">

                            </td>
                        `).join('')}
                    </tr>
                </tbody>
            </table>
        </div>
        `;
    } else {
        // ================= MODE BULANAN (TIDAK DIUBAH) =================
        let bulanList = [
            'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
            'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
        ];

        let sisaTahunLaluInput = `
        <div class="mb-2 d-flex align-items-center">
            <label class="me-2 mb-0" style="white-space: nowrap;">Sisa tahun lalu:</label>
            <input type="text"
               class="form-control angka-format text-center mx-auto"
                name="sisa_tahun_lalu[${sub}]"
                style="width:200px;">
        </div>
        `;

        table = `
        <div class="table-responsive mb-4">
            <strong>${sub}</strong>
            <p>${indikator_penjelasan}</p>

            <input type="hidden" name="indikator_id[${sub}]" value="${indikator.id}">
            <input type="hidden" name="sub_indikator_list[]" value="${sub}">

            ${sisaTahunLaluInput}

            <table class="table table-bordered text-center mt-2">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        ${bulanList.map(b => `<th>${b}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${labelPenghitungan.map(label => `
                        <tr>
                            <td>${label}</td>
                            ${bulanList.map(b => `
                                <td>
                                    <input type="text"
                                        style="width:120px"
                                        class="form-control angka-format"
                                        name="${label.toLowerCase()}[${sub}][${b}]">
                                </td>
                            `).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        `;
    }

    indikatorSection.append(table);
});
// ================= LOGIC READONLY TRIWULAN (FINAL) =================
function aturTriwulan() {
    let tahun  = parseInt($('#tahunTerpilih').val());
    let khusus = parseInt($('#tw4Khusus').val()); // 1 atau 0

    $('.input-triwulan').each(function () {
        let tw = $(this).data('tw');

        // default: AKTIF
        let readonly = false;

        if (tahun === 2025) {
            if (khusus === 1) {
                // hanya TW4 boleh
                readonly = (tw !== 'TW4');
            } else {
                // TW1–TW3 boleh
                readonly = (tw === 'TW4');
            }
        }
        // tahun 2026+ => semua aktif

        $(this)
            .prop('readonly', readonly)
            .toggleClass('bg-light', readonly);
    });
}

// WAJIB dipanggil setelah semua table selesai dirender
aturTriwulan();

// ================= LOGIC READONLY TRIWULAN (FINAL) =================
function aturTriwulan() {
    let tahun  = parseInt($('#tahunTerpilih').val());
    let khusus = parseInt($('#tw4Khusus').val()); // 1 atau 0

    $('.input-triwulan').each(function () {
        let tw = $(this).data('tw');

        // default: AKTIF
        let readonly = false;

        if (tahun === 2025) {
            if (khusus === 1) {
                // hanya TW4 boleh
                readonly = (tw !== 'TW4');
            } else {
                // TW1–TW3 boleh
                readonly = (tw === 'TW4');
            }
        }
        // tahun 2026+ => semua aktif

        $(this)
            .prop('readonly', readonly)
            .toggleClass('tw-readonly', readonly);
    });
}

// WAJIB dipanggil setelah semua table selesai dirender
aturTriwulan();


                         // Ambil data pengukuran dari DB
$.ajax({
    url: '/get-pengukuran/' + indikator.id,
    method: 'GET',
    success: function(pengukuranData) {
        const bulanIndex = [
            '', 'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
            'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'
        ];

        // mapping bulan -> triwulan
        const triwulanMap = {3: 'TW1', 6: 'TW2', 9: 'TW3', 12: 'TW4'};

        let labels = indikator.indikator_penghitungan
            ? indikator.indikator_penghitungan.split(',').map(s => s.trim().toLowerCase())
            : ['ditangani', 'diselesaikan']; // default

        pengukuranData.forEach(item => {
            // === MODE BULANAN ===
            if (labels.length > 1) {
                if (item.perhitungan) {
                    let parts = item.perhitungan.split(';');
                    labels.forEach((label, idx) => {
                        let val = parts[idx] ?? '';
                        let bulanNama = bulanIndex[item.bulan];
                        let selector = `input[name="${label}[${item.sub_indikator}][${bulanNama}]"]`;
                        if ($(selector).length) {
                            $(selector).val(val ? formatRibuan(val.toString()) : '-');
                        }
                    });
                }

                if (item.bulan === 1 && item.sisa_tahun_lalu !== null) {
                    let sisaInput = $(`input[name="sisa_tahun_lalu[${item.sub_indikator}]"]`);
                    if (sisaInput.length) {
                        sisaInput.val(formatRibuan(item.sisa_tahun_lalu.toString()));
                    }
                }
            }

            // === MODE TRIWULAN ===
            if (labels.length === 1) {
                let tw = triwulanMap[item.bulan];
                if (tw && item.capaian !== null) {
                    let selector = `input[name="${labels[0]}[${item.sub_indikator}][${tw}]"]`;
                    if ($(selector).length) {
                        $(selector).val(item.capaian ? formatRibuan(item.capaian.toString()) : '-');
                    }
                }
            }
        });
    }
});


                        });
                    },
                    error: function() {
                        $('#indikator-section').html('<p>Gagal memuat data indikator.</p>');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Sembunyikan tombol simpan saat halaman pertama kali dimuat
            $('#btn-simpan').hide();

            // Ketika tombol bidang diklik
            $('.bidang-item').on('click', function() {
                // Tampilkan tombol simpan
                $('#btn-simpan').fadeIn();

                // Tambahan: jika ingin hanya 1 tombol bidang yang aktif berwarna kuning
                $('.bidang-item').removeClass('active text-white bg-warning');
                $(this).addClass('active text-white bg-warning');

                // TODO: Tambahkan logika AJAX load sub indikator jika perlu
            });
        });
    </script>
    
@endpush
<style>
    .bidang-item.active-bidang {
        background-color: #ffc107;
        /* Bootstrap's warning (yellow) */
        color: black;
        border-color: #ffc107;
    }
    .tw-readonly {
    background-color: #d1d5db !important; /* abu-abu gelap */
    color: #374151;              /* teks lebih gelap */
    border-color: #9ca3af;       /* border jelas */
    cursor: not-allowed;
}

.tw-readonly::placeholder {
    color: #6b7280;
}

</style>
