<?php

namespace App\Filament\Resources\ProfilKecamatans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Definisi tabel profil kecamatan di halaman admin.
 */
class ProfilKecamatansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun.tahun')
                    ->label('Tahun')
                    ->badge()
                    ->sortable(),

                TextColumn::make('luas_wilayah_km2')
                    ->label('Luas Wilayah')
                    ->suffix(' km2')
                    ->sortable(),

                TextColumn::make('total_penduduk')
                    ->label('Total Penduduk')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('penduduk_laki_laki')
                    ->label('Laki-laki')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('penduduk_perempuan')
                    ->label('Perempuan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('jumlah_desa')->label('Desa')->numeric()->toggleable(),
                TextColumn::make('jumlah_dusun')->label('Dusun')->numeric()->toggleable(),
                TextColumn::make('jumlah_rw')->label('RW')->numeric()->toggleable(),
                TextColumn::make('jumlah_rt')->label('RT')->numeric()->toggleable(),

                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->wrap()
                    ->limit(60)
                    // Placeholder tampil ketika kolom kosong, bukan disembunyikan,
                    // supaya admin tahu ada field yang bisa diisi.
                    ->placeholder('Tidak ada catatan')
                    ->toggleable(),
            ])
            ->defaultSort('tahun.tahun', 'desc')
            ->filters([
                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->relationship('tahun', 'tahun'),
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
