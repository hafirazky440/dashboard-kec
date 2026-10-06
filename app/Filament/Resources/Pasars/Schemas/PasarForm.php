<?php

namespace App\Filament\Resources\Pasars\Schemas;

use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input data pasar, bank, dan koperasi per tahun.
 *
 * Kolom jumlah sengaja boleh kosong. Pada sumber, baris Bank dan Koperasi
 * menampilkan satuan KM (1,11 KM dan 0,37 KM) yang nilainya sama dengan
 * Jl. Pajajaran dan Jl. Pasar pada tabel jalan. Indikasinya angka
 * salah tempel, jadi jumlah dibiarkan kosong dan alasannya dicatat di catatan.
 */
class PasarForm
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

                        TextInput::make('nama')
                            ->label('Nama Pasar')
                            ->maxLength(255)
                            ->required()
                            ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
                            ->validationMessages(['unique' => UnikDalamLingkup::pesan()]),
                    ]),

                Section::make('Detail')
                    ->description('Isi hanya bagian yang memang ada pada sumber.')
                    ->schema([
                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Kosongkan bila sumber tidak mencantumkan jumlah.'),

                        TextInput::make('lokasi')
                            ->label('Lokasi')
                            ->maxLength(255)
                            ->helperText('Contoh: Cicalengka Wetan.'),

                        TextInput::make('hari_operasi')
                            ->label('Hari Operasi')
                            ->maxLength(255)
                            ->helperText('Contoh: Selasa dan Kamis.'),
                    ])
                    ->columns(3),

                Section::make('Catatan Verifikasi')
                    ->description('Wajib diisi bila jumlah sengaja dikosongkan karena angka pada sumber meragukan.')
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Tampil di halaman publik sebagai penanda angka yang perlu dicek ulang.'),
                    ]),
            ]);
    }
}
