<?php

namespace App\Filament\Resources\Sekolahs\Schemas;

use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Formulir input jumlah sekolah per jenjang dan per jenis.
 *
 * Kombinasi jenjang dan jenis harus unik dalam satu tahun. Aturan itu
 * dibuatkan oleh database, dan form ini memvalidasinya lebih dulu supaya
 * admin mendapat pesan error sebelum menyentuh database.
 */
class SekolahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tahun_id')
                    ->label('Tahun')
                    ->options(fn () => Tahun::orderByDesc('tahun')->pluck('tahun', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('jenjang')
                    ->label('Jenjang')
                    ->options([
                        'Kober' => 'Kober',
                        'TK' => 'TK (Taman Kanak-kanak)',
                        'RA' => 'RA (Raudhatul Athfal)',
                        'SD' => 'SD',
                        'MI' => 'MI',
                        'SMP' => 'SMP',
                        'MTs' => 'MTs',
                        'SMA' => 'SMA',
                        'SMK' => 'SMK',
                        'MA' => 'MA',
                        'Perguruan Tinggi' => 'Perguruan Tinggi',
                    ])
                    ->searchable()
                    ->required()
                    ->helperText('Pilih dari daftar atau ketik jenjang lain bila muncul di sumber.'),

                Select::make('jenis')
                    ->label('Jenis Sekolah')
                    ->options([
                        'Negeri' => 'Negeri',
                        'Swasta' => 'Swasta',
                    ])
                    ->required()
                    ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id', 'jenjang'))
                    ->validationMessages(['unique' => UnikDalamLingkup::pesan()])
                    ->helperText('Menentukan kepemilikan sekolah: negeri atau swasta.'),

                TextInput::make('jumlah')
                    ->label('Jumlah Sekolah')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->helperText('Jumlah unit sekolah, bukan jumlah murid.'),
            ]);
    }
}
