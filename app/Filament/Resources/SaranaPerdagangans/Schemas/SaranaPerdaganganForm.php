<?php

namespace App\Filament\Resources\SaranaPerdagangans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input sarana perdagangan.
 */
class SaranaPerdaganganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Sarana Perdagangan')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama')
                            ->maxLength(255)
                            ->required(),

                        TextInput::make('jenis')
                            ->label('Jenis')
                            ->maxLength(100)
                            ->helperText('Misalnya: Pasar Tradisional, Pasar Swalayan.'),

                        TextInput::make('lokasi')
                            ->label('Lokasi')
                            ->maxLength(255),
                    ])
                    ->columns(3),
            ]);
    }
}
