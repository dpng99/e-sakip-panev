---
description: Standar Integritas Data Pengukuran dan Pelaporan Kinerja SAKIP
globs:
  - "app/Models/Indikator*.php"
  - "app/Models/Pengukuran*.php"
  - "app/Models/Target*.php"
  - "app/Http/Controllers/Pengukuran*.php"
  - "app/Http/Controllers/Pelaporan*.php"
  - "resources/views/kelola/pengukuran*.blade.php"
  - "resources/views/kelola/pelaporan*.blade.php"
alwaysApply: false
---

# Standar Integritas Data Pengukuran & Pelaporan SAKIP

## 1. Penanganan Primary Key String pada Indikator SAKIP
- Tabel `sinori_sakip_indikator` menggunakan tipe data `VARCHAR(50)` untuk kolom `id` (misalnya kode format `IKSS1-1`, `IKSS2-1`, dsb.).
- Model Eloquent `Indikator` dan `SinoriSakipIndikator` WAJIB mendeklarasikan:
  ```php
  public $incrementing = false;
  protected $keyType = 'string';
  ```
- Dalam query builder (`DB::table('pengukuran')` atau `DB::table('target')`), parameter `indikator_id` HARUS selalu dicasting secara eksplisit ke string: `(string) $indikator->id` atau `(string) $indikatorId`.
- HINDARI membandingkan kolom string dengan integer 0 di MySQL untuk mencegah koersi tipe (*type coercion*) yang menyebabkan seluruh string non-angka dianggap cocok dengan 0.
- Validasi request untuk `indikator_id` HARUS menggunakan `'required'` atau `'required|string'`, TIDAK BOLEH `'required|integer'`.

## 2. Standar Hak Akses Menu Pengukuran (Role & Scope Invariant)
- Menu Pengukuran digunakan oleh seluruh tingkatan satker:
  - Kejagung (Level 1): `/pengukuran-ikp`
  - Kejati (Level 2), Kejari (Level 3), Cabjari (Level 4), dan Admin (Level 99): `/pengukuran`
- `checkAccess()` pada `PengukuranController` HARUS mengizinkan level `[1, 2, 3, 4, 99]` atau satker `admin`/`999999`.
- Di `sidebar.blade.php`, tautan Pengukuran wajib aktif untuk level `[1, 2, 3, 4, 99]`.

## 3. Isolasi Data Pengukuran Antar-Tahun dan Sesi Satker
- Saat mengambil data pada `getPengukuran($indikatorId)`, wajib menerapkan filter:
  - `id_satker` dari `session('id_satker') ?? auth()->user()?->id_satker`
  - `tahun` dari `session('tahun_terpilih')`
  - `khusus` dari `session('tw4_khusus', false)`
- Pada penyimpanan catatan Pelaporan (`simpanKeterangan`), jika baris pengukuran di bulan akhir triwulan belum tersedia, sistem wajib membuat baris pengukuran baru (`Pengukuran::create`) agar keterangan faktor penghambat dan langkah optimalisasi tidak hilang.
