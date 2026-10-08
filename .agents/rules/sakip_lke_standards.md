---
description: Standar Sinkronisasi Bukti Dukung LKE dan Kaidah UI/UX Tata Kelola AKIP
globs:
  - "app/Http/Controllers/**/*.php"
  - "app/Helpers/Lke*.php"
  - "app/Models/Lke*.php"
  - "resources/views/kelola/**/*.blade.php"
  - "resources/views/lke/**/*.blade.php"
alwaysApply: false
---

# Standar Sinkronisasi Bukti Dukung LKE & UI/UX PROSAKIP

## 1. Kebijakan Sinkronisasi Bukti Dukung (Unified Evidence Policy)
- Setiap kali berinteraksi dengan bukti dukung LKE, data antara `bukti_dukung` (legacy) dan `lke_satker_bukti` (LKE baru) HARUS selalu disinkronkan dua arah melalui `App\Helpers\LkeEvidenceHelper`.
- Normalisasi kode kriteria (misal `1.B.1.1` -> `1.B.1`) wajib diterapkan saat membaca atau menyimpan bukti dukung.
- Pencocokan bukti dukung wajib memeriksa `buktidukung_id`, `kode_bukti`, dan fallback resolusi nama berkas `LkeEvidenceHelper::matchesBuktiId()`.
- Tautan aset fisik bukti dukung wajib selalu mengarah ke direktori satker: `asset('uploads/repository/' . $idSatker . '/' . basename($filename))`.

## 2. Kaidah UI/UX Tata Kelola AKIP
- Gunakan komponen `page-hero-card` dengan palet resmi Kejaksaan RI (Emerald gradient dan Gold accent). Hindari header latar mustard (`#e6bf3e`).
- Tampilkan ringkasan metrik KPI (Total Kriteria, Bukti Terpenuhi, Belum Lengkap, Persentase Progres) di bagian atas tabel.
- Jangan gunakan `rowspan` berlebihan yang merusak tampilan responsif; kelompokkan data berdasarkan Komponen dan Subkomponen secara bertingkat.
- Berikan umpan balik interaktif (spinner loading pada tombol, toast notification, dan modal ramah pengguna) tanpa menggunakan dialog `alert()` bawaan peramban.

## 3. Gradasi Nilai Sub Komponen LKE (Koefisien Penilaian)
Gradasi BB/A/AA hanya berlaku jika **seluruh kriteria telah terpenuhi (100%)**. Syarat tambahan untuk tiap gradasi berbeda menurut jenis sub komponen.

### Struktur Kode / Penomoran LKE (sumber: tabel `lke_*`, per `tahun`)
- **Komponen** (`lke_komponen.no`): `1` Perencanaan (bobot 30), `2` Pengukuran (30), `3` Pelaporan (15), `4` Evaluasi AKIP Internal (25). Total 100.
- **Sub Komponen** (`lke_subkomponen.kode` = `{no}.{huruf}`). Huruf menentukan **jenis** sub komponen:
  - `X.A` = **KEBERADAAN**, 20% dari bobot komponen (1.A=6, 2.A=6, 3.A=3, 4.A=5)
  - `X.B` = **KUALITAS**, 30% dari bobot komponen (1.B=9, 2.B=9, 3.B=4.5, 4.B=7.5)
  - `X.C` = **PEMANFAATAN**, 50% dari bobot komponen (1.C=15, 2.C=15, 3.C=7.5, 4.C=12.5)
- **Kriteria** (`lke_kriteria.kode` = `{no}.{huruf}.{urut}`, mis. `1.B.10`). FK `subkomponen_id` berisi kode sub komponen (string, mis. `1.A`). Kriteria punya flag jenjang `eselon_i/kejati/kejari/cabjari`.
- **Parameter** (`lke_parameter`, FK `kriteria_id`): biner, `Terpenuhi` = skor 1, `Tidak Terpenuhi` = skor 0. Tidak ada gradasi di bawah BB per kriteria.
- **Penilaian per kriteria** (`lke_penilaian_satker`): kolom evaluasi (`parameter_id, nilai, skor, catatan, evaluator_id`) dan kolom PM (`pm_*`) disimpan pada baris yang sama.
- **Koefisien per sub komponen** (`lke_subkomponen_koefisien`, unik `id_satker+tahun+subkomponen_kode`): `target_koefisien` (90/100), `nilai` (Terpenuhi/Tidak), `koefisien` efektif (default 80), plus kembaran `pm_*`. Koefisien disimpan sebagai integer persen (80/90/100).
- Jika data `tahun` terpilih belum ada, master LKE fallback ke tahun 2025.

