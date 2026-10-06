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
        Schema::create('profil_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->decimal('luas_wilayah_km2', 8, 2)->nullable();
            $table->unsignedSmallInteger('jumlah_desa')->nullable();
            $table->unsignedSmallInteger('jumlah_dusun')->nullable();
            $table->unsignedSmallInteger('jumlah_rw')->nullable();
            $table->unsignedInteger('jumlah_rt')->nullable();
            $table->unsignedInteger('total_penduduk')->nullable();
            $table->unsignedInteger('penduduk_laki_laki')->nullable();
            $table->unsignedInteger('penduduk_perempuan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique('tahun_id');
        });

        Schema::create('pemerintahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->string('jenis', 50);
            $table->unsignedSmallInteger('laki_laki');
            $table->unsignedSmallInteger('perempuan');
            $table->timestamps();

            $table->unique(['tahun_id', 'jenis']);
        });

        Schema::create('geografi_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->foreignId('desa_id')->constrained('desa')->cascadeOnDelete();
            $table->decimal('luas_km2', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['tahun_id', 'desa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geografi_desa');
        Schema::dropIfExists('pemerintahan');
        Schema::dropIfExists('profil_kecamatan');
    }
};
