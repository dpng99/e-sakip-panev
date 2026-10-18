@extends('layouts.app')

@section('content')
<div class="content" id="content">
<div class="container-fluid py-4 px-4">
    <!-- Header Page -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>Master Bukti Dukung
            </h3>
            <p class="text-muted mb-0 text-sm">
                Kelola daftar master bukti dukung LKE yang tersedia dalam sistem secara dinamis.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('lke.master_bukti.sync_schema') }}" method="POST" onsubmit="return confirm('Jalankan pemeriksaan & penambahan kolom database otomatis berdasarkan skema rujukan simemek3.sql? Kolom yang sudah ada tidak akan diubah.');" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm" title="Periksa dan tambahkan kolom yang belum ada di database secara idempoten">
                    <i class="bi bi-arrow-repeat me-1"></i> Sinkronisasi Kolom DB
                </button>
            </form>
            <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm text-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Bukti Dukung Baru
            </button>
            <span class="badge bg-primary px-3 py-2 text-xs rounded-pill">
                <i class="bi bi-shield-lock-fill me-1"></i> Mode Administrator
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tableMasterBukti">
                    <thead class="table-light text-secondary text-xs text-uppercase">
                        <tr>
                            <th style="width: 5%" class="text-center">ID</th>
                            <th style="width: 35%">Nama Dokumen</th>
                            <th style="width: 20%">Pola Nama Berkas Fisik</th>
                            <th style="width: 20%">Tabel Sumber (SAKIP)</th>
                            <th style="width: 8%" class="text-center">Tahun</th>
                            <th style="width: 12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($masterBukti as $bukti)
                            <tr>
                                <td class="text-center fw-bold text-muted">#{{ $bukti->id }}</td>
                                <td class="fw-medium text-dark">
                                    {{ $bukti->dokumen }}
                                    @if(!empty($bukti->keterangan) && $bukti->keterangan !== '-')
                                        <div class="text-xs text-muted mt-1"><i class="bi bi-info-circle me-1"></i>{{ $bukti->keterangan }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($bukti->format_nama_file)
                                        <code class="text-xs text-primary bg-light px-2 py-1 rounded">{{ $bukti->format_nama_file }}</code>
                                    @else
                                        <span class="text-muted text-xs fst-italic">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($bukti->tabel_sumber)
                                        <span class="badge bg-light text-dark border"><i class="bi bi-database me-1 text-primary"></i>{{ $bukti->tabel_sumber }}</span>
                                    @else
                                        <span class="badge bg-light text-muted border fst-italic">Unggahan Fisik Mandiri</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $bukti->tahun ?? 'Semua' }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 edit-btn"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal"
                                            data-id="{{ $bukti->id }}"
                                            data-dokumen="{{ $bukti->dokumen }}"
                                            data-tabel="{{ $bukti->tabel_sumber }}"
                                            data-format="{{ $bukti->format_nama_file }}"
                                            data-keterangan="{{ $bukti->keterangan }}"
                                            data-tahun="{{ $bukti->tahun }}"
                                            title="Edit Master Bukti">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('lke.master_bukti.destroy', $bukti->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Master Bukti Dukung #{{ $bukti->id }} ({{ $bukti->dokumen }})?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" title="Hapus Master Bukti">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-bottom-0 rounded-top-4 pb-3">
                <h5 class="modal-title fw-bold text-dark" id="createModalLabel"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Bukti Dukung Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lke.master_bukti.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label for="create_dokumen" class="form-label fw-medium text-secondary text-sm">Nama Dokumen / Standar Evidence <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3" id="create_dokumen" name="dokumen" placeholder="Contoh: Standar Operasional Prosedur (SOP) Pengukuran Kinerja..." required>
                    </div>
                    <div class="mb-3">
                        <label for="create_format_nama_file" class="form-label fw-medium text-secondary text-sm">Pola Nama Berkas Fisik (Opsional)</label>
                        <input type="text" class="form-control rounded-3" id="create_format_nama_file" name="format_nama_file" placeholder="Contoh: sop_satker_{tahun}.pdf">
                        <div class="form-text mt-1 text-muted text-xs">
                            Gunakan tag <code>{tahun}</code> untuk pola nama file yang diunggah satker.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="create_tabel_sumber" class="form-label fw-medium text-secondary text-sm">Tabel Sumber Data (Opsional jika terintegrasi modul SAKIP)</label>
                        <select class="form-select rounded-3" id="create_tabel_sumber" name="tabel_sumber">
                            <option value="">-- Tidak ada (Unggahan Berkas Mandiri) --</option>
                            @foreach($tabelSumberList as $tabel)
                                <option value="{{ $tabel }}">{{ $tabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create_keterangan" class="form-label fw-medium text-secondary text-sm">Keterangan / Panduan</label>
                        <textarea class="form-control rounded-3 text-sm" id="create_keterangan" name="keterangan" rows="2" placeholder="Catatan petunjuk bagi satker..."></textarea>
                    </div>
                    <div class="mb-2">
                        <label for="create_tahun" class="form-label fw-medium text-secondary text-sm">Tahun Spesifik (Kosongkan jika berlaku untuk semua tahun)</label>
                        <select class="form-select rounded-3" id="create_tahun" name="tahun">
                            <option value="">-- Berlaku untuk Semua Tahun (Universal Master) --</option>
                            @foreach($tahunList as $thn)
                                <option value="{{ $thn }}">{{ $thn }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4 pt-2 pb-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-save me-1"></i> Simpan Bukti Dukung</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-bottom-0 rounded-top-4 pb-3">
                <h5 class="modal-title fw-bold text-dark" id="editModalLabel"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Bukti Dukung</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editForm">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label for="dokumen" class="form-label fw-medium text-secondary text-sm">Nama Dokumen</label>
                        <input type="text" class="form-control rounded-3" id="dokumen" name="dokumen" placeholder="Nama bukti dukung..." required>
                        <div class="form-text mt-1 text-muted text-xs">
                            <i class="bi bi-info-circle me-1"></i>Isi sebelumnya: <span id="prev_dokumen" class="fw-semibold text-dark"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="format_nama_file" class="form-label fw-medium text-secondary text-sm">Pola Nama Berkas Fisik</label>
                        <input type="text" class="form-control rounded-3" id="format_nama_file" name="format_nama_file" placeholder="Pola berkas file...">
                    </div>
                    <div class="mb-3">
                        <label for="tabel_sumber" class="form-label fw-medium text-secondary text-sm">Tabel Sumber</label>
                        <select class="form-select rounded-3" id="tabel_sumber" name="tabel_sumber">
                            <option value="">-- Tidak ada (Unggahan Berkas Mandiri) --</option>
                            @foreach($tabelSumberList as $tabel)
                                <option value="{{ $tabel }}">{{ $tabel }}</option>
                            @endforeach
                        </select>
                        <div class="form-text mt-1 text-muted text-xs">
                            <i class="bi bi-info-circle me-1"></i>Tabel sebelumnya: <span id="prev_tabel" class="fw-semibold text-dark"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-medium text-secondary text-sm">Keterangan / Panduan</label>
                        <textarea class="form-control rounded-3 text-sm" id="keterangan" name="keterangan" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <label for="tahun" class="form-label fw-medium text-secondary text-sm">Tahun</label>
                        <select class="form-select rounded-3" id="tahun" name="tahun">
                            <option value="">-- Berlaku untuk Semua Tahun (Universal Master) --</option>
                            @foreach($tahunList as $thn)
                                <option value="{{ $thn }}">{{ $thn }}</option>
                            @endforeach
                        </select>
                        <div class="form-text mt-1 text-muted text-xs">
                            <i class="bi bi-info-circle me-1"></i>Tahun sebelumnya: <span id="prev_tahun" class="fw-semibold text-dark"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4 pt-2 pb-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#tableMasterBukti').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                },
                "pageLength": 25,
                "order": [[0, "asc"]]
            });
        }

        // Event listener saat modal edit akan ditampilkan
        const editModalEl = document.getElementById('editModal');
        if (editModalEl) {
            editModalEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (!button) return;

                const id = button.getAttribute('data-id') || '';
                const dokumen = button.getAttribute('data-dokumen') || '';
                const tabel = button.getAttribute('data-tabel') || '';
                const format = button.getAttribute('data-format') || '';
                const keterangan = button.getAttribute('data-keterangan') || '';
                const tahun = button.getAttribute('data-tahun') || '';

                $('#editForm').attr('action', `/lke/master-bukti/update/${id}`);
                $('#dokumen').val(dokumen);
                $('#tabel_sumber').val(tabel);
                $('#format_nama_file').val(format);
                $('#keterangan').val(keterangan);
                $('#tahun').val(tahun);

                $('#prev_dokumen').text(dokumen || '-');
                $('#prev_tabel').text(tabel || '-');
                $('#prev_tahun').text(tahun || 'Semua Tahun');
            });
        }

        $(document).on('click', '.edit-btn', function() {
            const id = $(this).attr('data-id');
            const dokumen = $(this).attr('data-dokumen') || '';
            const tabel = $(this).attr('data-tabel') || '';
            const format = $(this).attr('data-format') || '';
            const keterangan = $(this).attr('data-keterangan') || '';
            const tahun = $(this).attr('data-tahun') || '';

            $('#editForm').attr('action', `/lke/master-bukti/update/${id}`);
            $('#dokumen').val(dokumen);
            $('#tabel_sumber').val(tabel);
            $('#format_nama_file').val(format);
            $('#keterangan').val(keterangan);
            $('#tahun').val(tahun);

            $('#prev_dokumen').text(dokumen || '-');
            $('#prev_tabel').text(tabel || '-');
            $('#prev_tahun').text(tahun || 'Semua Tahun');
        });
    });
</script>
@endpush
@endsection
