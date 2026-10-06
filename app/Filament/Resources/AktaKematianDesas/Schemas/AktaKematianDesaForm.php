<?php

namespace App\Filament\Resources\AktaKematianDesas\Schemas;

use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input jumlah akta kematian per desa per tahun.
 *
 * Kolom 'total' sengaja bisa diisi manual, bukan dihitung otomatis, karena
 * sumber cetakan kadang punya ketidaksesuaian dan nilainya perlu
 * ditampilkan apa adanya sambil dicatat, bukan dibetulkan diam-diam.
 */
class AktaKematianDesaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas')
                    ->schema([
                        Select::make('tahun_id')
                            ->label('Tahun')
                            ->options(fn () => Tahun::orderByDesc('tahun')->pluck('tahun', 'id'))
                            ->searchable()
                            ->required(),

                        Select::make('desa_id')
                            ->label('Desa')
                            ->relationship('desa', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            // Satu desa hanya boleh punya satu baris per tahun.
                            ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
                            ->validationMessages(['unique' => UnikDalamLingkup::pesan()]),
                    ]),

                Section::make('Jumlah Akta Kematian')
                    ->description('Isi angka tanpa titik pemisah. Contoh: 195 berarti 195 akta.')
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

                        TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText('Sebaiknya sama dengan Laki-laki + Perempuan.'),
                    ])
                    ->columns(3),
            ]);
    }
}
