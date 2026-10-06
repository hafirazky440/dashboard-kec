<?php

namespace App\Filament\Resources\ProfilKecamatans\Schemas;

use App\Models\Tahun;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input untuk Profil Kecamatan (satu baris per tahun).
 *
 * Kolom 'catatan' dipakai untuk menandai angka yang meragukan dari sumber,
 * supaya angka tetap tampil apa adanya tanpa pernah dikoreksi diam-diam.
 * Lihat docs/data-verifikasi-pdf.md untuk daftar lengkapnya.
 */
class ProfilKecamatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Periode')
                    ->description('Setiap tahun hanya boleh punya satu baris profil.')
                    ->schema([
                        Select::make('tahun_id')
                            ->label('Tahun')
                            ->options(fn () => Tahun::orderByDesc('tahun')->pluck('tahun', 'id'))
                            ->searchable()
                            ->required()
                            // Mencegah admin membuat tahun ganda karena ada
                            // unique constraint di level database.
                            ->unique(ignoreRecord: true),
                    ]),

                Section::make('Wilayah')
                    ->schema([
                        TextInput::make('luas_wilayah_km2')
                            ->label('Luas Wilayah (km2)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('km2'),

                        TextInput::make('jumlah_desa')->label('Jumlah Desa')->numeric()->minValue(0),
                        TextInput::make('jumlah_dusun')->label('Jumlah Dusun')->numeric()->minValue(0),
                        TextInput::make('jumlah_rw')->label('Jumlah RW')->numeric()->minValue(0),
                        TextInput::make('jumlah_rt')->label('Jumlah RT')->numeric()->minValue(0),
                    ])
                    ->columns(3),

                Section::make('Penduduk')
                    ->schema([
                        TextInput::make('total_penduduk')->label('Total Penduduk')->numeric()->minValue(0),
                        TextInput::make('penduduk_laki_laki')->label('Laki-laki')->numeric()->minValue(0),
                        TextInput::make('penduduk_perempuan')->label('Perempuan')->numeric()->minValue(0),
                    ])
                    ->columns(3),

                Section::make('Catatan Verifikasi')
                    ->description('Isi bila angka di atas perlu dikonfirmasi ke sumber. Tampil di halaman publik sebagai penanda.')
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
