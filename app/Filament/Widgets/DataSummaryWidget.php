<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Desas\DesaResource;
use App\Filament\Resources\Murids\MuridResource;
use App\Filament\Resources\Sekolahs\SekolahResource;
use App\Models\DataPenduduk;
use App\Models\Desa;
use App\Models\Guru;
use App\Models\Mbg;
use App\Models\Murid;
use App\Models\RuasJalan;
use App\Models\Sekolah;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ringkasan data di halaman depan panel admin.
 *
 * Widget ini menggantikan FilamentInfoWidget bawaan yang hanya berisi
 * tautan dokumentasi. Yang ditampilkan di sini adalah angka yang sebenarnya
 * menggambarkan isi database.
 *
 * Semua angka diambil dari database, bukan ditulis manual, supaya admin
 * langsung melihat kondisi data terkini tanpa harus membuka tiap tabel.
 */
class DataSummaryWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = true;

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $totalPenduduk = $this->nilaiPenduduk('Total Penduduk');
        $pendudukLk = $this->nilaiPenduduk('Laki-laki');
        $pendudukPr = $this->nilaiPenduduk('Perempuan');
        $totalSekolah = (int) Sekolah::sum('jumlah');
        $totalMurid = (int) Murid::sum('jumlah');
        $totalGuru = (int) Guru::sum('jumlah');
        $totalPanjangJalan = (float) RuasJalan::sum('panjang_km');
        $totalMbg = (int) Mbg::sum('jumlah');
        $jumlahPerluPeriksa = $this->hitungPerluPeriksa();

        return [
            Stat::make('Jumlah Desa', Desa::count())
                ->description('Desa di wilayah Kecamatan Cicalengka')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('info')
                ->url(DesaResource::getUrl('index')),

            Stat::make('Total Penduduk', $totalPenduduk !== null
                ? number_format($totalPenduduk, 0, ',', '.')
                : 'Tidak tersedia')
                ->description($pendudukLk !== null && $pendudukPr !== null
                    ? 'Laki-laki '.number_format($pendudukLk, 0, ',', '.').' · Perempuan '.number_format($pendudukPr, 0, ',', '.')
                    : 'Jumlah laki-laki dan perempuan')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Sekolah', number_format($totalSekolah, 0, ',', '.'))
                ->description('Guru tercatat '.number_format($totalGuru, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info')
                ->url(SekolahResource::getUrl('index')),

            Stat::make('Total Murid', number_format($totalMurid, 0, ',', '.'))
                ->description('Seluruh jenjang pendidikan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->url(MuridResource::getUrl('index')),

            Stat::make('Panjang Jalan', number_format($totalPanjangJalan, 1, ',', '.').' km')
                ->description('Seluruh ruas jalan tercatat')
                ->descriptionIcon('heroicon-m-map')
                ->color('warning'),

            Stat::make('Program MBG', number_format($totalMbg, 0, ',', '.'))
                ->description('Akumulasi jumlah pada tabel MBG')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),

            Stat::make('Perlu Diperiksa', $jumlahPerluPeriksa)
                ->description('Baris yang kolom angkanya kosong pada sumber')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($jumlahPerluPeriksa > 0 ? 'danger' : 'gray'),
        ];
    }

    /**
     * Membaca satu indikator dari tabel data_penduduk.
     */
    protected function nilaiPenduduk(string $nama): ?int
    {
        $nilai = DataPenduduk::where('nama_data', $nama)->value('jumlah');

        return $nilai === null ? null : (int) $nilai;
    }

    /**
     * Menghitung baris yang angka wajibnya tidak terisi pada sumber cetakan.
     *
     * Nilai ini sengaja ditampilkan supaya admin tahu masih ada pekerjaan
     * pending, bukan disembunyikan supaya dashboard terlihat bersih.
     */
    protected function hitungPerluPeriksa(): int
    {
        $jumlah = 0;

        // Hanya tabel yang kolomnya wajib terisi pada sumber yang dihitung.
        // Kolom desa.luas_km2 sengaja dibiarkan bebas karena tidak setiap
        // desa mencantumkan luas wilayahnya pada cetakan.
        foreach ([
            ['ruas_jalan', 'panjang_km'],
            ['pengairan', 'panjang_km'],
        ] as [$tabel, $kolom]) {
            if (! Schema::hasColumn($tabel, $kolom)) {
                continue;
            }

            $jumlah += (int) DB::table($tabel)->whereNull($kolom)->count();
        }

        return $jumlah;
    }
}
