<?php

namespace App\Filament\Resources\Jalans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel data jalan di halaman admin.
 */
class JalansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Jalan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->badge()
                    ->sortable(),

                TextColumn::make('tahun.tahun')
                    ->label('Tahun')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('panjang_km')
                    ->label('Panjang')
                    ->suffix(' km')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    // Tanda hubung pada sumber berarti panjang tidak diketahui,
                    // jadi ditampilkan sebagai teks, bukan angka nol.
                    ->placeholder('Tidak tersedia'),

                TextColumn::make('batas')
                    ->label('Batas')
                    ->wrap()
                    ->placeholder('Tidak ada'),

                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->wrap()
                    ->limit(50)
                    ->placeholder('Tidak ada')
                    ->toggleable(),
            ])
            ->defaultSort('tingkat')
            ->filters([
                SelectFilter::make('tahun')->label('Tahun')->relationship('tahun', 'tahun'),
                SelectFilter::make('tingkat')->label('Tingkat')->options([
                    'Nasional' => 'Nasional',
                    'Provinsi' => 'Provinsi',
                    'Kabupaten' => 'Kabupaten',
                    'Desa' => 'Desa',
                ]),
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
