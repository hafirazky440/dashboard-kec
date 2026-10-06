<?php

namespace App\Filament\Resources\GeografiDesas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel luas wilayah desa di halaman admin.
 */
class GeografiDesasTable
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

                TextColumn::make('luas_km2')
                    ->label('Luas Wilayah')
                    ->suffix(' km2')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
            ])
            ->defaultSort('luas_km2', 'desc')
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
