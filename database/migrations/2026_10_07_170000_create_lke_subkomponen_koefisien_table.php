<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add bobot to lke_komponen if not exists
        if (!Schema::hasColumn('lke_komponen', 'bobot')) {
            Schema::table('lke_komponen', function (Blueprint $table) {
                $table->decimal('bobot', 5, 2)->default(0)->after('nama');
            });
        }

        // 2. Add bobot to lke_subkomponen if not exists
        if (!Schema::hasColumn('lke_subkomponen', 'bobot')) {
            Schema::table('lke_subkomponen', function (Blueprint $table) {
                $table->decimal('bobot', 5, 2)->default(0)->after('nama');
            });
        }

        // 3. Populate standard SAKIP weights for Komponen
        $komponenBobot = [
            1 => 30.00, // Perencanaan Kinerja
            2 => 30.00, // Pengukuran Kinerja
            3 => 15.00, // Pelaporan Kinerja
            4 => 25.00, // Evaluasi Akuntabilitas Kinerja Internal
        ];
        foreach ($komponenBobot as $no => $bbt) {
            DB::table('lke_komponen')->where('no', $no)->update(['bobot' => $bbt]);
        }

        // 4. Populate standard SAKIP weights for the 12 Subkomponen
        $subkomponenBobot = [
            '1.A' => 6.00,
            '1.B' => 9.00,
            '1.C' => 15.00,
            '2.A' => 6.00,
            '2.B' => 9.00,
            '2.C' => 15.00,
            '3.A' => 3.00,
            '3.B' => 4.50,
            '3.C' => 7.50,
            '4.A' => 5.00,
            '4.B' => 7.50,
            '4.C' => 12.50,
        ];
        foreach ($subkomponenBobot as $kode => $bbt) {
            DB::table('lke_subkomponen')->where('kode', $kode)->update(['bobot' => $bbt]);
        }

        // 5. Create lke_subkomponen_koefisien table
        if (!Schema::hasTable('lke_subkomponen_koefisien')) {
            Schema::create('lke_subkomponen_koefisien', function (Blueprint $table) {
                $table->id();
                $table->string('id_satker', 50)->index();
                $table->integer('tahun')->index();
                $table->string('subkomponen_kode', 20)->index();

                // Tahap Evaluasi (JAMWAS / Kejati)
                $table->integer('target_koefisien')->default(90); // 90 atau 100
                $table->text('uraian')->nullable();
                $table->string('bukti_dukung')->nullable();
                $table->string('link_bukti_dukung')->nullable();
                $table->string('nilai', 50)->nullable(); // 'Terpenuhi' atau 'Tidak Terpenuhi'
                $table->integer('koefisien')->default(80); // 80 jika Tidak Terpenuhi/null, 90 atau 100 jika Terpenuhi
                $table->string('evaluator_id', 50)->nullable();

                // Tahap Penilaian Mandiri (PM Satker)
                $table->integer('pm_target_koefisien')->default(90); // 90 atau 100
                $table->text('pm_uraian')->nullable();
                $table->string('pm_bukti_dukung')->nullable();
                $table->string('pm_link_bukti_dukung')->nullable();
                $table->string('pm_nilai', 50)->nullable(); // 'Terpenuhi' atau 'Tidak Terpenuhi'
                $table->integer('pm_koefisien')->default(80); // 80 jika Tidak Terpenuhi/null, 90 atau 100 jika Terpenuhi
                $table->string('pm_evaluator_id', 50)->nullable();

                $table->timestamps();

                $table->unique(['id_satker', 'tahun', 'subkomponen_kode'], 'satker_tahun_subkomp_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lke_subkomponen_koefisien');

        if (Schema::hasColumn('lke_subkomponen', 'bobot')) {
            Schema::table('lke_subkomponen', function (Blueprint $table) {
                $table->dropColumn('bobot');
            });
        }

        if (Schema::hasColumn('lke_komponen', 'bobot')) {
            Schema::table('lke_komponen', function (Blueprint $table) {
                $table->dropColumn('bobot');
            });
        }
    }
};
