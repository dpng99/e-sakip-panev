@extends('layouts.app')

@section('title', 'Input Kriteria AKIP')

@section('content')
<div class="content" id="content">
    <div class="container-fluid px-0">

        <!-- Header Banner -->
        <div class="card page-hero-card mb-4 border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark px-2 py-1 fw-bold">
                                <i class="bi bi-pencil-square me-1"></i>Master Kriteria AKIP
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1 text-white">Input Kriteria Penilaian AKIP</h3>
                        <p class="text-white-50 mb-0 small">
                            Tambahkan kriteria penilaian baru ke dalam komponen dan subkomponen evaluasi AKIP.
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dataLke') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Evaluasi AKIP
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 p-2 text-white" style="background: var(--kj-gold-dark);">
                                <i class="bi bi-ui-checks-grid fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.05rem;">Formulir Tambah Kriteria</h5>
                                <small class="text-muted">Isi seluruh informasi parameter kriteria dengan teliti</small>
                            </div>
                        </div>
                        <span class="badge bg-light text-muted border">Tahun {{ $tahun ?? date('Y') }}</span>
                    </div>

                    <div class="card-body p-4">
                        {{-- Notifikasi Sukses --}}
                        @if(session('success'))
                            <div class="alert alert-success d-flex align-items-center gap-2 py-3 px-4 rounded-3 border-0 shadow-sm mb-4">
                                <i class="bi bi-check-circle-fill fs-4 text-success flex-shrink-0"></i>
                                <div>
                                    <div class="fw-bold">Berhasil!</div>
                                    <small>{{ session('success') }}</small>
                                </div>
                            </div>
                        @endif

                        {{-- Notifikasi Error --}}
                        @if($errors->any())
                            <div class="alert alert-danger py-3 px-4 rounded-3 border-0 shadow-sm mb-4">
                                <div class="d-flex align-items-center gap-2 fw-bold mb-1 text-danger">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Terdapat Kesalahan Isian
                                </div>
                                <ul class="mb-0 ps-3 small">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('kriteria.store') }}">
                            @csrf

                            <div class="row g-4">
                                <!-- Kolom Kiri: Klasifikasi Komponen & Nilai -->
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 bg-light bg-opacity-50 border h-100">
                                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                            <i class="bi bi-diagram-3-fill text-warning me-1"></i> 1. Klasifikasi Komponen
                                        </h6>

                                        <!-- Komponen -->
                                        <div class="mb-3">
                                            <label for="id_komponen" class="form-label">
                                                Komponen Utama <span class="text-danger">*</span>
                                            </label>
                                            <select id="id_komponen" name="id_komponen" class="form-select" required>
                                                <option value="">-- Pilih Komponen --</option>
                                                @foreach($komponen as $k)
                                                    <option value="{{ $k->id }}">{{ $k->komponen }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Subkomponen -->
                                        <div class="mb-3">
                                            <label for="id_subkomponen" class="form-label d-flex justify-content-between align-items-center">
                                                <span>Subkomponen <span class="text-danger">*</span></span>
                                                <small class="text-muted d-none" id="subkomponenLoading">
                                                    <span class="spinner-border spinner-border-sm text-warning" role="status"></span> Memuat...
                                                </small>
                                            </label>
                                            <select id="id_subkomponen" name="id_subkomponen" class="form-select" required>
                                                <option value="">-- Pilih Komponen Dahulu --</option>
                                            </select>
                                        </div>

                                        <div class="row g-2">
                                            <!-- Bobot -->
                                            <div class="col-6 mb-3">
                                                <label for="bobot" class="form-label">
                                                    Bobot Nilai <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" step="any" name="bobot" id="bobot" class="form-control" placeholder="Contoh: 10" required>
                                                    <span class="input-group-text small">%</span>
                                                </div>
                                            </div>

                                            <!-- Range Nilai -->
                                            <div class="col-6 mb-3">
                                                <label for="range_nilai" class="form-label">
                                                    Range Nilai <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="range_nilai" id="range_nilai" class="form-control" placeholder="Contoh: 0 - 100" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Detail Deskripsi & Bukti -->
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 bg-light bg-opacity-50 border h-100">
                                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                            <i class="bi bi-file-text-fill text-warning me-1"></i> 2. Deskripsi & Bentuk Bukti
                                        </h6>

                                        <!-- Bentuk Bukti -->
                                        <div class="mb-3">
                                            <label for="bentuk_bukti" class="form-label">
                                                Bentuk Bukti Dukung <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-file-earmark-check"></i></span>
                                                <input type="text" name="bentuk_bukti" id="bentuk_bukti" class="form-control" placeholder="Contoh: SK Tim, Dokumen Renstra, Laporan..." required>
                                            </div>
                                        </div>

                                        <!-- Kriteria Textarea -->
                                        <div class="mb-3">
                                            <label for="kriteria" class="form-label">
                                                Rincian Kriteria Penilaian <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="kriteria" id="kriteria" class="form-control" rows="5" placeholder="Tuliskan uraian kriteria penilaian yang jelas dan terukur..." required style="resize: vertical;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                                <a href="{{ route('dataLke') }}" class="btn btn-outline-secondary px-4">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-yellow px-4 fw-bold shadow-sm">
                                    <i class="bi bi-check2-circle me-1"></i> Simpan Kriteria
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const komponenSelect = document.getElementById('id_komponen');
    const subkomponenSelect = document.getElementById('id_subkomponen');
    const loadingIndicator = document.getElementById('subkomponenLoading');

    if (komponenSelect) {
        komponenSelect.addEventListener('change', function() {
            const komponenId = this.value;
            if (!komponenId) {
                subkomponenSelect.innerHTML = '<option value="">-- Pilih Komponen Dahulu --</option>';
                return;
            }

            if (loadingIndicator) loadingIndicator.classList.remove('d-none');
            subkomponenSelect.innerHTML = '<option value="">Memuat data subkomponen...</option>';
            subkomponenSelect.disabled = true;

            fetch('{{ url('/get-subkomponen') }}/' + encodeURIComponent(komponenId))
                .then(response => response.json())
                .then(data => {
                    subkomponenSelect.innerHTML = '<option value="">-- Pilih Subkomponen --</option>';
                    data.forEach(function(sub) {
                        subkomponenSelect.innerHTML += `<option value="${sub.id}">${sub.subkomponen}</option>`;
                    });
                    subkomponenSelect.disabled = false;
                    if (loadingIndicator) loadingIndicator.classList.add('d-none');
                })
                .catch(err => {
                    subkomponenSelect.innerHTML = '<option value="">Gagal memuat subkomponen</option>';
                    subkomponenSelect.disabled = false;
                    if (loadingIndicator) loadingIndicator.classList.add('d-none');
                });
        });
    }
});
</script>
@endsection