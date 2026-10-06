<?php

namespace App\Filament\Resources\PotensiDesas\Schemas;

use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Formulir input kategori potensi desa per tahun.
 *
 * Tabel ini hanya menyimpan kategori, bukan angka. Ini mengikuti sumbernya:
 * halaman potensi desa mencantumkan dua kolom, nama desa dan jenis potensi,
 * tanpa jumlah atau luas area.
 */
class PotensiDesaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tahun_id')
                    ->label('Tahun')
                    ->options(fn () => Tahun::orderByDesc('tahun')->pluck('tahun', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('desa_id')
                    ->label('Desa')
                    ->relationship('desa', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    // Satu desa hanya boleh punya satu kategori per tahun.
                    ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
                    ->validationMessages(['unique' => UnikDalamLingkup::pesan()])
                    ->helperText('Satu desa hanya boleh punya satu kategori per tahun.'),

                TextInput::make('kategori')
                    ->label('Kategori Potensi')
                    ->maxLength(255)
                    ->required()
                    ->helperText('Contoh: Pertanian dan UMKM, UMKM, Pertanian dan Pariwisata.'),
            ]);
    }
}
