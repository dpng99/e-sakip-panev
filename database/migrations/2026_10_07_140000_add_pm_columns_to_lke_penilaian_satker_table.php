<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lke_penilaian_satker', function (Blueprint $table) {
            if (!Schema::hasColumn('lke_penilaian_satker', 'pm_parameter_id')) {
                $table->unsignedBigInteger('pm_parameter_id')->nullable()->after('kriteria_id');
            }
            if (!Schema::hasColumn('lke_penilaian_satker', 'pm_nilai')) {
                $table->string('pm_nilai', 50)->nullable()->after('pm_parameter_id');
            }
            if (!Schema::hasColumn('lke_penilaian_satker', 'pm_skor')) {
                $table->double('pm_skor', 8, 2)->nullable()->default(null)->after('pm_nilai');
            }
            if (!Schema::hasColumn('lke_penilaian_satker', 'pm_catatan')) {
                $table->text('pm_catatan')->nullable()->after('pm_skor');
            }
            if (!Schema::hasColumn('lke_penilaian_satker', 'pm_evaluator_id')) {
                $table->string('pm_evaluator_id', 50)->nullable()->after('pm_catatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lke_penilaian_satker', function (Blueprint $table) {
            $columns = ['pm_parameter_id', 'pm_nilai', 'pm_skor', 'pm_catatan', 'pm_evaluator_id'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('lke_penilaian_satker', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
