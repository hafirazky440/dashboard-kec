<?php

namespace App\Filament\Resources\PegawaiKecamatans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input pegawai kecamatan.
 */
class PegawaiKecamatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas')
                    ->schema([
                        TextInput::make('status')
                            ->label('Status Pegawai')
                            ->maxLength(100)
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: PNS, PPPK, PPPK Paruh Waktu'),
                    ]),

                Section::make('Jumlah')
                    ->description('Isi dengan angka, tanpa titik pemisah. Contoh: 13 berarti 13 orang.')
                    ->schema([
                        TextInput::make('laki_laki')
                            ->label('Laki-laki')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('perempuan')
                            ->label('Perempuan')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
