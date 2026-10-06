<?php

namespace App\Filament\Resources\Pemerintahans\Schemas;

use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input data pegawai pemerintahan per tahun.
 *
 * Field 'jenis' menyimpan kategori seperti PNS, PPPK, atau PPPK Paruh Waktu.
 * Daftar kategori diambil bebas dari isian admin, sehingga kategori baru
 * bisa ditambahkan tanpa mengubah kode.
 */
class PemerintahanForm
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

                        TextInput::make('jenis')
                            ->label('Jenis Pegawai')
                            ->maxLength(50)
                            ->required()
                            // Kategori boleh sama antar tahun, jadi keunikannya
                            // dihitung bersama tahun_id.
                            ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
                            ->validationMessages(['unique' => UnikDalamLingkup::pesan()])
                            ->placeholder('Contoh: PNS, PPPK, PPPK Paruh Waktu'),
                    ]),

                Section::make('Jumlah')
                    ->description('Isi dengan angka, tanpa titik pemisah. Contoh: 13 berarti 13 orang.')
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
                    ])
                    ->columns(2),
            ]);
    }
}
