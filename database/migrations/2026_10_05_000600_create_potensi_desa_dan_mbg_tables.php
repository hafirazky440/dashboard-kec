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
        Schema::create('potensi_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->foreignId('desa_id')->constrained('desa')->cascadeOnDelete();
            $table->string('kategori');
            $table->timestamps();

            $table->unique(['tahun_id', 'desa_id']);
        });

        Schema::create('mbg', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->string('jenis', 50);
            $table->unsignedInteger('jumlah');
            $table->string('satuan', 30)->nullable();
            $table->timestamps();

            $table->unique(['tahun_id', 'jenis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mbg');
        Schema::dropIfExists('potensi_desa');
    }
};
