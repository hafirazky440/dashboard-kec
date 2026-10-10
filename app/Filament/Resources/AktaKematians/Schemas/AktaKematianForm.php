<?php

namespace App\Filament\Resources\AktaKematians\Schemas;

use App\Rules\TotalHarusSamaDenganRincian;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input data akta kematian per desa.
 *
 * Kolom total dihitung otomatis dari laki-laki dan perempuan, sama seperti
 * akta kelahiran, supaya tidak ada total yang salah ketik.
 */
class AktaKematianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas')
                    ->schema([
                        TextInput::make('desa')
                            ->label('Nama Desa')
                            ->maxLength(255)
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Satu desa hanya boleh punya satu baris akta kematian.'),
                    ]),

                Section::make('Jumlah Kematian')
                    ->schema([
                        TextInput::make('laki_laki')
                            ->label('Laki-laki')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(TotalHarusSamaDenganRincian::setTotal('total', 'laki_laki', 'perempuan')),

                        TextInput::make('perempuan')
                            ->label('Perempuan')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(TotalHarusSamaDenganRincian::setTotal('total', 'laki_laki', 'perempuan')),

                        TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText('Terisi otomatis dari rincian.'),
                    ])
                    ->columns(3),
            ]);
    }
}
