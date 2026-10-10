<?php

use App\Enums\UserRole;
use App\Filament\Resources\AktaKelahirans\AktaKelahiranResource;
use App\Filament\Resources\AktaKematians\AktaKematianResource;
use App\Filament\Resources\DataPenduduks\DataPendudukResource;
use App\Filament\Resources\Desas\DesaResource;
use App\Filament\Resources\Gurus\GuruResource;
use App\Filament\Resources\Kecamatans\KecamatanResource;
use App\Filament\Resources\Mbgs\MbgResource;
use App\Filament\Resources\Murids\MuridResource;
use App\Filament\Resources\PegawaiKecamatans\PegawaiKecamatanResource;
use App\Filament\Resources\Pengairans\PengairanResource;
use App\Filament\Resources\RuasJalans\RuasJalanResource;
use App\Filament\Resources\SaranaPerdagangans\SaranaPerdaganganResource;
use App\Filament\Resources\Sekolahs\SekolahResource;
use App\Models\AktaKelahiran;
use App\Models\Desa;
use App\Models\RuasJalan;
use App\Models\Sekolah;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Menguji pencarian global Filament dengan data nyata.
 *
 * Test ini sengaja memanggil getGlobalSearchResults() secara langsung, bukan
 * hanya membuka halaman. Global search baru benar-benar berjalan ketika hasil
 * pencarian dirender, dan di situlah bug nama kolom akan muncul. Membuka
 * halaman saja tidak akan menangkap bug itu.
 */
uses(RefreshDatabase::class);

beforeEach(function () {
    // Pencarian global hanya jalan di dalam konteks panel Filament.
    Filament::setCurrentPanel('admin');

    $this->actingAs(User::create([
        'name' => 'Petugas',
        'email' => 'petugas@example.test',
        'password' => 'rahasia-kuat-123',
        'role' => UserRole::Admin,
    ]));
});

function isiDataPencarian(): array
{
    $kulon = Desa::create(['nama' => 'Cicalengka Kulon', 'luas_km2' => 0.49, 'potensi' => 'UMKM']);
    $nagrog = Desa::create(['nama' => 'Nagrog', 'luas_km2' => 10.03, 'potensi' => 'Pertanian']);

    AktaKelahiran::create([
        'desa' => 'Nagrog',
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

    RuasJalan::create([
        'nama' => 'Jalan Nagrog',
        'status' => 'Kabupaten',
        'panjang_km' => 3.5,
    ]);

    Sekolah::create([
        'jenis' => 'SD Negeri',
        'jumlah' => 46,
    ]);

    return [$kulon, $nagrog];
}

/**
 * Setiap resource harus bisa dirender hasilnya tanpa error, termasuk yang
 * tabelnya tidak punya kolom teks.
 */
it('bisa menjalankan pencarian global di setiap resource', function (string $resource) {
    isiDataPencarian();

    $hasil = $resource::getGlobalSearchResults('a');

    expect($hasil->all())->toBeArray();
})->with([
    'desa' => DesaResource::class,
    'kecamatan' => KecamatanResource::class,
    'data penduduk' => DataPendudukResource::class,
    'pegawai kecamatan' => PegawaiKecamatanResource::class,
    'akta kelahiran' => AktaKelahiranResource::class,
    'akta kematian' => AktaKematianResource::class,
    'sekolah' => SekolahResource::class,
    'guru' => GuruResource::class,
    'murid' => MuridResource::class,
    'ruas jalan' => RuasJalanResource::class,
    'pengairan' => PengairanResource::class,
    'sarana perdagangan' => SaranaPerdaganganResource::class,
    'mbg' => MbgResource::class,
]);

it('mencari nama desa menemukan record akta kelahiran', function () {
    isiDataPencarian();

    $hasil = AktaKelahiranResource::getGlobalSearchResults('Nagrog');

    expect($hasil)->toHaveCount(1)
        ->and($hasil[0]->title)->toBe('Nagrog')
        // Tidak ada lagi relasi desa/tahun, jadi keterangan tambahan kosong.
        ->and($hasil[0]->details)->toBe([]);
});

it('mencari nama desa menemukan record desa', function () {
    isiDataPencarian();

    $hasil = DesaResource::getGlobalSearchResults('Nagrog');

    expect($hasil)->toHaveCount(1)
        ->and($hasil[0]->title)->toBe('Nagrog');
});

it('pencarian mencakup kolom kedua jika kolom pertama tidak cocok', function () {
    isiDataPencarian();

    // Desa mencari lewat kolom nama dan potensi.
    $hasil = DesaResource::getGlobalSearchResults('UMKM');

    expect($hasil)->toHaveCount(1)
        ->and($hasil[0]->title)->toBe('Cicalengka Kulon');
});

it('kolom yang tidak ada di tabel diabaikan', function () {
    isiDataPencarian();

    // RuasJalan memakai kolom nama dan status. Kalau salah satu nama kolomnya
    // keliru, pencarian harus tetap jalan, tidak boleh error.
    $hasil = RuasJalanResource::getGlobalSearchResults('Kabupaten');

    expect($hasil)->toHaveCount(1)
        ->and($hasil[0]->title)->toBe('Jalan Nagrog');
});

it('pencarian tidak melempar error untuk peran pengamat', function () {
    isiDataPencarian();

    $this->actingAs(User::create([
        'name' => 'Lihat Saja',
        'email' => 'lihat@example.test',
        'password' => 'rahasia-kuat-123',
        'role' => UserRole::Viewer,
    ]));

    $hasil = SekolahResource::getGlobalSearchResults('SD');

    expect($hasil->all())->toBeArray();
});
