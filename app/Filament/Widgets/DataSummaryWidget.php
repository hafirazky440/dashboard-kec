<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Desas\DesaResource;
use App\Filament\Resources\Murids\MuridResource;
use App\Filament\Resources\PotensiDesas\PotensiDesaResource;
use App\Filament\Resources\Sekolahs\SekolahResource;
use App\Models\Desa;
use App\Models\Guru;
use App\Models\Jalan;
use App\Models\Murid;
use App\Models\PotensiDesa;
use App\Models\ProfilKecamatan;
use App\Models\Sekolah;
use App\Models\Tahun;
use Filament\Actions\ViewAction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ringkasan data di halaman depan panel admin.
 *
 * Widget ini menggantikan FilamentInfoWidget bawaan yang hanya berisi
 * tautan dokumentasi. Yang ditampilkan di sini adalah angka yang sebenarnya
 * menggambarkan isi database, termasuk berapa banyak catatan yang masih
 * perlu diperiksa karena sumbernya ambigu.
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
        $tahun = Tahun::orderByDesc('tahun')->first();

        $totalDesa = Desa::count();
        $totalPenduduk = (int) ProfilKecamatan::sum('total_penduduk');
        $totalSekolah = (int) Sekolah::sum('jumlah');
        $totalMurid = (int) Murid::sum('jumlah');
        $totalGuru = (int) Guru::sum('jumlah');
        $totalJalan = (int) Jalan::sum('panjang_km');
        $jumlahPotensi = PotensiDesa::count();
        $jumlahCatatan = $this->hitungCatatanPerluPeriksa();

        return [
            Stat::make('Tahun Data', $tahun?->tahun ?? 'Belum ada')
                ->description($tahun?->judul ?? 'Belum ada data tahun')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Jumlah Desa', $totalDesa)
                ->description('Desa di wilayah Kecamatan Cicalengka')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('info')
                ->action(ViewAction::make()->url(DesaResource::getUrl('index'))),

            Stat::make('Total Penduduk', number_format($totalPenduduk, 0, ',', '.'))
                ->description('Laki-laki + perempuan')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Sekolah', number_format($totalSekolah, 0, ',', '.'))
                ->description('Guru tercatat '.number_format($totalGuru, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info')
                ->action(ViewAction::make()->url(SekolahResource::getUrl('index'))),

            Stat::make('Total Murid', number_format($totalMurid, 0, ',', '.'))
                ->description('Sistemik dan Madrasah Ibtidaiyah')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->action(ViewAction::make()->url(MuridResource::getUrl('index'))),

            Stat::make('Panjang Jalan', number_format($totalJalan, 1, ',', '.').' km')
                ->description('Jalan status desa dan setingkat desa')
                ->descriptionIcon('heroicon-m-map')
                ->color('warning'),

            Stat::make('Potensi Desa', $jumlahPotensi)
                ->description('Kategori potensi yang tercatat')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success')
                ->action(ViewAction::make()->url(PotensiDesaResource::getUrl('index'))),

            Stat::make('Catatan Perlu Periksa', $jumlahCatatan)
                ->description('Data tidak lengkap dari sumber PDF')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($jumlahCatatan > 0 ? 'danger' : 'gray'),
        ];
    }

    /**
     * Menghitung baris yang memuat catatan verifikasi.
     *
     * Catatan ini diisi manual dari sumber PDF pada nilai yang ambigu, misalnya
     * panjang jalan yang tidak tercetak atau satuan yang tidak sesuai. Angkanya
     * sengaja ditampilkan supaya admin tahu masih ada pekerjaan pending,
     * bukan disembunyikan supaya dashboard terlihat bersih.
     */
    protected function hitungCatatanPerluPeriksa(): int
    {
        $kolomCatatan = [
            'akta_kelahiran_desa' => 'catatan',
            'akta_kematian_desa' => 'catatan',
            'jalan' => 'catatan',
            'pasar' => 'catatan',
            'profil_kecamatan' => 'catatan',
            'kesehatan' => 'catatan',
        ];

        $total = 0;

        foreach ($kolomCatatan as $tabel => $kolom) {
            if (! Schema::hasColumn($tabel, $kolom)) {
                continue;
            }

            $total += DB::table($tabel)->whereNotNull($kolom)->where($kolom, '!=', '')->count();
        }

        // Baris tanpa nilai sama sekali juga perlu diperiksa, misalnya jalan
        // yang panjangnya tidak tercetak di sumber.
        $total += DB::table('jalan')->whereNull('panjang_km')->count();

        return $total;
    }
}
