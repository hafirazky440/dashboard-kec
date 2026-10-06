<?php

namespace App\Filament\Resources\AktaKelahiranDesas\Schemas;

use App\Models\Tahun;
use App\Rules\TotalHarusSamaDenganRincian;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input data akta kelahiran per desa per tahun.
 *
 * Data dibagi tiga kelompok: Wajib (semua kelahiran), Memiliki, dan Belum
 * Memiliki. Persentase kepemilikan akta TIDAK disimpan di database, selalu
 * dihitung ulang dari Memiliki dibagi Wajib. Alasannya, sumber cetakan pernah
 * salah ketik pada salah satu desa, dan menyimpan persentase akan membuat
 * angka salah itu ikut terbawa ke seluruh laporan.
 *
 * Karena itu kolom Total di sini tidak diketik manual, melainkan terisi
 * otomatis dari kolom rinciannya. Penyimpangan yang memang ada pada sumber
 * cetakan tetap bisa disimpan, dan ditandai lewat kolom Selisih di tabel
 * beserta kolom Catatan.
 */
class AktaKelahiranDesaForm
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
                            // Satu desa boleh punya satu baris per tahun, jadi
                            // keunikannya dihitung bersama tahun_id.
                            ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
                            ->validationMessages(['unique' => UnikDalamLingkup::pesan()]),
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

                Section::make('Catatan Verifikasi')
                    ->description('Isi bila penjumlahan di atas tidak konsisten dengan sumber.')
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
