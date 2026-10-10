<?php

namespace App\Filament\Resources\Gurus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input data guru.
 */
class GuruForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Guru')
                    ->schema([
                        TextInput::make('jenis')
                            ->label('Jenis Sekolah')
                            ->maxLength(255)
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Contoh: Sekolah Negeri, Sekolah Swasta.'),

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
