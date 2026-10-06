<?php

namespace App\Filament\Resources\Gurus\Schemas;

use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Formulir input jumlah guru per tahun.
 *
 * Kolom jenis menyimpan kategori guru, misalnya Sekolah Negeri dan Sekolah
 * Swasta. Tidak ada kolom desa_id karena sumber hanya memuat angka
 * guru tingkat kecamatan, bukan per desa.
 */
class GuruForm
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
                    ->label('Jenis Guru')
                    ->options([
                        'Sekolah Negeri' => 'Sekolah Negeri',
                        'Sekolah Swasta' => 'Sekolah Swasta',
                    ])
                    ->searchable()
                    ->required()
                    ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
                    ->validationMessages(['unique' => UnikDalamLingkup::pesan()])
                    ->helperText('Pilih dari daftar atau ketik kategori lain bila muncul di sumber.'),

                TextInput::make('jumlah')
                    ->label('Jumlah Guru')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->helperText('Jumlah guru, bukan jumlah murid.'),
            ]);
    }
}
