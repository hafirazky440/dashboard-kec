<?php

namespace App\Filament\Resources\Tahuns\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Formulir input untuk Tahun statistik (mis. 2026).
 *
 * Catatan: setiap tabel fakta di database memakai tahun_id sebagai foreign key.
 * Jadi menambah satu baris di sini otomatis menambah satu tahun dashboard
 * tanpa perlu menyentuh kode halaman maupun seeder.
 */
class TahunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tahun')
                    ->label('Tahun')
                    // numeric() memaksa input hanya angka dan mengubah tipe kolom di form.
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(2100)
                    ->required()
                    // ignoreRecord: true agar saat MENGEDIT baris ini, aturan unique
                    // tidak menolak nilai yang memang sama dengan dirinya sendiri.
                    ->unique(ignoreRecord: true)
                    ->helperText('Tahun data statistik, contoh: 2026.'),

                TextInput::make('judul')
                    ->label('Judul Publikasi')
                    ->maxLength(255)
                    ->placeholder('Cicalengka Dalam Angka 2026')
                    ->helperText('Dipakai sebagai judul halaman dan metadata. Boleh dikosongkan.'),
            ]);
    }
}
