<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_kecamatan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_kecamatan_id')
                ->constrained('pegawai_kecamatan')
                ->cascadeOnDelete();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('nip', 18)->unique();
            $table->string('eselon', 20);
            $table->string('golongan', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_kecamatan_detail');
    }
};
