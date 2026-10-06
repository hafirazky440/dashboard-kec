<?php

namespace App\Filament\Resources\Pemerintahans\Tables;

use App\Models\Pemerintahan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel data pemerintahan di halaman admin.
 *
 * Kolom 'total' dihitung dari laki_laki + perempuan, bukan kolom tersimpan,
 * sehingga tidak mungkin tidak sinkron dengan rinciannya.
 */
class PemerintahansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('tahun.tahun')
                    ->label('Tahun')
                    ->badge()
                    ->sortable(),

                TextColumn::make('laki_laki')->label('Laki-laki')->numeric()->sortable(),
                TextColumn::make('perempuan')->label('Perempuan')->numeric()->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->getStateUsing(fn ($record) => $record->total())
                    ->numeric()
                    ->weight('bold')
                    ->badge()
                    ->color('primary'),
            ])
            ->defaultSort('jenis')
            ->filters([
                SelectFilter::make('tahun')->label('Tahun')->relationship('tahun', 'tahun'),
                SelectFilter::make('jenis')->label('Jenis')->options(fn () => Pemerintahan::query()
                    ->distinct()
                    ->pluck('jenis', 'jenis')
                    ->all()),
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
