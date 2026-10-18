# Panduan File SQL Migrasi & Seeder Tahun 2026

Direktori ini berisi hasil konversi seluruh skema migrasi dan data seeding tahun 2026 menjadi query mentah SQL (`.sql`) yang kompatibel dengan MySQL 8.x / MariaDB.

---

## Daftar File SQL

| Nama File | Deskripsi | Ukuran |
| :--- | :--- | :--- |
| **`00_full_2026_setup.sql`** | Gabungan lengkap seluruh DDL Migrasi dan DML Seeder tahun 2026 (siap dieksekusi sekaligus). | ~141 KB |
| **`01_migrations_2026.sql`** | DDL Skema tabel baru tahun 2026 dan alterasi kolom tabel eksisting (termasuk lke_buktidukung & lke_gabungan). | ~18 KB |
| **`02_seeders_2026.sql`** | DML seluruh data master dari seeder tahun 2026 (semua baris data, termasuk riwayat). | ~126 KB |
| **`02_seeders_2026_only.sql`**| DML khusus baris data yang berfilter tahun berjalan `2026`. | ~68 KB |
| **`03_import_lke_bukti_dukung_dan_kriteria_2026.sql`** | DDL & DML khusus perbaikan tabel `lke_buktidukung` (memperbaiki error 1054 kolom `tabel_sumber`), pembuatan `lke_gabungan`, import 48 Master Bukti Dukung, update 77 Kriteria LKE 2026 (langkah kerja, dokumen bukti, kode bukti), dan sinkronisasi relasi gabungan 2026. | ~52 KB |
| **`04_sync_all_columns_simemek3.sql`** | DDL Idempoten 100% untuk sinkronisasi seluruh 26 tabel & kolom berdasarkan skema rujukan `simemek3.sql`. Menambah kolom yang belum ada dan melewati kolom yang sudah ada. | ~182 KB |

---

## Rincian Objek Database

### 1. Migrasi Skema (DDL) Tahun 2026
1. **`lke_satker_bukti`** *(Baru)*: Tabel bukti dukung satker per kriteria LKE (`2026_09_08_110000`).
2. **`lke_penilaian_satker`** *(Baru)*: Tabel hasil penilaian mandiri (PM) satker & evaluasi pokja (`2026_09_08_110000` & `2026_10_07_140000`).
3. **`dokumen_sakips`** *(Baru)*: Repositori dokumen pedoman dan template SAKIP (`2026_09_10_102703`).
4. **`sasaran_program`** *(Baru)*: Master sasaran program (SP) cascading kejaksaan (`2026_09_29_100001`).
5. **`indikator_kinerja_program`** *(Baru)*: Master indikator kinerja program (IKP) (`2026_09_29_100002`).
6. **`target_ikp`** *(Baru)*: Target tahunan dan triwulanan IKP per satker (`2026_09_29_100003`).
7. **`pelaporan_ikp`** *(Baru)*: Realisasi capaian, faktor, dan langkah optimalisasi per triwulan (`2026_09_29_100004` & `2026_09_30_100001`).
8. **`lke_subkomponen_koefisien`** *(Baru)*: Evaluasi dan penilaian mandiri koefisien 12 subkomponen (`2026_10_07_170000`).
9. **`lke_parameter`** *(Pendukung Seeder)*: Master parameter biner penilaian kriteria LKE (Terpenuhi / Tidak Terpenuhi).
10. **Alterasi Tabel Eksisting**:
    - `lke_komponen`: Penambahan kolom `bobot` (DECIMAL(5,2)).
    - `lke_subkomponen`: Penambahan kolom `bobot` (DECIMAL(5,2)).
    - `lke_kriteria`: Penambahan kolom `langkah_kerja` (TEXT) (`2026_10_14_180000`).

---

### 2. Seeder Data (DML) Tahun 2026
1. **Bobot Standar SAKIP**: Update bobot untuk 4 komponen utama dan 12 subkomponen SAKIP.
2. **`dokumen_sakips`** (25 baris):
   - Kategori `pedoman_ketentuan` (3 dokumen)
   - Kategori `template_tahun_berjalan` (5 dokumen template tahun 2026)
   - Kategori `arsip_template` (17 dokumen template)
3. **`sasaran_program`**: Master SP (34 baris untuk tahun 2026).
4. **`indikator_kinerja_program`**: Master IKP (63 baris untuk tahun 2026).
5. **`target_ikp`**: Target tahunan dan target triwulan (63 baris untuk tahun 2026).
6. **`lke_parameter`**: Opsi nilai kriteria Terpenuhi (skor 1) dan Tidak Terpenuhi (skor 0) (154 baris untuk tahun 2026).

---

## Cara Menjalankan File SQL

### Opsi A: Melalui Command Line (MySQL CLI)
```bash
mysql -u root -p panevkejaksaan_prosakip < database/sql_2026/00_full_2026_setup.sql
```

### Opsi B: Melalui HeidiSQL / phpMyAdmin / DBeaver
1. Buka database target (`panevkejaksaan_prosakip`).
2. Buka menu **File** > **Load SQL file...** (atau tab Query / Import).
3. Jika ingin setup database penuh dari awal: pilih `00_full_2026_setup.sql`.
4. Jika **hanya ingin memperbarui / import master bukti dukung 2026 & kriteria evaluasi (sekaligus mengatasi error 1054 tabel_sumber & tahun di server testing)**: pilih `03_import_lke_bukti_dukung_dan_kriteria_2026.sql`.
5. Jika **ingin menyinkronkan seluruh kolom dari berkas rujukan `simemek3.sql` sekaligus secara idempoten**: pilih `04_sync_all_columns_simemek3.sql`.
6. Jalankan/Eksekusi script.

---

### Opsi C: Melalui Fitur Aplikasi (Tanpa Perlu Akses Database Manual)

1. **Melalui Artisan Command (CLI)**:
   ```bash
   # Cek kolom apa saja yang kurang (simulasi/dry-run)
   php artisan schema:sync-columns --dry-run

   # Eksekusi penambahan kolom untuk seluruh tabel
   php artisan schema:sync-columns --force

   # Eksekusi untuk tabel tertentu saja (misal lke_gabungan)
   php artisan schema:sync-columns --table=lke_gabungan --force
   ```

2. **Melalui Browser / Helper CPanel (Tanpa SSH)**:
   Buka URL:
   `https://panev.kejaksaan.go.id/cpanel-setup?key=prosakip2026&action=sync-columns`

3. **Melalui Antarmuka Admin Master Bukti Dukung**:
   Buka menu **LKE Master Bukti** (`/lke/master-bukti`), lalu klik tombol **"Sinkronisasi Kolom DB"** di bagian header.


---

## Catatan Arsitektur Penting (Invariants)
- **Komponen ID**: Seluruh data komponen tetap menggunakan **ID 1 s/d 4** (`Perencanaan Kinerja`, `Pengukuran Kinerja`, `Pelaporan Kinerja`, `Evaluasi Akuntabilitas Kinerja Internal`). Script tidak menambahkan ID baru > 4, melainkan mengupdate in-place bobot dan keterangannya.
- **Relasi Gabungan**: Tabel `lke_gabungan` merujuk ke `komponen_id` bernilai 1, 2, 3, atau 4.
- **Master Bukti 45-48**: Bukti dukung ID 45-48 telah dipetakan di `LkeEvidenceHelper` dan siap diunggah atau disinkronkan.

