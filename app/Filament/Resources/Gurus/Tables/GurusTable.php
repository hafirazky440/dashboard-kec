<?php

namespace App\Filament\Resources\Gurus\Tables;

use App\Models\Guru;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel guru di halaman admin.
 *
 * Kolom total menjumlahkan seluruh kategori guru pada tahun yang sama,
 * sehingga admin tidak perlu menghitung manual.
 */
class GurusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenis')
                    ->label('Jenis Guru')
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

                TextColumn::make('total_tahun')
                    ->label('Total per Tahun')
                    ->getStateUsing(fn ($record) => Guru::query()
                        ->where('tahun_id', $record->tahun_id)
                        ->sum('jumlah'))
                    ->numeric()
                    ->badge()
                    ->color('primary')
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
            ]);
    }
}
