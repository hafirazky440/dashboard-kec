<?php

namespace App\Filament\Resources\RuasJalans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input ruas jalan.
 */
class RuasJalanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Ruas Jalan')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Jalan')
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('status')
                            ->label('Status')
                            ->maxLength(100)
                            ->helperText('Contoh: Jalan Desa, Jalan Kabupaten.'),

                        TextInput::make('panjang_km')
                            ->label('Panjang (km)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('km')
                            ->helperText('Boleh kosong bila panjang tidak tercantum pada sumber.'),
                    ])
                    ->columns(3),
            ]);
    }
}
