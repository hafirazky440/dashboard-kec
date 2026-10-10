<?php

namespace App\Filament\Resources\AktaKelahirans\Schemas;

use App\Rules\TotalHarusSamaDenganRincian;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input data akta kelahiran per desa.
 *
 * Data dibagi tiga kelompok: Wajib (semua kelahiran), Memiliki, dan Belum
 * Memiliki. Persentase kepemilikan akta disimpan sebagai kolom tersendiri
 * (persen_memiliki), bukan dihitung ulang, karena sudah menjadi bagian dari
 * tabel hasil impor CSV.
 *
 * Karena itu kolom Total di sini tidak diketik manual, melainkan terisi
 * otomatis dari kolom rinciannya. Penyimpangan yang memang ada pada sumber
 * cetakan tetap bisa disimpan, dan ditandai lewat kolom Selisih di tabel
 * beserta Catatan Verifikasi pada dashboard publik.
 */
class AktaKelahiranForm
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
                            ->helperText('Satu desa hanya boleh punya satu baris akta kelahiran.'),
                    ]),

                Section::make('Wajib Akta Kelahiran')
                    ->description('Jumlah seluruh kelahiran yang wajib terdaftar akta di desa ini.')
                    ->schema([
                        TextInput::make('wajib_laki_laki')->label('Laki-laki')->numeric()->minValue(0)->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(TotalHarusSamaDenganRincian::setTotal('wajib_total', 'wajib_laki_laki', 'wajib_perempuan')),
                        TextInput::make('wajib_perempuan')->label('Perempuan')->numeric()->minValue(0)->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(TotalHarusSamaDenganRincian::setTotal('wajib_total', 'wajib_laki_laki', 'wajib_perempuan')),
                        TextInput::make('wajib_total')->label('Total')->numeric()->minValue(0)->required()
                            ->helperText('Terisi otomatis dari rincian.'),
                    ])
                    ->columns(3),

                Section::make('Memiliki Akta Kelahiran')
                    ->description('Bagian dari data wajib yang sudah terbit akta.')
                    ->schema([
                        TextInput::make('memiliki_laki_laki')->label('Laki-laki')->numeric()->minValue(0)->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(TotalHarusSamaDenganRincian::setTotal('memiliki_total', 'memiliki_laki_laki', 'memiliki_perempuan')),
                        TextInput::make('memiliki_perempuan')->label('Perempuan')->numeric()->minValue(0)->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(TotalHarusSamaDenganRincian::setTotal('memiliki_total', 'memiliki_laki_laki', 'memiliki_perempuan')),
                        TextInput::make('memiliki_total')->label('Total')->numeric()->minValue(0)->required()
                            ->helperText('Terisi otomatis dari rincian.'),
                    ])
                    ->columns(3),

                Section::make('Belum Memiliki Akta Kelahiran')
                    ->description('Sisa dari data wajib yang akta-nya belum terbit.')
                    ->schema([
                        TextInput::make('belum_laki_laki')->label('Laki-laki')->numeric()->minValue(0)->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(TotalHarusSamaDenganRincian::setTotal('belum_total', 'belum_laki_laki', 'belum_perempuan')),
                        TextInput::make('belum_perempuan')->label('Perempuan')->numeric()->minValue(0)->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(TotalHarusSamaDenganRincian::setTotal('belum_total', 'belum_laki_laki', 'belum_perempuan')),
                        TextInput::make('belum_total')->label('Total')->numeric()->minValue(0)->required()
                            ->helperText('Terisi otomatis dari rincian.'),
                    ])
                    ->columns(3),

                Section::make('Persentase')
                    ->description('Persentase kelahiran yang sudah memiliki akta, sesuai angka tercetak pada sumber.')
                    ->schema([
                        TextInput::make('persen_memiliki')
                            ->label('Persen Memiliki Akta')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }
}
