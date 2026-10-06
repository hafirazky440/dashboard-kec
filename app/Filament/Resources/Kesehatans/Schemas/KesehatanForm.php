<?php

namespace App\Filament\Resources\Kesehatans\Schemas;

use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input data kesehatan per tahun.
 *
 * Tabel ini sengaja masih kosong. Halaman 24 PDF berisi salinan tabel murid,
 * bukan data kesehatan, jadi tidak ada angka yang layak dimasukkan dari sana.
 * Formulirnya tetap dibuat agar admin bisa menambah data secara manual ketika
 * sumber yang benar sudah tersedia.
 */
class KesehatanForm
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

                        Select::make('jenis')
                            ->label('Jenis Sarana Kesehatan')
                            ->options([
                                'Pusat Kesehatan Masyarakat' => 'Pusat Kesehatan Masyarakat',
                                'Pusat Promotif Preventif' => 'Pusat Promotif Preventif',
                                'Rumah Sakit' => 'Rumah Sakit',
                                'Klinik' => 'Klinik',
                                'Apotek' => 'Apotek',
                                'Posyandu' => 'Posyandu',
                            ])
                            ->searchable()
                            ->required()
                            ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
                            ->validationMessages(['unique' => UnikDalamLingkup::pesan()])
                            ->helperText('Pilih dari daftar atau ketik kategori lain bila tersedia di sumber.'),
                    ]),

                Section::make('Detail')
                    ->schema([
                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                    ]),

                Section::make('Catatan Sumber')
                    ->description('Cantumkan asal angka ini, karena sumber resmi untuk kesehatan belum tersedia.')
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Tampil di halaman publik sebagai penanda asal data.'),
                    ]),
            ]);
    }
}