### a. Sub Komponen KEBERADAAN (`X.A`, berbasis konsistensi waktu)
| Nilai | Koefisien | Syarat |
|---|---|---|
| BB | 80% (0.80) | Seluruh kriteria telah terpenuhi (100%) |
| A  | 90% (0.90) | Seluruh kriteria terpenuhi (100%) **dan** telah dipertahankan setidaknya **2 tahun terakhir** |
| AA | 100% (1.00) | Seluruh kriteria terpenuhi (100%) **dan** telah dipertahankan setidaknya **5 tahun terakhir** |

### b. Sub Komponen KUALITAS (`X.B`) dan PEMANFAATAN (`X.C`) (berbasis upaya/inovasi)
| Nilai | Koefisien | Syarat |
|---|---|---|
| BB | 80% (0.80) | Kualitas seluruh kriteria telah terpenuhi (100%) |
| A  | 90% (0.90) | Kualitas seluruh kriteria terpenuhi (100%) **dan** terdapat beberapa upaya yang bisa dihargai dari pemenuhan kriteria tersebut |
| AA | 100% (1.00) | Kualitas seluruh kriteria terpenuhi (100%) **dan** terdapat upaya inovatif serta layak menjadi percontohan di tingkat nasional |

### Kaidah Implementasi
- Bedakan logika gradasi menurut jenis sub komponen. KEBERADAAN memakai durasi pemenuhan (tahun berturut-turut). KUALITAS dan PEMANFAATAN memakai penilaian upaya/inovasi.
- **Gerbang 100% bersifat mutlak:** gradasi/koefisien BB/A/AA TIDAK BOLEH diberikan sebelum seluruh kriteria terpenuhi (100%). Baru setelah 100% terpenuhi, nilai boleh dikalikan dengan koefisien gradasi yang lebih tinggi.
- **Gradasi berjenjang dan eksklusif:** BB → A → AA bersifat berjenjang (A mensyaratkan BB, AA mensyaratkan A). Gradasi adalah pilihan tunggal per sub komponen, tidak boleh memilih lebih dari satu (gunakan radio/select, bukan checkbox).
- Simpan koefisien sebagai konstanta/konfigurasi terpusat (mis. `BB => 0.80, A => 0.90, AA => 1.00`). Jangan menulis angka langsung di banyak tempat.
- Rumus nilai sub komponen (lihat `LkeEvaluasiController::calculateHierarchyScores`): `rasio × (koefisien / 100) × bobot`, dengan `rasio = jumlah kriteria Terpenuhi / jumlah seluruh kriteria sub komponen`. Nilai LKE adalah jumlah nilai 12 sub komponen. Hitung terpisah untuk Evaluasi (`nilai/skor/koefisien`) dan PM (`pm_*`).
- **Contoh perhitungan baku:** Sub komponen `1.A` (bobot 6, total 6 kriteria).
  Terpenuhi: 1.A.1, 1.A.2, 1.A.4, 1.A.6. Tidak terpenuhi: 1.A.3, 1.A.5.
  1. Rasio = 4 / 6 = 0,6667
  2. × koefisien 80% (wajib 80% karena belum 100%) = 0,5333 (persentase 53,3%)
  3. × bobot 6 = **3,20** (skor subkomponen).
  Jika seluruh 6 kriteria terpenuhi (100%): BB (80%) = 4,80 · A (90%) = 5,40 · AA (100%) = 6,00.
- **Mekanisme Gerbang Form Koefisien (Gate & Auto-Reset):**
  - Kotak form koefisien TERKUNCI (disabled) selama kriteria pada subkomponen tersebut belum 100% terpenuhi.
  - Form PM terbuka jika kriteria versi PM 100% terpenuhi; Form Evaluasi terbuka jika kriteria versi Evaluasi 100% terpenuhi.
  - Begitu 100% terpenuhi, form koefisien WAJIB diisi dengan pilihan gradasi: BB (80%), A (90%), atau AA (100%).
  - Jika kriteria yang sebelumnya 100% diubah menjadi "Tidak Terpenuhi" (< 100%), gradasi tersimpan OTOMATIS DIKOSONGKAN / DI-RESET ke default 80%, form dikunci kembali.
  - Jika sudah 100% namun gradasi belum disimpan secara eksplisit: nilai sementara dihitung dengan koefisien default 80% (BB), disertai badge status / peringatan bahwa gradasi wajib dipilih.
  - Validasi isian A/AA: Kotak Uraian / Penjelasan WAJIB diisi, sedangkan Bukti Dukung (link atau unggah berkas) bersifat OPSIONAL.
