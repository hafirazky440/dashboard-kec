<?php

namespace App\Filament\Resources\Desas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input untuk data Desa.
 *
 * Kolom luas_km2 dan potensi boleh kosong karena pada sumber cetakan tidak
 * setiap desa mencantumkan luas, dan tidak semua desa punya potensi yang
 * tercatat.
 */
class DesaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Desa')
                            ->maxLength(255)
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Nama resmi desa. Contoh: Cicalengka Kulon.'),
                    ]),

                Section::make('Wilayah dan Potensi')
                    ->schema([
                        TextInput::make('luas_km2')
                            ->label('Luas Wilayah (km²)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('km²')
                            ->helperText('Boleh kosong bila luas tidak tercantum pada sumber.'),

                        TextInput::make('potensi')
                            ->label('Potensi')
                            ->maxLength(255)
                            ->helperText('Potensi unggulan desa, misalnya pertanian atau pariwisata. Boleh kosong.'),
                    ])
                    ->columns(2),
            ]);
    }
}
