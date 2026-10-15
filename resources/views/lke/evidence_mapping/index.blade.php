@extends('layouts.app')

@section('content')
<div class="content" id="content">
<div class="container-fluid py-4 px-4">
    <!-- Header Page -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-diagram-3-fill text-success me-2"></i>Pengaturan Mapping Bukti Dukung LKE
            </h3>
            <p class="text-muted mb-0 text-sm">
                Kelola relasi bukti dukung (evidence) per kriteria secara mudah, cepat, dan terintegrasi dengan tabel sumber database.
            </p>
        </div>
        <div class="mt-2 mt-md-0">
            <span class="badge bg-success px-3 py-2 text-xs rounded-pill">
                <i class="bi bi-shield-lock-fill me-1"></i> Mode Administrator
            </span>
        </div>
    </div>

    <!-- Metric Cards (Low Cognitive Load Overview) -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100 border-start border-success border-4">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                        <i class="bi bi-card-checklist fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted text-xs">Total Kriteria LKE</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalKriteria }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100 border-start border-success border-4">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-circle me-3">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted text-xs">Kriteria Ter-Mapping</div>
                        <h4 class="fw-bold mb-0 text-success">{{ $mappedCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100 border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-circle me-3">
                        <i class="bi bi-files fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted text-xs">Master Dokumen Bukti Dukung</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $masterBukti->count() }} Dokumen</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('lke.evidence_mapping.index') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label text-xs text-muted mb-1">Filter Komponen</label>
                    <select name="komponen_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Komponen --</option>
                        @foreach($komponenList as $k)
                            <option value="{{ $k->id }}" {{ $komponenFilter == $k->id ? 'selected' : '' }}>
                                {{ $k->no }}. {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xs text-muted mb-1">Filter Subkomponen</label>
                    <select name="subkomponen_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Subkomponen --</option>
                        @foreach($subkomponenList as $sub)
                            <option value="{{ $sub->kode }}" {{ $subkomponenFilter == $sub->kode ? 'selected' : '' }}>
                                {{ $sub->kode }} - {{ Str::limit($sub->nama, 35) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-xs text-muted mb-1">Pencarian Cepat</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari kode (misal: 1.A.5) atau nama kriteria..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-emerald btn-sm w-100 me-2">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('lke.evidence_mapping.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table of Criteria & Mapped Evidence -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                Daftar Kriteria & Bukti Dukung
            </h5>
            <span class="text-muted small">Menampilkan {{ $kriteriaList->count() }} Kriteria</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tableKriteria">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 70px;">Kode</th>
                        <th style="min-width: 250px;">Nama Kriteria</th>
                        <th style="min-width: 380px;">Bukti Dukung Terpetakan (Evidence)</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kriteriaList as $item)
                        @php
                            $evidenceIds = $item->bukti_dukung_ids;
                        @endphp
                        <tr id="row-{{ str_replace('.', '-', $item->kode) }}">
                            <td class="text-center">
                                <span class="badge bg-dark px-2 py-1 fs-6 font-monospace">{{ $item->kode }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->nama }}</div>
                                <div class="text-muted small mt-1">
                                    <span class="badge bg-secondary-subtle text-secondary me-1">{{ $item->subkomponen_id }}</span>
                                    {{ $item->subkomponen->nama ?? '' }}
                                </div>
                            </td>
                            <td>
                                <div id="evidence-container-{{ str_replace('.', '-', $item->kode) }}">
                                    @if(!empty($evidenceIds))
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            @foreach($evidenceIds as $eid)
                                                @php
                                                    $doc = $masterBukti->firstWhere('id', $eid);
                                                @endphp
                                                <span class="badge bg-white text-dark border shadow-xs p-2 d-inline-flex align-items-center" style="font-size: 0.82rem;">
                                                    <span class="badge bg-primary me-1">#{{ $eid }}</span>
                                                    <span class="text-truncate" style="max-width: 260px;" title="{{ $doc->dokumen ?? 'Dokumen #'.$eid }}">
                                                        {{ $doc->dokumen ?? 'Dokumen #'.$eid }}
                                                    </span>
                                                    @if($doc && $doc->tabel_sumber)
                                                        <span class="badge bg-info-subtle text-info ms-1" style="font-size: 0.7rem;">{{ $doc->tabel_sumber }}</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            <i class="bi bi-exclamation-circle me-1"></i> Belum ada bukti dukung
                                        </span>
                                    @endif
                                </div>
                                <div id="langkah-container-{{ str_replace('.', '-', $item->kode) }}" class="mt-2 pt-2 border-top">
                                    <div class="text-xs fw-semibold text-muted mb-1 d-flex align-items-center">
                                        <i class="bi bi-list-task text-primary me-1"></i> Langkah Kerja:
                                    </div>
                                    @if(!empty($item->langkah_kerja))
                                        <div class="text-xs text-dark bg-light p-2 rounded border" style="white-space: pre-line;">{!! nl2br(e($item->langkah_kerja)) !!}</div>
                                    @else
                                        <span class="text-muted text-xs fst-italic">Belum ada langkah kerja.</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary px-3 rounded-pill btn-edit-mapping"
                                        data-kode="{{ $item->kode }}"
                                        data-nama="{{ $item->nama }}">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Mapping
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada data kriteria yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<!-- Modal Intuitive Evidence Mapping Editor (Low Cognitive Load) -->
<div class="modal fade" id="modalEditMapping" tabindex="-1" aria-labelledby="modalEditMappingLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="modalEditMappingLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Bukti Dukung Kriteria
                    </h5>
                    <small class="text-white-50">Sesuaikan dokumen bukti dukung yang menjadi syarat kriteria ini</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Info Kriteria Card -->
                <div class="p-3 bg-light rounded-3 mb-4 border">
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge bg-dark fs-6 font-monospace me-2" id="modalKriteriaKode">1.A.5</span>
                        <span class="text-muted small">Kriteria Penilaian LKE</span>
                    </div>
                    <div class="fw-semibold text-dark fs-6" id="modalKriteriaNama">-</div>
                </div>

                <!-- Selected Evidence Chips (Visual & Easy to Remove) -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                        <span>Bukti Dukung Terpilih (<span id="selectedCount">0</span> Dokumen)</span>
                        <small class="text-muted">Klik ikon <i class="bi bi-x-circle text-danger"></i> untuk menghapus</small>
                    </label>
                    <div id="selectedChipsContainer" class="p-3 border rounded-3 bg-light-subtle d-flex flex-wrap gap-2 min-vh-25" style="min-height: 60px;">
                        <span class="text-muted small fst-italic py-1" id="emptyChipsNotice">Belum ada bukti dukung yang dipilih.</span>
                    </div>
                </div>

                <!-- Searchable Master Evidence Document List -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold text-dark mb-0">Pilih dari Master Bukti Dukung</label>
                        <div class="input-group input-group-sm" style="max-width: 250px;">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="filterMasterInput" class="form-control" placeholder="Cari nama dokumen / tabel...">
                        </div>
                    </div>

                    <div class="border rounded-3 p-2 bg-white" style="max-height: 280px; overflow-y: auto;" id="masterEvidenceList">
                        <div class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Memuat daftar dokumen bukti dukung...
                        </div>
                </div>

                <!-- Langkah Kerja / Petunjuk Kerja Section -->
                <div class="mt-4 pt-3 border-top">
                    <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center" for="modalLangkahKerja">
                        <span><i class="bi bi-list-task text-primary me-1"></i> Langkah Kerja / Panduan Pengerjaan</span>
                        <small class="text-muted">Panduan teknis langkah pemenuhan untuk kriteria ini</small>
                    </label>
                    <textarea class="form-control" id="modalLangkahKerja" rows="4" placeholder="Tuliskan petunjuk atau langkah-langkah kerja untuk kriteria ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary px-4 rounded-pill" id="btnSaveMapping">
                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090">
    <div id="mappingToast" class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fs-6" id="toastMessage">
                Mapping berhasil disimpan!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentKode = null;
    let selectedBuktiIds = new Set();
    let allMasterBukti = [];

    const modalEl = document.getElementById('modalEditMapping');
    const modal = new bootstrap.Modal(modalEl);
    const toastEl = document.getElementById('mappingToast');
    const toast = new bootstrap.Toast(toastEl, { delay: 3500 });

    // Show Toast Helper
    function showToast(message, isSuccess = true) {
        const toastMessage = document.getElementById('toastMessage');
        toastMessage.innerHTML = message;
        toastEl.className = `toast align-items-center text-white border-0 shadow ${isSuccess ? 'bg-success' : 'bg-danger'}`;
        toast.show();
    }

    // Open Modal and load criteria details
    document.querySelectorAll('.btn-edit-mapping').forEach(button => {
        button.addEventListener('click', function () {
            currentKode = this.getAttribute('data-kode');
            const kriteriaNama = this.getAttribute('data-nama');

            document.getElementById('modalKriteriaKode').textContent = currentKode;
            document.getElementById('modalKriteriaNama').textContent = kriteriaNama;
            document.getElementById('modalLangkahKerja').value = '';
            document.getElementById('masterEvidenceList').innerHTML = `
                <div class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Memuat data kriteria...
                </div>
            `;

            modal.show();

            // Fetch detail from backend
            fetch(`/lke/evidence-mapping/detail/${encodeURIComponent(currentKode)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        selectedBuktiIds = new Set(data.selectedIds);
                        allMasterBukti = data.allBukti;
                        document.getElementById('modalLangkahKerja').value = (data.kriteria && data.kriteria.langkah_kerja) ? data.kriteria.langkah_kerja : '';
                        renderSelectedChips();
                        renderMasterList();
                    } else {
                        showToast('Gagal memuat detail kriteria', false);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Terjadi kesalahan koneksi', false);
                });
        });
    });

    // Render Selected Chips
    function renderSelectedChips() {
        const container = document.getElementById('selectedChipsContainer');
        container.innerHTML = '';

        if (selectedBuktiIds.size === 0) {
            container.innerHTML = '<span class="text-muted small fst-italic py-1" id="emptyChipsNotice">Belum ada bukti dukung yang dipilih.</span>';
            document.getElementById('selectedCount').textContent = '0';
            return;
        }

        document.getElementById('selectedCount').textContent = selectedBuktiIds.size;

        selectedBuktiIds.forEach(id => {
            const item = allMasterBukti.find(b => b.id === id);
            const title = item ? item.dokumen : `Dokumen #${id}`;
            const tableSource = item && item.tabel_sumber ? item.tabel_sumber : '';

            const chip = document.createElement('div');
            chip.className = 'badge bg-white text-dark border shadow-xs p-2 d-inline-flex align-items-center';
            chip.style.fontSize = '0.85rem';
            chip.innerHTML = `
                <span class="badge bg-primary me-1">#${id}</span>
                <span class="text-truncate me-2" style="max-width: 320px;" title="${title}">${title}</span>
                ${tableSource ? `<span class="badge bg-info-subtle text-info me-2" style="font-size: 0.7rem;">${tableSource}</span>` : ''}
                <button type="button" class="btn-close btn-close-sm ms-auto" aria-label="Remove" data-remove-id="${id}"></button>
            `;

            chip.querySelector('button').addEventListener('click', function () {
                const removeId = parseInt(this.getAttribute('data-remove-id'));
                selectedBuktiIds.delete(removeId);
                renderSelectedChips();
                renderMasterList();
            });

            container.appendChild(chip);
        });
    }

    // Render Master Evidence Checkbox List
    function renderMasterList(searchQuery = '') {
        const listContainer = document.getElementById('masterEvidenceList');
        listContainer.innerHTML = '';

        const query = searchQuery.toLowerCase().trim();
        const filtered = allMasterBukti.filter(b => {
            if (!query) return true;
            return b.dokumen.toLowerCase().includes(query)
                || (b.tabel_sumber && b.tabel_sumber.toLowerCase().includes(query))
                || b.id.toString().includes(query);
        });

        if (filtered.length === 0) {
            listContainer.innerHTML = '<div class="text-center py-3 text-muted small">Tidak ditemukan dokumen bukti yang cocok.</div>';
            return;
        }

        filtered.forEach(b => {
            const isChecked = selectedBuktiIds.has(b.id);
            const itemDiv = document.createElement('div');
            itemDiv.className = `p-2 border-bottom d-flex align-items-center justify-content-between rounded-1 ${isChecked ? 'bg-primary-subtle' : ''}`;
            itemDiv.style.cursor = 'pointer';

            itemDiv.innerHTML = `
                <div class="form-check d-flex align-items-center mb-0 w-100">
                    <input class="form-check-input me-3" type="checkbox" value="${b.id}" id="chkBukti${b.id}" ${isChecked ? 'checked' : ''}>
                    <label class="form-check-label w-100 d-flex flex-wrap justify-content-between align-items-center cursor-pointer" for="chkBukti${b.id}">
                        <div>
                            <span class="badge bg-secondary me-1">#${b.id}</span>
                            <span class="fw-semibold text-dark">${b.dokumen}</span>
                        </div>
                        <div class="mt-1 mt-md-0">
                            ${b.tabel_sumber ? `<span class="badge bg-info-subtle text-info border border-info-subtle">${b.tabel_sumber}</span>` : '<span class="badge bg-light text-muted">Upload Manual</span>'}
                        </div>
                    </label>
                </div>
            `;

            const chk = itemDiv.querySelector('input');
            chk.addEventListener('change', function () {
                const id = parseInt(this.value);
                if (this.checked) {
                    selectedBuktiIds.add(id);
                } else {
                    selectedBuktiIds.delete(id);
                }
                renderSelectedChips();
                renderMasterList(document.getElementById('filterMasterInput').value);
            });

            listContainer.appendChild(itemDiv);
        });
    }

    // Filter master list on search typing
    document.getElementById('filterMasterInput').addEventListener('input', function () {
        renderMasterList(this.value);
    });

    // Save Mapping AJAX
    document.getElementById('btnSaveMapping').addEventListener('click', function () {
        if (!currentKode) return;

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...';

        const buktiArray = Array.from(selectedBuktiIds);
        const langkahKerjaVal = document.getElementById('modalLangkahKerja').value.trim();

        fetch(`/lke/evidence-mapping/update/${encodeURIComponent(currentKode)}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                bukti_ids: buktiArray,
                langkah_kerja: langkahKerjaVal
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Perubahan';

            if (data.success) {
                showToast(data.message, true);
                modal.hide();

                // Update row in table immediately without reloading page
                const rowSafeKode = currentKode.replace(/\./g, '-');
                const container = document.getElementById(`evidence-container-${rowSafeKode}`);
                if (container) {
                    if (data.mapped_items && data.mapped_items.length > 0) {
                        let html = '<div class="d-flex flex-wrap gap-1 align-items-center">';
                        data.mapped_items.forEach(doc => {
                            html += `
                                <span class="badge bg-white text-dark border shadow-xs p-2 d-inline-flex align-items-center" style="font-size: 0.82rem;">
                                    <span class="badge bg-primary me-1">#${doc.id}</span>
                                    <span class="text-truncate" style="max-width: 260px;" title="${doc.dokumen}">
                                        ${doc.dokumen}
                                    </span>
                                    ${doc.tabel_sumber ? `<span class="badge bg-info-subtle text-info ms-1" style="font-size: 0.7rem;">${doc.tabel_sumber}</span>` : ''}
                                </span>
                            `;
                        });
                        html += '</div>';
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                <i class="bi bi-exclamation-circle me-1"></i> Belum ada bukti dukung
                            </span>
                        `;
                    }
                }

                // Update langkah kerja container in table
                const langkahContainer = document.getElementById(`langkah-container-${rowSafeKode}`);
                if (langkahContainer) {
                    if (data.langkah_kerja && data.langkah_kerja.trim() !== '') {
                        const escapedText = data.langkah_kerja
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#039;')
                            .replace(/\n/g, '<br>');
                        langkahContainer.innerHTML = `
                            <div class="text-xs fw-semibold text-muted mb-1 d-flex align-items-center">
                                <i class="bi bi-list-task text-primary me-1"></i> Langkah Kerja:
                            </div>
                            <div class="text-xs text-dark bg-light p-2 rounded border" style="white-space: pre-line;">${escapedText}</div>
                        `;
                    } else {
                        langkahContainer.innerHTML = `
                            <div class="text-xs fw-semibold text-muted mb-1 d-flex align-items-center">
                                <i class="bi bi-list-task text-primary me-1"></i> Langkah Kerja:
                            </div>
                            <span class="text-muted text-xs fst-italic">Belum ada langkah kerja.</span>
                        `;
                    }
                }
            } else {
                showToast(data.message || 'Gagal menyimpan perubahan.', false);
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Perubahan';
            showToast('Terjadi kesalahan sistem saat menyimpan.', false);
        });
    });
});
</script>
@endpush
@endsection
