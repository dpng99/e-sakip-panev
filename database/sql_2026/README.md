# Panduan File SQL Migrasi & Seeder Tahun 2026

Direktori ini berisi hasil konversi seluruh skema migrasi dan data seeding tahun 2026 menjadi query mentah SQL (`.sql`) yang kompatibel dengan MySQL 8.x / MariaDB.

---

## Daftar File SQL

| Nama File | Deskripsi | Ukuran |
| :--- | :--- | :--- |
| **`00_full_2026_setup.sql`** | Gabungan lengkap seluruh DDL Migrasi dan DML Seeder tahun 2026 (siap dieksekusi sekaligus). | ~141 KB |
| **`01_migrations_2026.sql`** | DDL Skema tabel baru tahun 2026 dan alterasi kolom tabel eksisting. | ~14 KB |
| **`02_seeders_2026.sql`** | DML seluruh data master dari seeder tahun 2026 (semua baris data, termasuk riwayat). | ~126 KB |
| **`02_seeders_2026_only.sql`**| DML khusus baris data yang berfilter tahun berjalan `2026`. | ~68 KB |

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
2. Buka menu **File** > **Load SQL file...** (atau tab Query).
3. Pilih file `00_full_2026_setup.sql` (atau jalankan `01_migrations_2026.sql` kemudian `02_seeders_2026.sql`).
4. Jalankan/Eksekusi script.
