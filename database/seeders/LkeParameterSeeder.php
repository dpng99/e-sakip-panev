<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LkeParameter;
use App\Models\LkePenilaianSatker;

class LkeParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan kolom id AUTO_INCREMENT jika didukung MySQL
        try {
            DB::statement('ALTER TABLE lke_parameter MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        } catch (\Throwable $e) {
            // Abaikan jika database bukan MySQL atau permission terbatas
        }

        // 2. Hapus seeder/data lama pada tabel lke_parameter
        DB::table('lke_parameter')->delete();

        try {
            DB::statement('ALTER TABLE lke_parameter AUTO_INCREMENT = 1');
        } catch (\Throwable $e) {
            // Abaikan jika database bukan MySQL
        }

        // 3. Data master kriteria dan deskripsi parameter
        $criteriaParams = [
            '1.A.1' => [
                'terpenuhi'       => 'Pedoman 4 Tahun 2024 TELAH disosialisasikan/didistribusikan kepada seluruh pegawai',
                'tidak_terpenuhi' => 'Pedoman 4 Tahun 2024 BELUM disosialisasikan/didistribusikan kepada seluruh pegawai',
            ],
            '1.A.2' => [
                'terpenuhi'       => 'Apabila terdapat dokumen RENSTRA Satuan Kerja',
                'tidak_terpenuhi' => 'Apabila tidak tersedia dokumen RENSTRA Satuan Kerja',
            ],
            '1.A.3' => [
                'terpenuhi'       => 'Apabila terdapat dokumen RENJA Satuan Kerja',
                'tidak_terpenuhi' => 'Apabila tidak tersedia dokumen RENJA Satuan Kerja',
            ],
            '1.A.4' => [
                'terpenuhi'       => 'Apabila terdapat dokumen rencana aksi kinerja Satuan Kerja',
                'tidak_terpenuhi' => 'Apabila tidak tersedia dokumen rencana aksi kinerja Satuan Kerja',
            ],
            '1.A.5' => [
                'terpenuhi'       => '2 dokumen tersebut tersedia',
                'tidak_terpenuhi' => 'Apabila tidak tersedia dokumen',
            ],
            '1.A.6' => [
                'terpenuhi'       => 'Apabila terdapat dokumen Perjanjian Kinerja untuk SELURUH Pejabat Struktural',
                'tidak_terpenuhi' => 'Apabila tidak tersedia dokumen Perjanjian Kinerja',
            ],
            '1.B.1' => [
                'terpenuhi'       => 'apabila 4 dokumen tersebut telah ditetapkan dengan Keputusan atau ditandatangani oleh Kepala Satuan Kerja',
                'tidak_terpenuhi' => 'apabila hanya 3 dokumen tersebut telah ditetapkan dengan Keputusan atau ditandatangani oleh Kepala Satuan Kerja',
            ],
            '1.B.2' => [
                'terpenuhi'       => 'Jika 3 dokumen perencanaan tersebut telah dipublikasikan tepat waktu',
                'tidak_terpenuhi' => 'Kriteria belum terpenuhi: Dokumen Perencanaan telah dipublikasikan tepat waktu',
            ],
            '1.B.3' => [
                'terpenuhi'       => 'Jika terpenuhi 4 kriteria',
                'tidak_terpenuhi' => 'Jika terpenuhi 3 kriteria',
            ],
            '1.B.4' => [
                'terpenuhi'       => 'Jika seluruh Sasaran Strategis, Sasaran Program, dan Sasaran Kegiatan pada Satuan Kerja telah sesuai dengan Cascading Kinerja',
                'tidak_terpenuhi' => 'Jika terdapat Sasaran Strategis, Sasaran Program, dan Sasaran Kegiatan pada Satuan Kerja tidak sesuai dengan Cascading Kinerja',
            ],
            '1.B.5' => [
                'terpenuhi'       => 'Jika seluruh target kinerja yang ditetapkan oleh satuan kerja lebih besar atau sama dengan target kinerja nasional yang ditetapkan dalam Rencana Strategis Kejaksaan RI.',
                'tidak_terpenuhi' => 'Jika terdapat target kinerja yang ditetapkan oleh satuan kerja lebih kecil dari target kinerja nasional yang ditetapkan dalam Rencana Strategis Kejaksaan RI.',
            ],
            '1.B.6' => [
                'terpenuhi'       => 'Indikator Kinerja Utama (IKU) telah menggambarkan kondisi kinerja utama secara berkesinambungan (sustainable)',
                'tidak_terpenuhi' => 'Indikator Kinerja Utama (IKU) belum menggambarkan kondisi kinerja utama secara sustainable / terdapat revisi yang tidak sesuai',
            ],
            '1.B.7' => [
                'terpenuhi'       => 'Terdapat Peningkatan Target per Tahun pada Periode Renstra (5 Tahun) yang menantang secara realistis',
                'tidak_terpenuhi' => 'Terdapat Peningkatan Target per Tahun pada Renstra (5 Tahun) yang menantang, tetapi tidak realistis',
            ],
            '1.B.8' => [
                'terpenuhi'       => 'Seluruh Dokumen RENSTRA, IKU, dan Perjanjian Kinerja Satuan Kerja telah selaras dan menggambarkan hubungan sebab akibat (Cascading)',
                'tidak_terpenuhi' => 'Terdapat Dokumen RENSTRA, IKU, dan Perjanjian Kinerja Satuan Kerja yang belum selaras dan menggambarkan hubungan sebab akibat (Cascading)',
            ],
            '1.B.9' => [
                'terpenuhi'       => 'Perencanaan Kinerja telah memberikan informasi keterkaitan lintas bidang/tusi (Crosscutting)',
                'tidak_terpenuhi' => 'Crosscutting belum tergambar pada dokumen perencanaan kinerja satuan kerja',
            ],
            '1.B.10' => [
                'terpenuhi'       => 'Seluruh pegawai satuan kerja telah merumuskan dan menetapkan perencanaan kinerja (SKP 100%)',
                'tidak_terpenuhi' => 'Masih terdapat pegawai yang belum menyusun atau menetapkan SKP perencanaan kinerja',
            ],
            '1.C.1' => [
                'terpenuhi'       => 'Anggaran dan Target pada RENJA telah selaras Dengan DIPA',
                'tidak_terpenuhi' => 'Anggaran dan Target pada RENJA belum seluruhnya Selaras Dengan DIPA',
            ],
            '1.C.2' => [
                'terpenuhi'       => 'Seluruh Rencana Aksi memiliki korelasi (hubungan sebab akibat) dengan IKU',
                'tidak_terpenuhi' => 'Terdapat Rencana Aksi yang tidak memiliki korelasi (hubungan sebab akibat) dengan IKU',
            ],
            '1.C.3' => [
                'terpenuhi'       => 'Seluruh indikator yang diperjanjikan telah tercapai sesuai target/melebihi target (data pada Laporan Kinerja Satuan Kerja)',
                'tidak_terpenuhi' => 'indikator yang diperjanjikan seluruhnya tidak mencapai target yang telah diperjanjikan (data pada Laporan Kinerja Satuan Kerja)',
            ],
            '1.C.4' => [
                'terpenuhi'       => 'Jika rencana aksi kinerja dipantau/dilaporkan pada Rapat Staf EKA Triwulan I dan II Tahun 2025',
                'tidak_terpenuhi' => 'Jika rencana aksi kinerja dipantau/dilaporkan pada Rapat Staf EKA hanya pada salah satu triwulan',
            ],
            '1.C.5' => [
                'terpenuhi'       => 'Penetapan target kinerja telah disusun sebagai bentuk penyempurnaan perencanaan kinerja yang ditetapkan dari hasil analisis capaian kinerja/ tren capaian kinerja periode-periode sebelumnya, beban kerja dan ketersediaan anggaran pada masing-masing satuan kerja',
                'tidak_terpenuhi' => 'Penetapan target kinerja yang disusun belum sepenuhnya mempertimbangkan hasil analisis capaian kinerja/ tren capaian kinerja periode-periode sebelumnya, beban kerja dan ketersediaan anggaran pada masing-masing satuan kerja',
            ],
            '1.C.6' => [
                'terpenuhi'       => 'Penetapan target kinerja telah disusun sebagai bentuk penyempurnaan perencanaan kinerja yang ditetapkan dari hasil analisis capaian kinerja/ tren capaian kinerja periode-periode sebelumnya, beban kerja dan ketersediaan anggaran pada masing-masing satuan kerja',
                'tidak_terpenuhi' => 'Tidak terdapat dokumen Penetapan Target Kinerja Satuan Kerja',
            ],
            '1.C.7' => [
                'terpenuhi'       => '1. Satuan Kerja menyusun Laporan Kinerja sesuai dengan Perjanjian Kinerja yang telah ditetapkan setiap triwulan. 2. Satuan Kerja melakukan evaluasi terhadap capaian kinerja setiap triwulan salah satunya melalui Rapat Staf Evaluasi Kinerja dan Anggaran.',
                'tidak_terpenuhi' => '1. Satuan Kerja menyusun Laporan Kinerja sesuai dengan Perjanjian Kinerja yang telah ditetapkan setiap semester. 2. Satuan Kerja melakukan evaluasi terhadap capaian kinerja setiap semester salah satunya melalui Rapat Staf Evaluasi Kinerja dan Anggaran.',
            ],
            '1.C.8' => [
                'terpenuhi'       => 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami kinerja yang telah direncanakan dan memiliki komitmen untuk mencapai sasaran kinerja tersebut',
                'tidak_terpenuhi' => 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai TIDAK memahami kinerja yang telah direncanakan dan memiliki komitmen untuk mencapai sasaran kinerja tersebut',
            ],
            '2.A.1' => [
                'terpenuhi'       => 'Pedoman 4 Tahun 2024 TELAH disosialisasikan/didistribusikan kepada seluruh pegawai',
                'tidak_terpenuhi' => 'Pedoman 4 Tahun 2024 BELUM disosialisasikan/didistribusikan kepada seluruh pegawai',
            ],
            '2.A.2' => [
                'terpenuhi'       => 'Jika TELAH tersedia dokumen penetapan target kinerja satuan kerja tahun 2025 yang memuat definisi operasional yang jelas',
                'tidak_terpenuhi' => 'Jika BELUM tersedia dokumen penetapan target kinerja satuan kerja tahun 2025',
            ],
            '2.A.3' => [
                'terpenuhi'       => 'ika TELAH tersedia Memorandum Kepala Satuan Kerja perihal pengumpulan data kinerja yang dapat diandalkan',
                'tidak_terpenuhi' => 'Jika BELUM tersedia Memorandum Kepala Satuan Kerja perihal pengumpulan data kinerja yang dapat diandalkan',
            ],
            '2.B.1' => [
                'terpenuhi'       => 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat pembahasan terkait hasil pengukuran capaian kinerja',
                'tidak_terpenuhi' => 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat pembahasan terkait hasil pengukuran capaian kinerja',
            ],
            '2.B.2' => [
                'terpenuhi'       => 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya sesuai dengan Penetapan Target Kinerja',
                'tidak_terpenuhi' => 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya tidak sesuai dengan Penetapan Target Kinerja',
            ],
            '2.B.3' => [
                'terpenuhi'       => 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya sesuai dengan Penetapan Target Kinerja',
                'tidak_terpenuhi' => 'Data kinerja pada nota dinas penyampaian data kinerja setiap triwulan seluruhnya tidak sesuai dengan Penetapan Target Kinerja',
            ],
            '2.B.4' => [
                'terpenuhi'       => 'Jika pengukuran telah dilakukan dan dituangkan dalam Laporan Kinerja triwulan I dan II Tahun 2025',
                'tidak_terpenuhi' => 'Jika pengukuran TIDAK dilakukan dan dituangkan dalam Laporan Kinerja',
            ],
            '2.B.5' => [
                'terpenuhi'       => 'Jika TERDAPAT Nota dinas penyampaian data kinerja dari seluruh bidang untuk menghitung capaian kinerja kepala satuan kerja',
                'tidak_terpenuhi' => 'Jika TIDAK terdapat Nota dinas penyampaian data kinerja dari seluruh bidang untuk menghitung capaian kinerja kepala satuan kerja',
            ],
            '2.B.6' => [
                'terpenuhi'       => 'Satuan Kerja telah melakukan penginputan data kinerja ke dalam aplikasi (SICANA/aplikasi kinerja) secara tertib',
                'tidak_terpenuhi' => 'Satuan Kerja belum melakukan penginputan data kinerja ke dalam aplikasi kinerja secara tertib',
            ],
            '2.B.7' => [
                'terpenuhi'       => 'Hasil pengukuran pada apllikasi SICANA sama dengan hasil pengukuran yang dituangkan pada laporan kinerja',
                'tidak_terpenuhi' => 'Hasil pengukuran pada apllikasi SICANA tidak sama dengan hasil pengukuran yang dituangkan pada laporan kinerja',
            ],
            '2.C.1' => [
                'terpenuhi'       => 'Satuan kerja telah memberikan apresiasi/ penghargaan DAN pembinaan/punishment secara internal kepada satuan kerja/unit kerja dibawahnya berdasarkan pengukuran kinerja yang telah dilaksanakan',
                'tidak_terpenuhi' => 'Satuan kerja TIDAK memberikan apresiasi/ penghargaan maupun pembinaan/punishment secara internal kepada satuan kerja/unit kerja dibawahnya berdasarkan pengukuran kinerja yang telah dilaksanakan',
            ],
            '2.C.2' => [
                'terpenuhi'       => 'Jika TERDAPAT sampel dokumen Rekomendasi Kenaikan Pangkat/Promosi/Mutasi yang dilengkapi dengan SKP dan clearence',
                'tidak_terpenuhi' => 'Jika TIDAK terdapat sampel dokumen Rekomendasi Kenaikan Pangkat/Promosi/Mutasi yang dilengkapi dengan SKP dan clearence',
            ],
            '2.C.3' => [
                'terpenuhi'       => 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat uraian target, realisasi, dan strategi optimalisasi capaian kinerja pada periode selanjutnya dalam rangka pemenuhan target kinerja',
                'tidak_terpenuhi' => 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat uraian target, realisasi, dan strategi optimalisasi capaian kinerja pada periode selanjutnya dalam rangka pemenuhan target kinerja',
            ],
            '2.C.4' => [
                'terpenuhi'       => 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan',
                'tidak_terpenuhi' => 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan',
            ],
            '2.C.5' => [
                'terpenuhi'       => 'Jika Laporan Rapat Staf Triwulan I dan II TELAH memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan',
                'tidak_terpenuhi' => 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan pengukuran kinerja yang telah dilakukan',
            ],
            '2.C.6' => [
                'terpenuhi'       => 'Rata-rata capaian Indikator Kinerja Sasaran Strategis ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â°Ãƒâ€šÃ‚Â¥ 100% DAN realisasi anggaran ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â°Ãƒâ€šÃ‚Â¥95%',
                'tidak_terpenuhi' => 'Rata-rata capaian Indikator Kinerja Sasaran Strategis TIDAK mencapai 100%',
            ],
            '2.C.7' => [
                'terpenuhi'       => 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami dan peduli atas hasil pengukuran kinerja',
                'tidak_terpenuhi' => 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai TIDAK memahami dan peduli atas hasil pengukuran kinerja',
            ],
            '2.C.8' => [
                'terpenuhi'       => 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami dan peduli atas hasil pengukuran kinerja',
                'tidak_terpenuhi' => 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai TIDAK memahami dan peduli atas hasil pengukuran kinerja',
            ],
            '3.A.1' => [
                'terpenuhi'       => 'Satuan Kerja Telah Menyusun LKJIP Triwulan I dan II Tahun 2025',
                'tidak_terpenuhi' => 'Satuan Kerja TIDAK Menyusun LKJIP Triwulan I dan II Tahun 2025',
            ],
            '3.A.2' => [
                'terpenuhi'       => 'Jika telah disusun Laporan Kinerja triwulan I dan II Tahun 2025',
                'tidak_terpenuhi' => 'Jika TIDAK dilakukan penyusunan Laporan Kinerja',
            ],
            '3.A.3' => [
                'terpenuhi'       => 'Jika Laporan Kinerja triwulan I dan II Tahun 2025 telah ditandatangani oleh Kepala Satuan Kerja',
                'tidak_terpenuhi' => 'Jika Laporan Kinerja TIDAK ditandatangani oleh Kepala Satuan Kerja',
            ],
            '3.A.4' => [
                'terpenuhi'       => 'Apabila laporan kinerja satuan kerja triwulan IV /Laporan kinerja tahunan telah direviu APIP pada Bidang Pengawasan',
                'tidak_terpenuhi' => 'Apabila laporan kinerja satuan kerja triwulan IV /Laporan kinerja tahunan tidak direviu APIP pada Bidang Pengawasan',
            ],
            '3.A.5' => [
                'terpenuhi'       => 'Apabila Laporan Kinerja telah dipublikasi melalui website masing-masing.',
                'tidak_terpenuhi' => 'Apabila Laporan Kinerja belum dipublikasi melalui website masing-masing satuan kerja',
            ],
            '3.A.6' => [
                'terpenuhi'       => 'Bahwa seluruh laporan kinerja telah disampaikan secara tepat waktu (Sesuai dengan Pedoman 4 Tahun 2024 bahwa Laporan Kinerja dilaporkan atau diupload melalui ESR dan SICANA paling lambat 30 Hari setelah triwulan berakhir)',
                'tidak_terpenuhi' => 'Apabila terdapat laporan kinerja yang dilaporkan/diupload tidak tepat waktu',
            ],
            '3.B.1' => [
                'terpenuhi'       => 'Apabila seluruh laporan kinerja telah disusun sesuai standar yang terdiri dari BAB I - BAB IV sesuai dengan pedoman nomor 4 tahun 2024 yang telah sesuai dengan dokumen Perjanjian Kinerja Kepala Satuan Kerja',
                'tidak_terpenuhi' => 'Apabila laporan kinerja telah disusun sesuai standar yang terdiri dari BAB I - BAB IV sesuai dengan pedoman nomor 4 tahun 2024 namun terdapat beberapa indikator yang dilaporkan dalam Laporan Kinerja tidak sesuai dengan dokumen Perjanjian Kinerja Kepala Satuan Kerja',
            ],
            '3.B.2' => [
                'terpenuhi'       => 'Apabila Laporan Kinerja telah memuat capaian kinerja dari seluruh indikator pada Perjanjian Kinerja (sesuai dengan format yang telah diberikan dari Kejaksaan Agung) indikator dalam PK lengkap',
                'tidak_terpenuhi' => 'Apabila indikator yang disajikan dalam Laporan Kinerja tidak ada',
            ],
            '3.B.3' => [
                'terpenuhi'       => 'Apabila terdapat perbandingan realisasi kinerja dengan target kinerja tahunan untuk seluruh indikator kinerja',
                'tidak_terpenuhi' => 'Tidak terdapat perbandingan realisasi kinerja dengan target kinerja tahunan',
            ],
            '3.B.4' => [
                'terpenuhi'       => 'Apabila pada laporan triwulan II terdapat perbandingan realisasi kinerja dengan kinerja triwulan sebelumnya (mencakup seluruh indikator)',
                'tidak_terpenuhi' => 'tidak terdapat perbandingan kinerja dengan triwulan sebelumnya',
            ],
            '3.B.5' => [
                'terpenuhi'       => 'apabila pada laporan tahunan terdapat perbandingan realisasi kinerja dengan kinerja tahun-tahun sebelumnya (mencakup seluruh indikator)',
                'tidak_terpenuhi' => 'tidak terdapat perbandingan kinerja dengan tahun sebelumnya',
            ],
            '3.B.6' => [
                'terpenuhi'       => 'apabila seluruh indikator pada Laporan Kinerja telah memuat analisa faktor-faktor yang mempengaruhi keberhasilan, dan hambatan yang dihadapi serta upaya nyata yang telah dilakukan',
                'tidak_terpenuhi' => 'terdapat laporan kinerja namun tidak terinformasikan hambatan atau upaya nyata untuk mencapai target kinerja',
            ],
            '3.B.7' => [
                'terpenuhi'       => 'apabila dalam laporan kinerja telah diinformasikan tentang efisiensi sumber daya baik sumber daya manusia maupun anggaran dalam mencapai kinerja.',
                'tidak_terpenuhi' => 'apabila dalam laporan kinerja belum menginformasikan tentang efisiensi sumber daya baik sumber daya manusia maupun anggaran dalam mencapai kinerja',
            ],
            '3.B.8' => [
                'terpenuhi'       => 'Apabila Laporan Kinerja telah memuat strategi peningkatan capaian kinerja untuk periode selanjutnya pada seluruh indikator',
                'tidak_terpenuhi' => 'Apabila tidak terdapat informasi upaya perbaikan dan penyempurnaan kinerja untuk periode selanjutnya pada Laporan Kinerja',
            ],
            '3.C.1' => [
                'terpenuhi'       => 'Jika Laporan Rapat Staf Triwulan I dan II TELAH yang memuat pembahasan laporan kinerja serta dinamika kelompok untuk pencapaian target kinerja',
                'tidak_terpenuhi' => 'Jika Laporan Rapat Staf Triwulan I dan II TIDAK yang memuat pembahasan laporan kinerja serta dinamika kelompok untuk pencapaian target kinerja',
            ],
            '3.C.2' => [
                'terpenuhi'       => 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai SUDAH memahami pegawai informasi di dalam laporan kinerja',
                'tidak_terpenuhi' => 'Hasil observasi dan atau wawancara menunjukkan bahwa pegawai BELUM memahami pegawai informasi di dalam laporan kinerja',
            ],
            '3.C.3' => [
                'terpenuhi'       => 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TELAH memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja',
                'tidak_terpenuhi' => 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TIDAK memuat penyesuaian pada kegiatan (bisa dalam bentuk perubahan jadwal kegiatan untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja',
            ],
            '3.C.4' => [
                'terpenuhi'       => 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TELAH memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja)',
                'tidak_terpenuhi' => 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TIDAK memuat optimalisasi anggaran kegiatan (untuk periode selanjutnya) berdasarkan informasi dalam laporan kinerja',
            ],
            '3.C.5' => [
                'terpenuhi'       => 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TELAH memuat evaluasi pencapaian keberhasilan kinerja',
                'tidak_terpenuhi' => 'Jika Laporan Kinerja Satuan Kerja Triwulan I dan II Tahun 2025 TIDAK memuat evaluasi pencapaian keberhasilan kinerja',
            ],
            '3.C.6' => [
                'terpenuhi'       => 'Penetapan target kinerja TELAH disusun sebagai bentuk penyesuaian perencanaan kinerja berdasarkan informasi yang terdapat pada laporan kinerja periode sebelumnya',
                'tidak_terpenuhi' => 'Penetapan target kinerja BELUM disusun sebagai bentuk penyesuaian perencanaan kinerja berdasarkan informasi yang terdapat pada laporan kinerja periode sebelumnya',
            ],
            '3.C.7' => [
                'terpenuhi'       => 'Hasil observasi dan atau wawancara menunjukkan bahwa SUDAH terpenuhi/terjadiperubahan budaya kinerja organisasi yang berpedoman pada laporan kinerja',
                'tidak_terpenuhi' => 'Hasil observasi dan atau wawancara menunjukkan bahwa BELUM terpenuhi/terjadi perubahan budaya kinerja organisasi yang berpedoman pada laporan kinerja',
            ],
            '4.A.1' => [
                'terpenuhi'       => 'Pedoman 4 Tahun 2025 TELAH disosialisasikan/didistribusikan kepada seluruh pegawai',
                'tidak_terpenuhi' => 'Pedoman 4 Tahun 2025 BELUM disosialisasikan/didistribusikan kepada seluruh pegawai',
            ],
            '4.A.2' => [
                'terpenuhi'       => 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
                'tidak_terpenuhi' => 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
            ],
            '4.A.3' => [
                'terpenuhi'       => 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
                'tidak_terpenuhi' => 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
            ],
            '4.B.1' => [
                'terpenuhi'       => 'Jika TERDAPAT surat penyampaian PM AKIP Internal berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
                'tidak_terpenuhi' => 'Jika TIDAK terdapat Surat penyampaian PM AKIP Internal berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
            ],
            '4.B.2' => [
                'terpenuhi'       => 'Tersedia notulensi dan dokumentasi atas pelaksanaan sosialisasi/bimbingan teknis/pengarahan terkait SAKIP kepada kepala satuan kerja',
                'tidak_terpenuhi' => 'SDM Tim Penilai Mandiri telah mengikuti sosialisasi/bimbingan teknis/pengarahan terkait Sistem Akuntabilitas Kinerja Instansi Pemerintah',
            ],
            '4.B.3' => [
                'terpenuhi'       => 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
                'tidak_terpenuhi' => 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
            ],
            '4.B.4' => [
                'terpenuhi'       => 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
                'tidak_terpenuhi' => 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
            ],
            '4.B.5' => [
                'terpenuhi'       => 'Jika TERDAPAT surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
                'tidak_terpenuhi' => 'Jika TIDAK terdapat Surat penyampaian PM AKIP INTERNAL berserta LKE kepada Jaksa Agung Muda Bidang Pengawasan',
            ],
            '4.C.1' => [
                'terpenuhi'       => 'Seluruh rekomendasi hasil evaluasi AKIP internal tahun sebelumnya telah ditindaklanjuti (100%)',
                'tidak_terpenuhi' => 'Rekomendasi hasil evaluasi AKIP internal tahun sebelumnya belum seluruhnya ditindaklanjuti',
            ],
            '4.C.2' => [
                'terpenuhi'       => 'Jika tidak ada hasil evaluasi dan rekomendasi berulang pada LHE T-1 dibandingkan LHE T-2',
                'tidak_terpenuhi' => 'Jika masih TERDAPAT hasil evaluasi dan rekomendasi berulang pada LHE T-1 dibandingkan LHE T-2',
            ],
            '4.C.3' => [
                'terpenuhi'       => 'Nilai LHE meningkat dari tahun sebelumnya',
                'tidak_terpenuhi' => 'Nilai LHE menurun dari tahun sebelumnya',
            ],
            '4.C.4' => [
                'terpenuhi'       => 'Jika telah dilakukan MONITORING SECARA PERIODIK atas capaian rencana aksi tindak lanjut hasil evaluasi dan rekomendasi AKIP',
                'tidak_terpenuhi' => 'Jika TIDAK terdapat dokumen Rencana Aksi tindak lanjut pemenuhan evaluasi dan rekomendasi AKIP',
            ],
            '4.C.5' => [
                'terpenuhi'       => 'Telah terjadi perbaikan dan peningkatan capaian Indikator Kinerja Sasaran Strategis (IKSS)',
                'tidak_terpenuhi' => 'Belum terjadi perbaikan atau peningkatan capaian Indikator Kinerja Sasaran Strategis (IKSS)',
            ],
            '4.C.6' => [
                'terpenuhi'       => 'Jika hasil dari evaluasi dan rekomendasi pada LHE T-1 TELAH menjadi Rencana Aksi Kinerja tahun berjalan',
                'tidak_terpenuhi' => 'Jika hasil dari evaluasi dan rekomendasi pada LHE T-1 BELUM menjadi Rencana Aksi Kinerja tahun berjalan',
            ],
        ];

        // 4. Tahun yang didukung (2025 dan 2026)
        $years = [2025, 2026];
        $insertData = [];
        $now = now();
        $id = 1;

        foreach ($years as $tahun) {
            foreach ($criteriaParams as $kriteriaId => $param) {
                // Opsi Terpenuhi: Skor 1
                $insertData[] = [
                    'id'          => $id++,
                    'kriteria_id' => $kriteriaId,
                    'nilai'       => 'Terpenuhi',
                    'skor'        => 1.00,
                    'keterangan'  => $param['terpenuhi'],
                    'tahun'       => $tahun,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];

                // Opsi Tidak Terpenuhi: Skor 0
                $insertData[] = [
                    'id'          => $id++,
                    'kriteria_id' => $kriteriaId,
                    'nilai'       => 'Tidak Terpenuhi',
                    'skor'        => 0.00,
                    'keterangan'  => $param['tidak_terpenuhi'],
                    'tahun'       => $tahun,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        // Chunk insert untuk performa optimal
        foreach (array_chunk($insertData, 100) as $chunk) {
            DB::table('lke_parameter')->insert($chunk);
        }

        // 5. Sinkronisasi data riwayat penilaian yang sudah ada di lke_penilaian_satker (jika ada)
        $existingPenilaians = LkePenilaianSatker::all();
        foreach ($existingPenilaians as $p) {
            $isTerpenuhi = ($p->skor > 0 || strtoupper($p->nilai) === 'YA' || $p->nilai === 'Terpenuhi');
            $targetNilai = $isTerpenuhi ? 'Terpenuhi' : 'Tidak Terpenuhi';
            $targetSkor = $isTerpenuhi ? 1.00 : 0.00;

            $matchedParam = LkeParameter::where('kriteria_id', $p->kriteria_id)
                ->where('tahun', $p->tahun)
                ->where('nilai', $targetNilai)
                ->first();

            if ($matchedParam) {
                $p->update([
                    'parameter_id' => $matchedParam->id,
                    'nilai'        => $targetNilai,
                    'skor'         => $targetSkor,
                ]);
            }
        }
    }
}
