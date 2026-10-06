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
        Schema::create('jalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->string('tingkat', 30);
            $table->string('nama');
            $table->decimal('panjang_km', 8, 2)->nullable();
            $table->string('batas')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['tahun_id', 'tingkat', 'nama'], 'jalan_tahun_tingkat_nama_unique');
        });

        Schema::create('sungai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->string('nama');
            $table->decimal('panjang_km', 8, 2)->nullable();
            $table->string('status', 30)->nullable();
            $table->timestamps();

            $table->unique(['tahun_id', 'nama']);
        });

        Schema::create('pasar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->string('nama');
            $table->unsignedInteger('jumlah')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('hari_operasi')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['tahun_id', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasar');
        Schema::dropIfExists('sungai');
        Schema::dropIfExists('jalan');
    }
};
