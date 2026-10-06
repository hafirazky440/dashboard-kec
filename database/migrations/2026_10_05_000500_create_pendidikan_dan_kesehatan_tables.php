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
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->string('jenjang', 40);
            $table->string('jenis', 20);
            $table->unsignedInteger('jumlah');
            $table->timestamps();

            $table->unique(['tahun_id', 'jenjang', 'jenis']);
        });

        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->string('jenis', 30);
            $table->unsignedInteger('jumlah');
            $table->timestamps();

            $table->unique(['tahun_id', 'jenis']);
        });

        Schema::create('murid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->string('jenjang', 40);
            $table->unsignedInteger('jumlah');
            $table->timestamps();

            $table->unique(['tahun_id', 'jenjang']);
        });

        Schema::create('kesehatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->cascadeOnDelete();
            $table->string('jenis', 40);
            $table->unsignedInteger('jumlah');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['tahun_id', 'jenis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kesehatan');
        Schema::dropIfExists('murid');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('sekolah');
    }
};
