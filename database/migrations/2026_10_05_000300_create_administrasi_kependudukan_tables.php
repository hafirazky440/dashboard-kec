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
        Schema::create('akta_kematian_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->foreignId('desa_id')->constrained('desa')->cascadeOnDelete();
            $table->unsignedInteger('laki_laki');
            $table->unsignedInteger('perempuan');
            $table->unsignedInteger('total');
            $table->timestamps();

            $table->unique(['tahun_id', 'desa_id']);
        });

        Schema::create('akta_kelahiran_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->foreignId('desa_id')->constrained('desa')->cascadeOnDelete();
            $table->unsignedInteger('wajib_laki_laki');
            $table->unsignedInteger('wajib_perempuan');
            $table->unsignedInteger('wajib_total');
            $table->unsignedInteger('memiliki_laki_laki');
            $table->unsignedInteger('memiliki_perempuan');
            $table->unsignedInteger('memiliki_total');
            $table->unsignedInteger('belum_laki_laki');
            $table->unsignedInteger('belum_perempuan');
            $table->unsignedInteger('belum_total');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['tahun_id', 'desa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akta_kelahiran_desa');
        Schema::dropIfExists('akta_kematian_desa');
    }
};
