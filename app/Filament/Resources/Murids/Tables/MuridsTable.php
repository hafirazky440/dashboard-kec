<?php

namespace App\Filament\Resources\Murids\Tables;

use App\Models\Murid;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel murid di halaman admin.
 */
class MuridsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenjang')
                    ->label('Jenjang')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('tahun.tahun')
                    ->label('Tahun')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah Murid')
                    ->numeric()
                    ->sortable(),

                // Persentase dihitung dari total murid pada tahun yang sama,
                // sehingga tidak pernah perlu disimpan di database.
                TextColumn::make('persentase')
                    ->label('Porsi Murid')
                    ->getStateUsing(function ($record): ?float {
                        $total = Murid::query()->where('tahun_id', $record->tahun_id)->sum('jumlah');

                        return $total > 0 ? round($record->jumlah / $total * 100, 2) : null;
                    })
                    ->suffix('%')
                    ->numeric(decimalPlaces: 2)
                    ->toggleable(),

                TextColumn::make('total_tahun')
                    ->label('Total Murid per Tahun')
                    ->getStateUsing(fn ($record) => Murid::query()
                        ->where('tahun_id', $record->tahun_id)
                        ->sum('jumlah'))
                    ->numeric()
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
            ])
            ->defaultSort('jumlah', 'desc')
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
