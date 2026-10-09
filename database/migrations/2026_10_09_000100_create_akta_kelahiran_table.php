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
        Schema::create('akta_kelahiran', function (Blueprint $table) {
            $table->id();
            $table->string('desa');
            $table->unsignedInteger('wajib_laki_laki');
            $table->unsignedInteger('wajib_perempuan');
            $table->unsignedInteger('wajib_total');
            $table->unsignedInteger('memiliki_laki_laki');
            $table->unsignedInteger('memiliki_perempuan');
            $table->unsignedInteger('memiliki_total');
            $table->unsignedInteger('belum_laki_laki');
            $table->unsignedInteger('belum_perempuan');
            $table->unsignedInteger('belum_total');
            $table->decimal('persen_memiliki', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akta_kelahiran');
    }
};
