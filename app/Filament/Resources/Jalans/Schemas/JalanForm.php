<?php

namespace App\Filament\Resources\Jalans\Schemas;

use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Formulir input data jalan per tahun.
 *
 * Catatan tentang dua kolom nullable di sini:
 * - panjang_km dibiarkan kosong. Pada sumber, panjang jalan Andir-Ciseke
 *   tertulis tanda hubung, yang berarti tidak ada data, bukan nol.
 * - batas juga dibiarkan kosong bila sumber tidak menyebutkannya.
 *
 * Jalan tidak punya kolom desa_id di database. Sumber hanya mencatat
 * tingkat jalan dan nama jalannya, jadi memaksakan desa justru menambah
 * data yang tidak ada di sumber.
 */
class JalanForm
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

                        Select::make('tingkat')
                            ->label('Tingkat Jalan')
                            ->options([
                                'Nasional' => 'Nasional',
                                'Provinsi' => 'Provinsi',
                                'Kabupaten' => 'Kabupaten',
                                'Desa' => 'Desa',
                            ])
                            ->required()
                            ->helperText('Menentukan siapa yang bertanggung jawab atas jalan.'),

                        TextInput::make('nama')
                            ->label('Nama Jalan')
                            ->maxLength(255)
                            ->required()
                            // Nama jalan boleh sama antar tahun, dan nama yang sama
                            // pun boleh dipakai tingkat jalan yang berbeda.
                            ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id', 'tingkat'))
                            ->validationMessages(['unique' => UnikDalamLingkup::pesan()])
                            ->helperText('Nama atau jalur sesuai penulisan pada sumber.'),
                    ]),

                Section::make('Detail')
                    ->schema([
                        TextInput::make('panjang_km')
                            ->label('Panjang')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('km')
                            ->helperText('Kosongkan bila panjang tidak diketahui pada sumber.'),

                        TextInput::make('batas')
                            ->label('Batas')
                            ->maxLength(255)
                            ->helperText('Contoh: Bts. Kab. Bandung/Sumedang. Kosongkan bila tidak ada.'),
                    ])
                    ->columns(2),

                Section::make('Catatan Verifikasi')
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Tampil di halaman publik sebagai penanda bahwa angka perlu dicek ulang.'),
                    ]),
            ]);
    }
}
