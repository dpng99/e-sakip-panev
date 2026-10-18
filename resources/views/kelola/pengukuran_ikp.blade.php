@extends('layouts.app')

@section('title', 'Pengukuran IKP - Kejaksaan Agung')

@section('content')
    <div class="content" id="content">
        <div class="container-fluid">
            <div class="card" style="width: 100%;">
                <div class="card border-light shadow-sm" style="background-color: #e6bf3e;">
                    <div class="py-2 text-center">
                        <h2 class="mb-0"><b>Pengukuran Kinerja Sasaran Program (SP) & IKP</b></h2>
                        <small class="text-dark">Tingkat Kejaksaan Agung (Pusat) &mdash; Tahun {{ $tahun }}</small>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Sidebar Bidang -->
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-warning text-dark font-weight-bold">
                                    <strong>📌 Bidang Pengampu</strong>
                                </div>
                                <div class="card-body p-2" id="sidebar-bidang-container">
                                    @foreach ($satkerBidangs as $sb)
                                        <button type="button"
                                            class="btn btn-outline-warning text-dark w-100 mb-2 text-start btn-satker-ikp"
                                            data-satker-id="{{ $sb['id'] }}"
                                            data-satker-nama="{{ $sb['nama'] }}">
                                            <i class="bi bi-folder2-open me-2 text-warning"></i>
                                            <span class="bidang-nama-text">{{ $sb['nama'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Content Pengukuran IKP -->
                        <div class="col-md-9">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-warning text-dark d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold" id="current-bidang-title">📋 Pengukuran Kinerja IKP</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2" id="accordion-action-buttons" style="display:none !important;">
                                        <button type="button" class="btn btn-sm btn-outline-dark" id="btn-expand-all" title="Buka Semua Sasaran Program">
                                            <i class="bi bi-arrows-expand me-1"></i> Buka Semua
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-dark" id="btn-collapse-all" title="Tutup Semua Sasaran Program">
                                            <i class="bi bi-arrows-collapse me-1"></i> Tutup Semua
                                        </button>
                                        <button type="button" class="btn btn-dark btn-sm" id="btn-save-top">
                                            <i class="bi bi-save me-1"></i> Simpan Pengukuran
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" id="pengukuran-ikp-container">
                                    <div class="text-center py-5 text-muted" id="placeholder-message">
                                        <i class="bi bi-arrow-left-circle display-4"></i>
                                        <p class="mt-3 fs-5">Silakan pilih bidang di sebelah kiri untuk menginput capaian IKP.</p>
                                    </div>

                                    <form id="form-pengukuran-ikp" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="id_satker_bidang" id="input-satker-id" value="">
                                        <div id="ikp-tables-wrapper"></div>

                                        <div class="text-end mt-4">
                                            <button type="submit" class="btn btn-warning btn-lg text-dark fw-bold px-4" id="btn-save-bottom">
                                                <i class="bi bi-check2-circle me-1"></i> Simpan Pengukuran
                                            </button>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentSatkerId = null;

    $('.btn-satker-ikp').on('click', function() {
        $('.btn-satker-ikp').removeClass('active bg-warning text-dark font-weight-bold shadow-sm');
        $(this).addClass('active bg-warning text-dark font-weight-bold shadow-sm');

        currentSatkerId = $(this).data('satker-id');
        const satkerNama = $(this).data('satker-nama');

        $('#current-bidang-title').html(`📋 Pengukuran IKP: <strong>${satkerNama}</strong>`);
        $('#input-satker-id').val(currentSatkerId);
        loadIkpPengukuranData(currentSatkerId);
    });

    // Otomatis pilih bidang pertama jika hanya ada 1 bidang (misal login sebagai JAMWAS)
    if ($('.btn-satker-ikp').length === 1) {
        $('.btn-satker-ikp').first().trigger('click');
    }

    function loadIkpPengukuranData(satkerId) {
        $('#placeholder-message').hide();
        $('#form-pengukuran-ikp').hide();
        $('#accordion-action-buttons').attr('style', 'display: none !important');
        $('#ikp-tables-wrapper').html('<div class="text-center py-4"><div class="spinner-border text-warning" role="status"></div><p class="mt-2">Memuat data IKP...</p></div>');
        $('#form-pengukuran-ikp').show();

        $.ajax({
            url: `{{ url('/pengukuran-ikp/data') }}/${encodeURIComponent(satkerId)}`,
            method: 'GET',
            success: function(data) {
                if (!data || data.length === 0) {
                    $('#ikp-tables-wrapper').html(`
                        <div class="alert alert-info text-center my-4">
                            <i class="bi bi-info-circle me-1"></i> Belum ada data Sasaran Program & IKP yang terdaftar untuk bidang ini.
                        </div>
                    `);
                    $('#accordion-action-buttons').attr('style', 'display: none !important');
                    $('#btn-save-bottom').hide();
                    return;
                }

                let html = '<div class="accordion" id="accordionPengukuranIkp">';

                data.forEach(function(sp, idx) {
                    html += `
                    <div class="accordion-item mb-3 border border-warning shadow-sm rounded overflow-hidden">
                        <h2 class="accordion-header" id="heading-sp-${idx}">
                            <button class="accordion-button bg-light text-dark fw-bold py-3"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse-sp-${idx}"
                                aria-expanded="true"
                                aria-controls="collapse-sp-${idx}">
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
                        <div id="collapse-sp-${idx}" class="accordion-collapse collapse show" aria-labelledby="heading-sp-${idx}">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0 text-center">
                                        <thead class="table-warning text-dark align-middle">
                                            <tr>
                                                <th style="min-width: 250px;" class="text-start">Indikator Kinerja Program (IKP)</th>
                                                <th style="width: 100px;">Target Thn</th>
                                                <th style="width: 100px;">TW 1</th>
                                                <th style="width: 100px;">TW 2</th>
                                                <th style="width: 100px;">TW 3</th>
                                                <th style="width: 100px;">TW 4</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                    sp.ikps.forEach(function(ikp) {
                        html += `
                            <tr>
                                <td class="text-start">
                                    <div class="fw-bold text-primary">${ikp.kode_ikp}</div>
                                    <div class="small text-muted">${ikp.nama_ikp}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fs-7">${ikp.target_tahun}</span>
                                </td>`;

                        for (let tw = 1; tw <= 4; tw++) {
                            const val = (ikp.capaians[tw] !== null && ikp.capaians[tw] !== undefined) ? ikp.capaians[tw] : '';
                            const targetTwVal = (ikp.target_tw && ikp.target_tw[tw]) ? ikp.target_tw[tw] : ikp.target_tahun;
                            const pct = (ikp.perhitungans[tw] !== null && ikp.perhitungans[tw] !== undefined)
                                ? ikp.perhitungans[tw]
                                : (targetTwVal > 0 && val !== '' ? ((val / targetTwVal) * 100).toFixed(1) : '-');

                            html += `
                                <td>
                                    <input type="number" step="any"
                                        class="form-control form-control-sm text-center input-capaian-ikp"
                                        name="capaian[${ikp.ikp_id}][${tw}]"
                                        value="${val}"
                                        data-target="${targetTwVal}"
                                        data-ikp="${ikp.ikp_id}"
                                        data-tw="${tw}"
                                        placeholder="0">
                                    <small class="text-muted d-block mt-1 span-pct" id="pct-${ikp.ikp_id}-${tw}">
                                        ${pct !== '-' ? pct + '%' : '-'}
                                    </small>
                                </td>`;
                        }

                        html += `</tr>`;
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

                $('#ikp-tables-wrapper').html(html);
                $('#accordion-action-buttons').removeAttr('style');
                $('#btn-save-bottom').show();
            },
            error: function() {
                $('#ikp-tables-wrapper').html('<div class="alert alert-danger">Gagal memuat data pengukuran IKP.</div>');
            }
        });
    }

    // Expand All / Collapse All buttons
    $(document).on('click', '#btn-expand-all', function() {
        $('#accordionPengukuranIkp .accordion-collapse').addClass('show');
        $('#accordionPengukuranIkp .accordion-button').removeClass('collapsed').attr('aria-expanded', 'true');
    });

    $(document).on('click', '#btn-collapse-all', function() {
        $('#accordionPengukuranIkp .accordion-collapse').removeClass('show');
        $('#accordionPengukuranIkp .accordion-button').addClass('collapsed').attr('aria-expanded', 'false');
    });

    // Live update perhitungan capaian
    $(document).on('input', '.input-capaian-ikp', function() {
        const val = parseFloat($(this).val());
        const target = parseFloat($(this).data('target'));
        const ikpId = $(this).data('ikp');
        const tw = $(this).data('tw');
        const pctEl = $(`#pct-${ikpId}-${tw}`);

        if (!isNaN(val) && !isNaN(target) && target > 0) {
            const calculated = ((val / target) * 100).toFixed(1);
            pctEl.text(calculated + '%');
        } else if (!isNaN(val)) {
            pctEl.text('100%');
        } else {
            pctEl.text('-');
        }
    });

    $('#btn-save-top').on('click', function() {
        $('#form-pengukuran-ikp').submit();
    });

    $('#form-pengukuran-ikp').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#btn-save-bottom');
        const topBtn = $('#btn-save-top');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');
        topBtn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: '{{ route("pengukuran-ikp.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Simpan Pengukuran');
                topBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Pengukuran');
                alert(res.message || 'Pengukuran IKP berhasil disimpan!');
            },
            error: function() {
                submitBtn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Simpan Pengukuran');
                topBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Pengukuran');
                alert('Terjadi kesalahan saat menyimpan data pengukuran.');
            }
        });
    });
});
</script>
@endpush
<style>
.bidang-nama-text {
    line-height: 1.3;
    display: inline-block;
    word-break: break-word;
}
.btn-satker-ikp {
    white-space: normal;
    text-align: left;
    padding: 0.6rem 0.75rem;
}
.accordion-button:not(.collapsed) {
    background-color: #fff8e1 !important;
    color: #000 !important;
    box-shadow: none;
}
.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,0,0,.125);
}
</style>
