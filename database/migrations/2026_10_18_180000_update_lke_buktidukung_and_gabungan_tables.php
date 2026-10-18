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
        // 1. Pastikan tabel lke_buktidukung ada dan memiliki seluruh kolom yang diperlukan
        if (!Schema::hasTable('lke_buktidukung')) {
            Schema::create('lke_buktidukung', function (Blueprint $table) {
                $table->id();
                $table->text('dokumen');
                $table->string('format_nama_file', 255)->nullable();
                $table->text('keterangan')->nullable();
                $table->boolean('ada_di_sistem')->default(false);
                $table->string('tabel_sumber', 100)->nullable()->index();
                $table->unsignedSmallInteger('tahun')->nullable()->index();
            });
        } else {
            Schema::table('lke_buktidukung', function (Blueprint $table) {
                if (!Schema::hasColumn('lke_buktidukung', 'dokumen')) {
                    $table->text('dokumen')->nullable()->after('id');
                }
                if (!Schema::hasColumn('lke_buktidukung', 'format_nama_file')) {
                    $table->string('format_nama_file', 255)->nullable()->after('dokumen');
                }
                if (!Schema::hasColumn('lke_buktidukung', 'keterangan')) {
                    $table->text('keterangan')->nullable()->after('format_nama_file');
                }
                if (!Schema::hasColumn('lke_buktidukung', 'ada_di_sistem')) {
                    $table->boolean('ada_di_sistem')->default(false)->after('keterangan');
                }
                if (!Schema::hasColumn('lke_buktidukung', 'tabel_sumber')) {
                    $table->string('tabel_sumber', 100)->nullable()->index()->after('ada_di_sistem');
                }
                if (!Schema::hasColumn('lke_buktidukung', 'tahun')) {
                    $table->unsignedSmallInteger('tahun')->nullable()->index()->after('tabel_sumber');
                }
            });
        }

        // 2. Pastikan tabel lke_gabungan ada dan memiliki kolom lengkap
        if (!Schema::hasTable('lke_gabungan')) {
            Schema::create('lke_gabungan', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('komponen_id')->nullable()->index();
                $table->string('sub_komponen_id', 255)->index();
                $table->string('kriteria_id', 255)->index();
                $table->integer('buktidukung_id')->default(0)->index();
                $table->unsignedSmallInteger('tahun')->default(2026)->index();
            });
        } else {
            Schema::table('lke_gabungan', function (Blueprint $table) {
                if (!Schema::hasColumn('lke_gabungan', 'komponen_id')) {
                    $table->unsignedBigInteger('komponen_id')->nullable()->after('id')->index();
                }
                if (!Schema::hasColumn('lke_gabungan', 'sub_komponen_id')) {
                    $table->string('sub_komponen_id', 255)->after('komponen_id')->index();
                }
                if (!Schema::hasColumn('lke_gabungan', 'kriteria_id')) {
                    $table->string('kriteria_id', 255)->after('sub_komponen_id')->index();
                }
                if (!Schema::hasColumn('lke_gabungan', 'buktidukung_id')) {
                    $table->integer('buktidukung_id')->default(0)->after('kriteria_id')->index();
                }
                if (!Schema::hasColumn('lke_gabungan', 'tahun')) {
                    $table->unsignedSmallInteger('tahun')->default(2025)->after('buktidukung_id')->index();
                }
            });
        }

        // 3. Pastikan kolom pendukung pada lke_kriteria tersedia
        if (Schema::hasTable('lke_kriteria')) {
            Schema::table('lke_kriteria', function (Blueprint $table) {
                if (!Schema::hasColumn('lke_kriteria', 'langkah_kerja')) {
                    $table->text('langkah_kerja')->nullable()->after('dokumen_bukti');
                }
                if (!Schema::hasColumn('lke_kriteria', 'kode_bukti')) {
                    $table->char('kode_bukti', 50)->nullable()->after('langkah_kerja');
                }
                if (!Schema::hasColumn('lke_kriteria', 'tahun')) {
                    $table->unsignedSmallInteger('tahun')->default(2025)->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opsional rollback tanpa menghapus data operasional
    }
};
