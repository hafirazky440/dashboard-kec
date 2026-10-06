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
                TextColumn::make('urutan')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('nama')
                    ->label('Nama Desa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('aktaKelahiranDesa_count')
                    ->label('Data Akta Kelahiran')
                    ->counts('aktaKelahiranDesa')
                    ->badge()
                    ->color('gray')
                    // Kolom ini hanya informatif, tidak bisa difilter.
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Mengurutkan berdasarkan urutan_tampil, bukan id.
            ->defaultSort('urutan')
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
