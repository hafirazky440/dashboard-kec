<?php

namespace App\Filament\Resources\Kecamatans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input profil Kecamatan.
 */
class KecamatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Kecamatan')
                            ->maxLength(255)
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Nama resmi kecamatan. Contoh: Cicalengka.'),
                    ]),

                Section::make('Wilayah')
                    ->schema([
                        TextInput::make('luas_km2')
                            ->label('Luas Wilayah (km²)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('km²')
                            ->required(),

                        TextInput::make('persen_pemilik_ktp')
                            ->label('Persentase Pemilik KTP')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
