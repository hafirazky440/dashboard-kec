<?php

namespace App\Filament\Resources\Desas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Definisi tabel daftar desa di halaman admin.
 */
class DesasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Desa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('luas_km2')
                    ->label('Luas Wilayah')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' km²')
                    ->sortable()
                    ->placeholder('tidak tersedia')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('potensi')
                    ->label('Potensi')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('tidak tercatat')
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nama')
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
