<?php

namespace App\Filament\Resources\Kesehatans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel kesehatan di halaman admin.
 */
class KesehatansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenis')
                    ->label('Jenis Sarana Kesehatan')
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
                    ->sortable(),

                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->wrap()
                    ->limit(60)
                    ->placeholder('Tidak ada')
                    ->toggleable(),
            ])
            ->defaultSort('jenis')
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
            ])
            ->emptyStateHeading('Belum ada data kesehatan')
            ->emptyStateDescription(
                'Halaman 24 pada PDF sumber berisi salinan tabel murid, bukan data kesehatan. '
                .'Tabel ini dibiarkan kosong sampai ada sumber yang benar.',
            );
    }
}
