<?php

namespace App\Filament\Resources\Pengairans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input jaringan pengairan.
 */
class PengairanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pengairan')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama')
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('panjang_km')
                            ->label('Panjang (km)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('km')
                            ->helperText('Boleh kosong bila panjang tidak tercantum pada sumber.'),

                        TextInput::make('kewenangan')
                            ->label('Kewenangan')
                            ->maxLength(100)
                            ->helperText('Misalnya: Pusat, Provinsi, Kabupaten.'),
                    ])
                    ->columns(3),
            ]);
    }
}
