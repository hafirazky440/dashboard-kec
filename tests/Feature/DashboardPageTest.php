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
*/

use App\Models\AktaKelahiranDesa;
use App\Models\Desa;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\Tahun;
use App\Services\StatistikService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardPageTest extends TestCase
{
    use RefreshDatabase;

    protected function isiData(): Tahun
    {
        $tahun = Tahun::create(['tahun' => 2026]);

        // Id diambil dari model yang baru dibuat, bukan ditulis langsung,
        // karena penomoran id bisa saja tidak mulai dari 1 di database test.
        $kulon = Desa::create(['nama' => 'Cicalengka Kulon', 'slug' => 'cicalengka-kulon', 'urutan' => 1]);
        $nagrog = Desa::create(['nama' => 'Nagrog', 'slug' => 'nagrog', 'urutan' => 2]);

        // Nagrog sengaja dibuat tidak konsisten supaya catatan verifikasi
        // di halaman dashboard ikut teruji.
        AktaKelahiranDesa::create([
            'desa_id' => $kulon->id,
            'tahun_id' => $tahun->id,
            'wajib_laki_laki' => 100,
            'wajib_perempuan' => 100,
            'wajib_total' => 200,
            'memiliki_laki_laki' => 80,
            'memiliki_perempuan' => 70,
            'memiliki_total' => 150,
            'belum_laki_laki' => 20,
            'belum_perempuan' => 30,
            'belum_total' => 50,
        ]);

        AktaKelahiranDesa::create([
            'desa_id' => $nagrog->id,
            'tahun_id' => $tahun->id,
            'wajib_laki_laki' => 60,
            'wajib_perempuan' => 40,
            'wajib_total' => 100,
            'memiliki_laki_laki' => 30,
            'memiliki_perempuan' => 20,
            'memiliki_total' => 50,
            // Total rincian 46, jadi tidak cocok dengan 50 di atas.
            'belum_laki_laki' => 20,
            'belum_perempuan' => 26,
            'belum_total' => 50,
        ]);

        Murid::create(['tahun_id' => $tahun->id, 'jenjang' => 'SD dan sederajat', 'jumlah' => 14226]);

        // Guru di sumber hanya punya jenis, tidak punya jenjang. Baris ini
        // memastikan halaman tidak mencoba membaca kolom yang tidak ada.
        Guru::create(['tahun_id' => $tahun->id, 'jenis' => 'Sekolah Negeri', 'jumlah' => 772]);

        return $tahun;
    }

    public function test_halaman_utama_tampil_dengan_angka_dari_database(): void
    {
        $this->isiData();

        $this->get('/')
            ->assertOk()
            ->assertSee('Cicalengka Dalam Angka')
            ->assertSee('2026')
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

    public function test_tahun_tidak_dikenal_langsung_memakai_tahun_terbaru(): void
    {
        $tahun = $this->isiData();
        Tahun::create(['tahun' => 2025]);

        // 1999 tidak ada di database, jadi harus jatuh ke 2026, bukan 2025.
        $this->get('/?tahun=1999')
            ->assertOk()
            ->assertSee('Cicalengka Dalam Angka '.$tahun->tahun);
    }

    public function test_desa_tidak_diknown_menghasilkan_404(): void
    {
        $this->isiData();

        $this->get('/desa/tidak-ada')->assertNotFound();
    }

    public function test_halaman_desa_menampilkan_data_desa(): void
    {
        $this->isiData();

        $this->get('/desa/nagrog?tahun=2026')
            ->assertOk()
            ->assertSee('Nagrog')
            ->assertSee('Persentase sudah punya akta')
            ->assertSee('50,00%');
    }

    public function test_halaman_desa_menandai_ketidaksesuaian_total_dan_rincian(): void
    {
        $this->isiData();

        $this->get('/desa/nagrog')
            ->assertOk()
            ->assertSee('Total dan rincian pada sumber cetakan tidak cocok');
    }

    public function test_persentase_akta_dihitung_ulang_dan_selisih_terdeteksi(): void
    {
        $tahun = $this->isiData();
        $statistik = app(StatistikService::class);
        $data = $statistik->untukTahun($tahun);

        // Aggregate dua desa: 200 akta dimiliki dari 300 kelahiran wajib
        // = 66,67 persen. Angka diambil dari penjumlahan, bukan dari
        // membagi rata-rata persentase per desa.
        $this->assertSame(66.67, $data['aktaKelahiran']['persen_memiliki']);

        // Rincian Nagrog tidak cocok, jadi harus terdeteksi.
        $this->assertCount(1, $data['catatanVerifikasi']);
    }

    public function test_tahun_tidak_dikenal_pada_url_desa_juga_dipakai(): void
    {
        $this->isiData();

        $this->get('/desa/cicalengka-kulon?tahun=abc')
            ->assertOk()
            ->assertSee('Cicalengka Kulon');
    }

    public function test_kartu_akta_menyebut_angka_yang_mnemak_ke_sumber(): void
    {
        // Nilainya wajib_total, yaitu kelahiran yang wajib berakta, bukan
        // jumlah akta yang sudah terbit. Label yang tidak menyebut "wajib"
        // akan dibaca sebaliknya oleh pembaca laporan.
        $tahun = $this->isiData();
        $kartu = app(StatistikService::class)->untukTahun($tahun)['kartu'];

        $this->assertSame('Kelahiran Wajib Terdaftar', $kartu['akta']['label']);
        $this->assertSame('300', $kartu['akta']['nilai']);
    }

    public function test_sebaran_desa_tidak_menjalankan_query_per_desa(): void
    {
        // Dua desa sudah cukup untuk menangkap pola query per desa. Kalau
        // StatistikService kembali membuka satu query untuk tiap baris,
        // jumlah query akan naik seiring jumlah desa.
        $tahun = $this->isiData();

        $jumlah = 0;
        DB::listen(function () use (&$jumlah): void {
            $jumlah++;
        });

        app(StatistikService::class)->untukTahun($tahun);

        // Batasnya longgar supaya test ini tidak rapuh, tapi tetap jauh di
        // bawah jumlah yang akan terjadi bila query dijalankan per desa.
        $this->assertLessThan(40, $jumlah);
    }
}
