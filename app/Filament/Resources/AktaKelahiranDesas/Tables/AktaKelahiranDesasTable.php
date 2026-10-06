<?php

namespace App\Filament\Resources\AktaKelahiranDesas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel akta kelahiran per desa di halaman admin.
 *
 * Kolom persen_memiliki memanggil accessor dari model, sehingga selalu
 * dihitung dari data terbaru tanpa perlu kolom tersendiri di database.
 */
class AktaKelahiranDesasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('desa.nama')
                    ->label('Desa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('tahun.tahun')
                    ->label('Tahun')
                    ->badge()
                    ->sortable(),

                TextColumn::make('wajib_total')
                    ->label('Wajib')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('memiliki_total')
                    ->label('Memiliki')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('belum_total')
                    ->label('Belum Memiliki')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('persen_memiliki')
                    ->label('Persen Memiliki')
                    // Memanggil accessor yang melakukan perhitungan, bukan kolom tersimpan.
                    ->getStateUsing(fn ($record) => $record->persen_memiliki)
                    ->suffix('%')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->wrap()
                    ->limit(50)
                    ->placeholder('Tidak ada')
                    ->toggleable(),

                // Menampilkan selisih antara total tersimpan dan penjumlahan
                // rinciannya. Dua desa punya selisih yang memang ada di sumber
                // cetakan, jadi angka ini sengaja ditampilkan, bukan disembunyikan.
                TextColumn::make('selisih_total')
                    ->label('Selisih')
                    ->getStateUsing(fn ($record) => $record->daftarSelisih()
                        ->filter(fn (?int $nilai): bool => $nilai !== null && $nilai !== 0)
                        ->map(fn (int $nilai, string $prefix): string => $prefix.': '.$nilai)
                        ->values()
                        ->join(', '))
                    ->badge()
                    ->color('danger')
                    ->placeholder('Konsisten')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tahun.tahun', 'desc')
            ->filters([
                SelectFilter::make('tahun')->label('Tahun')->relationship('tahun', 'tahun'),
                SelectFilter::make('desa')->label('Desa')->relationship('desa', 'nama'),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Hapus yang dipilih'),
                ]),
            ]);
    }
}
