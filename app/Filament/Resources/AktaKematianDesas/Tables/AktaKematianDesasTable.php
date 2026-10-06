<?php

namespace App\Filament\Resources\AktaKematianDesas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel akta kematian per desa di halaman admin.
 *
 * Kolom selisih dihitung saat render untuk membantu admin cepat menemukan
 * baris yang L + P tidak sama dengan Total.
 */
class AktaKematianDesasTable
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

                TextColumn::make('laki_laki')->label('Laki-laki')->numeric()->sortable(),
                TextColumn::make('perempuan')->label('Perempuan')->numeric()->sortable(),
                TextColumn::make('total')->label('Total')->numeric()->sortable()->weight('bold'),

                TextColumn::make('selisih')
                    ->label('Cek L+P')
                    ->getStateUsing(fn ($record) => $record->laki_laki + $record->perempuan === $record->total
                        ? 'Sesuai'
                        : 'Tidak sesuai')
                    ->badge()
                    ->color(fn ($record) => $record->laki_laki + $record->perempuan === $record->total
                        ? 'success'
                        : 'danger')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('total', 'desc')
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
