<?php

namespace App\Filament\Resources\Pasars\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel pasar, bank, dan koperasi di halaman admin.
 */
class PasarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('tahun.tahun')
                    ->label('Tahun')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable()
                    // Jumlah yang kosong berarti sumber tidak memberi angka,
                    // bukan berarti nol. Placeholder menjaga perbedaan ini.
                    ->placeholder('Tidak tersedia'),

                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable()
                    ->placeholder('Tidak dicantumkan')
                    ->toggleable(),

                TextColumn::make('hari_operasi')
                    ->label('Hari Operasi')
                    ->placeholder('Tidak dicantumkan')
                    ->toggleable(),

                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->wrap()
                    ->limit(60)
                    ->placeholder('Tidak ada')
                    ->toggleable(),
            ])
            ->defaultSort('nama')
            ->filters([
                SelectFilter::make('tahun')->label('Tahun')->relationship('tahun', 'tahun'),
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
