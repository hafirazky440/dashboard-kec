<?php

namespace App\Filament\Resources\Desas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Formulir input untuk data Desa.
 *
 * Nama desa dipakai sebagai kunci pencocokan di seeder (bukan id angka),
 * jadi menuliskan ulang dengan huruf kapital akan membuat seeder gagal
 * menemukan desa tersebut.
 */
class DesaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Desa')
                    ->maxLength(255)
                    ->required()
                    ->unique(ignoreRecord: true)
                    // onBlur: alive dipanggil sekali saat kursor meninggalkan kolom,
                    // bukan setiap ketikan. Cukup untuk mengisi slug, dan tidak
                    // membebani server.
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $set, callable $get, ?string $state): void {
                        // Jangan menimpa slug yang sudah diisi manual.
                        if (filled($get('slug'))) {
                            return;
                        }

                        $set('slug', str((string) $state)->slug()->toString());
                    })
                    ->helperText('Nama resmi desa. Contoh: Cicalengka Kulon.'),

                TextInput::make('slug')
                    ->label('Slug')
                    ->maxLength(255)
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->regex('/^[a-z0-9\-]+$/')
                    ->helperText('Dipakai pada URL halaman publik. Otomatis terisi dari nama, bisa diubah manual.'),

                TextInput::make('urutan')
                    ->label('Urutan Tampil')
                    ->numeric()
                    ->minValue(1)
                    ->default(99)
                    ->required()
                    ->helperText('Angka kecil tampil lebih dulu. Mengikuti urutan pada PDF sumber.'),
            ]);
    }
}
