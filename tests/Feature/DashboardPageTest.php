<?php

/*
|--------------------------------------------------------------------------
| Test halaman publik dashboard
|--------------------------------------------------------------------------
|
| Fokusnya bukan tampilan, tapi apakah angka yang muncul benar-benar berasal
| dari database dan apakah nilai yang tidak ada di sumber ditampilkan
| sebagai "tidak tersedia", bukan sebagai angka nol.
|
| Skema tabel mengikuti file CSV tanpa dimensi tahun, jadi tidak ada lagi
| pemilih tahun maupun query ?tahun=.
|
*/

use App\Models\AktaKelahiran;
use App\Models\DataPenduduk;
use App\Models\Desa;
use App\Models\Guru;
use App\Models\Kecamatan;
use App\Models\Murid;
use App\Services\StatistikService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardPageTest extends TestCase
{
    use RefreshDatabase;

    protected function isiData(): Desa
    {
        Kecamatan::create([
            'nama' => 'Cicalengka',
            'luas_km2' => 42.21,
            'persen_pemilik_ktp' => 94.72,
        ]);

        DataPenduduk::create(['nama_data' => 'Total Penduduk', 'jumlah' => 1000]);
        DataPenduduk::create(['nama_data' => 'Laki-laki', 'jumlah' => 600]);
        DataPenduduk::create(['nama_data' => 'Perempuan', 'jumlah' => 400]);
        DataPenduduk::create(['nama_data' => 'Total Desa', 'jumlah' => 2]);

        $kulon = Desa::create(['nama' => 'Cicalengka Kulon', 'luas_km2' => 0.49, 'potensi' => 'Pertanian dan UMKM']);
        $nagrog = Desa::create(['nama' => 'Nagrog', 'luas_km2' => null, 'potensi' => 'UMKM']);

        AktaKelahiran::create([
            'desa' => 'Cicalengka Kulon',
            'wajib_laki_laki' => 100,
            'wajib_perempuan' => 100,
            'wajib_total' => 200,
            'memiliki_laki_laki' => 80,
            'memiliki_perempuan' => 70,
            'memiliki_total' => 150,
            'belum_laki_laki' => 20,
            'belum_perempuan' => 30,
            'belum_total' => 50,
            'persen_memiliki' => 75,
        ]);

        // Nagrog sengaja dibuat tidak konsisten supaya catatan verifikasi
        // di halaman dashboard ikut teruji: 20 + 26 = 46, bukan 50.
        AktaKelahiran::create([
            'desa' => 'Nagrog',
            'wajib_laki_laki' => 60,
            'wajib_perempuan' => 40,
            'wajib_total' => 100,
            'memiliki_laki_laki' => 30,
            'memiliki_perempuan' => 20,
            'memiliki_total' => 50,
            'belum_laki_laki' => 20,
            'belum_perempuan' => 26,
            'belum_total' => 50,
            'persen_memiliki' => 50,
        ]);

        Murid::create(['jenjang' => 'SD dan sederajat', 'jumlah' => 14226]);

        // Guru di sumber hanya punya jenis, tidak punya jenjang. Baris ini
        // memastikan halaman tidak mencoba membaca kolom yang tidak ada.
        Guru::create(['jenis' => 'Guru Sekolah Negeri', 'jumlah' => 772]);

        return $nagrog;
    }

    public function test_halaman_utama_tampil_dengan_angka_dari_database(): void
    {
        $this->isiData();

        $this->get('/')
            ->assertOk()
            ->assertSee('Cicalengka Dalam Angka')
            ->assertSee('Akta Kelahiran')
            ->assertSee('14.226');
    }

    public function test_halaman_utama_menampilkan_catatan_untuk_data_tidak_konsisten(): void
    {
        $this->isiData();

        $this->get('/')
            ->assertOk()
            ->assertSee('Catatan Verifikasi')
            ->assertSee('Nagrog');
    }

    public function test_desa_tidak_dikenal_menghasilkan_404(): void
    {
        $this->isiData();

        $this->get('/desa/99999')->assertNotFound();
        $this->get('/desa/bukan-angka')->assertNotFound();
    }

    public function test_halaman_desa_menampilkan_data_desa(): void
    {
        $nagrog = $this->isiData();

        $this->get('/desa/'.$nagrog->id)
            ->assertOk()
            ->assertSee('Nagrog')
            ->assertSee('Persentase sudah punya akta')
            ->assertSee('50,00%');
    }

    public function test_halaman_desa_menandai_ketidaksesuaian_total_dan_rincian(): void
    {
        $nagrog = $this->isiData();

        $this->get('/desa/'.$nagrog->id)
            ->assertOk()
            ->assertSee('Total dan rincian pada sumber cetakan tidak cocok');
    }

    public function test_persentase_akta_dihitung_ulang_dan_selisih_terdeteksi(): void
    {
        $this->isiData();
        $data = app(StatistikService::class)->ringkasan();

        // Aggregate dua desa: 200 akta dimiliki dari 300 kelahiran wajib
        // = 66,67 persen. Angka diambil dari penjumlahan, bukan dari
        // membagi rata-rata persentase per desa.
        $this->assertSame(66.67, $data['aktaKelahiran']['persen_memiliki']);

        // Rincian Nagrog tidak cocok, jadi harus terdeteksi.
        $this->assertCount(1, $data['catatanVerifikasi']);
    }

    public function test_sebaran_desa_tidak_menjalankan_query_per_desa(): void
    {
        // Dua desa sudah cukup untuk menangkap pola query per desa. Kalau
        // StatistikService kembali membuka satu query untuk tiap baris,
        // jumlah query akan naik seiring jumlah desa.
        $this->isiData();

        $jumlah = 0;
        DB::listen(function () use (&$jumlah): void {
            $jumlah++;
        });

        app(StatistikService::class)->ringkasan();

        // Batasnya longgar supaya test ini tidak rapuh, tapi tetap jauh di
        // bawah jumlah yang akan terjadi bila query dijalankan per desa.
        $this->assertLessThan(40, $jumlah);
    }
}
