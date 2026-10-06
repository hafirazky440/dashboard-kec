<?php

namespace App\Filament\Resources\Sekolahs\Tables;

use App\Models\Sekolah;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel sekolah di halaman admin.
 *
 * Kolom subtotal menjumlahkan jumlah sekolah per jenjang, sehingga admin
 * bisa langsung melihat total jenjang tanpa menghitung sendiri.
 * Nilainya dihitung saat tabel dirender, bukan disimpan.
 */
class SekolahsTable
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

                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state) => $state === 'Negeri' ? 'success' : 'info')
                    ->sortable(),

                TextColumn::make('tahun.tahun')
                    ->label('Tahun')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('subtotal')
                    ->label('Subtotal Jenjang')
                    ->getStateUsing(fn ($record) => Sekolah::query()
                        ->where('tahun_id', $record->tahun_id)
                        ->where('jenjang', $record->jenjang)
                        ->sum('jumlah'))
                    ->numeric()
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
            ])
            ->defaultSort('jenjang')
            ->filters([
                SelectFilter::make('tahun')->label('Tahun')->relationship('tahun', 'tahun'),
                SelectFilter::make('jenis')->label('Jenis')->options([
                    'Negeri' => 'Negeri',
                    'Swasta' => 'Swasta',
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
