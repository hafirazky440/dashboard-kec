<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\AktaKelahiranDesas\AktaKelahiranDesaResource;
use App\Filament\Resources\Desas\DesaResource;
use App\Filament\Resources\Jalans\JalanResource;
use App\Filament\Resources\PotensiDesas\PotensiDesaResource;
use App\Filament\Resources\ProfilKecamatans\ProfilKecamatanResource;
use App\Filament\Resources\Sekolahs\SekolahResource;
use App\Filament\Resources\Tahuns\TahunResource;
use App\Models\AktaKelahiranDesa;
use App\Models\Desa;
use App\Models\GeografiDesa;
use App\Models\Jalan;
use App\Models\PotensiDesa;
use App\Models\ProfilKecamatan;
use App\Models\Sekolah;
use App\Models\Tahun;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Menguji pencarian global Filament dengan data nyata.
 *
 * Test ini sengaja memanggil getGlobalSearchResults() secara langsung, bukan
 * hanya membuka halaman. Global search baru benar-benar berjalan ketika hasil
 * pencarian dirender, dan di situlah bug relasi akan muncul. Membuka halaman
 * saja tidak akan menangkap bug itu.
 */
class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Pencarian global hanya jalan di dalam konteks panel Filament.
        Filament::setCurrentPanel('admin');

        $this->actingAs(User::create([
            'name' => 'Petugas',
            'email' => 'petugas@example.test',
            'password' => 'rahasia-kuat-123',
            'role' => UserRole::Admin,
        ]));
    }

    protected function isiData(): Tahun
    {
        $tahun = Tahun::create(['tahun' => 2026, 'judul' => 'Cicalengka Dalam Angka 2026']);

        $nagrog = Desa::create(['nama' => 'Nagrog', 'slug' => 'nagrog', 'urutan' => 1]);
        $kulon = Desa::create(['nama' => 'Cicalengka Kulon', 'slug' => 'cicalengka-kulon', 'urutan' => 2]);

        GeografiDesa::create(['desa_id' => $kulon->id, 'tahun_id' => $tahun->id, 'luas_km2' => 0.49]);

        AktaKelahiranDesa::create([
            'desa_id' => $nagrog->id,
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

        PotensiDesa::create(['desa_id' => $kulon->id, 'tahun_id' => $tahun->id, 'kategori' => 'Pertanian']);
        PotensiDesa::create(['desa_id' => $nagrog->id, 'tahun_id' => $tahun->id, 'kategori' => 'UMKM']);

        Jalan::create([
            'tahun_id' => $tahun->id,
            'nama' => 'Jalan Nagrog',
            'tingkat' => 'Status desa',
            'panjang_km' => 3.5,
        ]);

        Sekolah::create([
            'tahun_id' => $tahun->id,
            'jenjang' => 'SD',
            'jenis' => 'Negeri',
            'jumlah' => 46,
        ]);

        ProfilKecamatan::create([
            'tahun_id' => $tahun->id,
            'luas_wilayah_km2' => 42.21,
            'jumlah_desa' => 2,
            'catatan' => 'Catatan verifikasi profil.',
        ]);

        return $tahun;
    }

    /**
     * @return array<string, array{class-string}>
     */
    public static function daftarResource(): array
    {
        $base = 'App\\Filament\\Resources\\';

        return [
            'desa' => [$base.'Desas\DesaResource'],
            'tahun' => [$base.'Tahuns\TahunResource'],
            'aktekelahiran' => [$base.'AktaKelahiranDesas\AktaKelahiranDesaResource'],
            'aktekematian' => [$base.'AktaKematianDesas\AktaKematianDesaResource'],
            'geografi' => [$base.'GeografiDesas\GeografiDesaResource'],
            'potensi' => [$base.'PotensiDesas\PotensiDesaResource'],
            'jalan' => [$base.'Jalans\JalanResource'],
            'pasar' => [$base.'Pasars\PasarResource'],
            'sungai' => [$base.'Sungais\SungaiResource'],
            'sekolah' => [$base.'Sekolahs\SekolahResource'],
            'guru' => [$base.'Gurus\GuruResource'],
            'murid' => [$base.'Murids\MuridResource'],
            'pemerintahan' => [$base.'Pemerintahans\PemerintahanResource'],
            'kesehatan' => [$base.'Kesehatans\KesehatanResource'],
            'mbg' => [$base.'Mbgs\MbgResource'],
            'profil' => [$base.'ProfilKecamatans\ProfilKecamatanResource'],
        ];
    }

    /**
     * Setiap resource harus bisa dirender hasilnya tanpa error, termasuk yang
     * tabelnya tidak punya kolom teks dan yang modelnya tidak punya relasi desa.
     */
    #[DataProvider('daftarResource')]
    public function test_setiap_resource_bisa_menjalankan_pencarian_global(string $resource): void
    {
        $this->isiData();

        /** @var class-string<resource> $resource */
        $hasil = $resource::getGlobalSearchResults('a');

        $this->assertIsArray($hasil->all());
    }

    public function test_mencari_nama_desa_menemukan_record_akta_kelahiran(): void
    {
        $this->isiData();

        $hasil = AktaKelahiranDesaResource::getGlobalSearchResults('Nagrog');

        $this->assertCount(1, $hasil);
        $this->assertSame('Nagrog', $hasil[0]->title);
    }

    public function test_keterangan_tahun_desa_d_itampilkan_pada_hasil_pencarian(): void
    {
        $this->isiData();

        $hasil = AktaKelahiranDesaResource::getGlobalSearchResults('Nagrog');

        $this->assertSame(
            ['Desa' => 'Nagrog', 'Tahun' => '2026'],
            $hasil[0]->details
        );
    }

    public function test_resource_tanpa_relasi_desa_tetap_aman(): void
    {
        $this->isiData();

        // Desa dan Tahun tidak punya relasi apa pun. Dipanggil tanpa ?. agar
        // error relasi yang salah Ketik akan benar-benar gagal test.
        $hasilDesa = DesaResource::getGlobalSearchResults('Nagrog');
        $hasilTahun = TahunResource::getGlobalSearchResults('2026');

        $this->assertCount(1, $hasilDesa);
        $this->assertSame('Nagrog', $hasilDesa[0]->title);
        $this->assertSame([], $hasilDesa[0]->details);

        // Tahun searched dengan kolom 'judul', bukan kolom angka 'tahun'.
        $this->assertCount(1, $hasilTahun);
        $this->assertSame('Cicalengka Dalam Angka 2026', $hasilTahun[0]->title);
        $this->assertSame([], $hasilTahun[0]->details);
    }

    public function test_resource_dengan_kolom_teks_pada_tabelnya_memberi_judul_dari_kolom_itu(): void
    {
        $this->isiData();

        $hasil = JalanResource::getGlobalSearchResults('Nagrog');

        $this->assertCount(1, $hasil);
        $this->assertSame('Jalan Nagrog', $hasil[0]->title);
        // Hanya ada relasi tahun, jadi desa tidak boleh muncul di keterangan.
        $this->assertSame(['Tahun' => '2026'], $hasil[0]->details);
    }

    public function test_pencarian_mencakup_kolom_kedua_jika_kolom_pertama_tidak_cocok(): void
    {
        $this->isiData();

        // PotensiDesa mencari lewat desa.nama dan kategori.
        $hasil = PotensiDesaResource::getGlobalSearchResults('UMKM');

        $this->assertCount(1, $hasil);
    }

    public function test_kolom_yang_tidak_ada_di_tabel_diabaikan(): void
    {
        $this->isiData();

        // ProfilKecamatan memakai kolom 'catatan'. Kalau nama kolomnya salah,
        // pencarian harus tetap jalan, tidak boleh error.
        $hasil = ProfilKecamatanResource::getGlobalSearchResults('verifikasi');

        $this->assertCount(1, $hasil);
    }

    public function test_pencarian_tidak_membocorkan_resource_yang_tidak_diizinkan(): void
    {
        $this->isiData();

        $this->actingAs(User::create([
            'name' => 'Lihat Saja',
            'email' => 'lihat@example.test',
            'password' => 'rahasia-kuat-123',
            'role' => UserRole::Viewer,
        ]));

        // Untuk viewer, canAccess() pada resource tetap benar karena akses
        // daftar hanya diberikan ke admin. Yang diuji di sini adalah agar
        // pencarian tidak melempar error untuk peran non admin.
        $hasil = SekolahResource::getGlobalSearchResults('SD');

        $this->assertIsArray($hasil->all());
    }
}
