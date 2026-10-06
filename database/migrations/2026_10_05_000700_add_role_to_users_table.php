<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom role ke tabel users.
 *
 * Nilai role memakai enum UserRole: admin, editor, viewer. Kolom ini nullable
 * hanya supaya migrasi tetap aman pada instalasi lama; setelah migration ini
 * dijalankan, baris mana pun yang masih kosong berarti datanya belum diisi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)
                ->nullable()
                ->after('email')
                ->comment('admin, editor, atau viewer');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
