<?php

namespace App\Filament\Resources\PegawaiKecamatans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Definisi tabel pegawai kecamatan di halaman admin.
 */
class PegawaiKecamatansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->label('Status Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('laki_laki')
                    ->label('Laki-laki')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('perempuan')
                    ->label('Perempuan')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->getStateUsing(fn ($record) => ($record->laki_laki ?? 0) + ($record->perempuan ?? 0))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('status')
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
