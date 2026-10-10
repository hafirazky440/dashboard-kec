<?php

namespace App\Filament\Resources\AktaKelahirans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Definisi tabel akta kelahiran per desa di halaman admin.
 */
class AktaKelahiransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('desa')
                    ->label('Desa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

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
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

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

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('desa')
            ->filters([
                //
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
