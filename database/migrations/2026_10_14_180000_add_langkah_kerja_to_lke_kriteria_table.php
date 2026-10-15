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
        Schema::table('lke_kriteria', function (Blueprint $table) {
            if (!Schema::hasColumn('lke_kriteria', 'langkah_kerja')) {
                $table->text('langkah_kerja')->nullable()->after('dokumen_bukti');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lke_kriteria', function (Blueprint $table) {
            if (Schema::hasColumn('lke_kriteria', 'langkah_kerja')) {
                $table->dropColumn('langkah_kerja');
            }
        });
    }
};
