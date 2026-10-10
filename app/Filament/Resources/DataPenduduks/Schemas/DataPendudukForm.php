<?php

namespace App\Filament\Resources\DataPenduduks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input data penduduk.
 */
class DataPendudukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Indikator')
                    ->schema([
                        TextInput::make('nama_data')
                            ->label('Nama Data')
                            ->maxLength(255)
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Contoh: Total Penduduk, Laki-laki, Perempuan, RT, RW, Dusun.'),

                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
