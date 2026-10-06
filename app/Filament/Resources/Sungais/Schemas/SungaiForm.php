<?php

namespace App\Filament\Resources\Sungais\Schemas;

use App\Models\Tahun;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Formulir input data sungai per tahun.
 *
 * Hanya ada satu kolom ukuran, yaitu panjang_km. Pada sumber, dua sungai
 * tidak punya angka panjang sehingga dibiarkan kosong. Sungai tidak punya
 * kolom desa_id maupun catatan di database karena sumber tidak menyebutkannya.
 */
class SungaiForm
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

                TextInput::make('nama')
                    ->label('Nama Sungai')
                    ->maxLength(255)
                    ->required()
                    ->helperText('Contoh: Sungai Citarik.'),

                TextInput::make('panjang_km')
                    ->label('Panjang')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix('km')
                    ->helperText('Kosongkan bila panjang tidak diketahui pada sumber.'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Kabupaten' => 'Kabupaten',
                        'Provinsi' => 'Provinsi',
                        'Nasional' => 'Nasional',
                    ])
                    ->searchable()
                    ->placeholder('Pilih status bila diketahui'),
            ]);
    }
}
