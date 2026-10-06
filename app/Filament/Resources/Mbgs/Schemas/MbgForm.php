<?php

namespace App\Filament\Resources\Mbgs\Schemas;

use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Formulir input data Makan Bergizi (MBG) per tahun.
 *
 * Kolom satuan wajib diisi karena jenis datanya berbeda: dapurdihitung dalam
 * unit dapur, penerima manfaat dalam orang. Tanpa satuan, angka 21 dan 46192
 * akan sama-sama terlihat seperti angka biasa sehingga mudah salah baca.
 */
class MbgForm
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

                Select::make('jenis')
                    ->label('Jenis Data')
                    ->options([
                        'Dapur Operasional' => 'Dapur Operasional',
                        'Dapur SPPG Siap Operasional' => 'Dapur SPPG Siap Operasional',
                        'Dapur SPPG Siap Persiapan' => 'Dapur SPPG Siap Persiapan',
                        'Total Penerima Manfaat' => 'Total Penerima Manfaat',
                    ])
                    ->searchable()
                    ->required()
                    ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
                    ->validationMessages(['unique' => UnikDalamLingkup::pesan()])
                    ->helperText('Pilih dari daftar atau ketik jenis lain bila muncul di sumber.'),

                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Select::make('satuan')
                    ->label('Satuan')
                    ->options([
                        'dapur' => 'dapur',
                        'orang' => 'orang',
                        'penerima' => 'penerima',
                        'menu' => 'menu',
                    ])
                    ->searchable()
                    ->required()
                    ->helperText('Sesuaikan dengan jenis datanya. Contoh: dapur atau orang.'),
            ]);
    }
}
