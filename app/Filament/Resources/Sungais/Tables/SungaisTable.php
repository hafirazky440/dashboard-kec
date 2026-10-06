<?php

namespace App\Filament\Resources\Sungais\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel data sungai di halaman admin.
 */
class SungaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Sungai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

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
                    ->placeholder('Tidak tersedia'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->placeholder('Tidak ada'),
            ])
            ->defaultSort('nama')
            ->filters([
                SelectFilter::make('tahun')->label('Tahun')->relationship('tahun', 'tahun'),
                SelectFilter::make('status')->label('Status')->options([
                    'Kabupaten' => 'Kabupaten',
                    'Provinsi' => 'Provinsi',
                    'Nasional' => 'Nasional',
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
