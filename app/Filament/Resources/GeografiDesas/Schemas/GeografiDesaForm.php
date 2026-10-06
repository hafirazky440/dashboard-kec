<?php

namespace App\Filament\Resources\GeografiDesas\Schemas;

use App\Models\Desa;
use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Formulir input luas wilayah per desa.
 *
 * Hanya desa yang luasnya diketahui dari sumber yang punya baris di sini.
 * Desa lain boleh dikosongkan, dan halaman publik akan menampilkannya
 * sebagai "belum tersedia" bukan sebagai angka nol.
 */
class GeografiDesaForm
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
                    // relationship() mengisi dropdown langsung dari relasi di model,
                    // jadi tidak perlu menulis daftar desa secara manual.
                    ->relationship('desa', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    // Luas wilayah satu desa dicatat sekali per tahun.
                    ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
                    ->validationMessages(['unique' => UnikDalamLingkup::pesan()]),

                TextInput::make('luas_km2')
                    ->label('Luas Wilayah')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix('km2')
                    ->required()
                    ->helperText('Contoh: 10,03 berarti 10,03 km2. Gunakan titik desimal.'),
            ]);
    }
}
