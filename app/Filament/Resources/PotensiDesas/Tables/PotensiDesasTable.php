<?php

namespace App\Filament\Resources\PotensiDesas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel potensi desa di halaman admin.
 *
 * Karena sumber tidak memberi angka, tabel ini sengaja tidak punya kolom
 * jumlah. Menambahkan kolom angka di sini berarti mengarang data.
 */
class PotensiDesasTable
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

                TextColumn::make('kategori')
                    ->label('Kategori Potensi')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->wrap(),

                TextColumn::make('tahun.tahun')
                    ->label('Tahun')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
            ])
            ->defaultSort('desa.nama')
            ->filters([
                SelectFilter::make('tahun')->label('Tahun')->relationship('tahun', 'tahun'),
                SelectFilter::make('desa')->label('Desa')->relationship('desa', 'nama'),
                SelectFilter::make('kategori')->label('Kategori')->options([
                    'Pertanian dan UMKM' => 'Pertanian dan UMKM',
                    'UMKM' => 'UMKM',
                    'Pertanian' => 'Pertanian',
                    'Pertanian dan Pariwisata' => 'Pertanian dan Pariwisata',
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
