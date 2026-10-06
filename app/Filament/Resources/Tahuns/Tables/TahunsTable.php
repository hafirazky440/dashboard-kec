<?php

namespace App\Filament\Resources\Tahuns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Definisi tabel daftar tahun di halaman admin.
 *
 * Kolom jumlah dihitung on-the-fly lewat ->counts(), bukan kolom tersimpan,
 * jadi angkanya selalu akurat walau tabel fakta sudah ditambah atau dihapus.
 * Karena itu labelnya menyebut tabel yang dihitung, bukan sekadar "Jumlah Data".
 */
class TahunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('judul')
                    ->label('Judul Publikasi')
                    ->searchable()
                    ->wrap()
                    ->placeholder('Tidak diisi')
                    // Sembunyikan kolom dari tampilan tabel tapi tetap bisa dicari.
                    ->toggleable(isToggledHiddenByDefault: true),

                // counts() menjumlahkan baris anak lewat relasi hasMany di model Tahun.
                TextColumn::make('jumlah_data')
                    ->label('Desa (Akta Kelahiran)')
                    ->counts('aktaKelahiranDesa')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('jumlah_sekolah')
                    ->label('Sekolah')
                    ->counts('sekolah')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('jumlah_murid')
                    ->label('Murid')
                    ->counts('murid')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Tahun terbaru di atas.
            ->defaultSort('tahun', 'desc')
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
