<?php

use App\Filament\Resources\AktaKelahiranDesas\AktaKelahiranDesaResource;
use App\Filament\Resources\AktaKelahiranDesas\Pages\ListAktaKelahiranDesas;
use App\Filament\Resources\AktaKematianDesas\AktaKematianDesaResource;
use App\Filament\Resources\Desas\DesaResource;
use App\Filament\Resources\GeografiDesas\GeografiDesaResource;
use App\Filament\Resources\Gurus\GuruResource;
use App\Filament\Resources\Jalans\JalanResource;
use App\Filament\Resources\Kesehatans\KesehatanResource;
use App\Filament\Resources\Mbgs\MbgResource;
use App\Filament\Resources\Murids\MuridResource;
use App\Filament\Resources\Pasars\PasarResource;
use App\Filament\Resources\Pemerintahans\PemerintahanResource;
use App\Filament\Resources\PotensiDesas\PotensiDesaResource;
use App\Filament\Resources\ProfilKecamatans\ProfilKecamatanResource;
use App\Filament\Resources\Sekolahs\SekolahResource;
use App\Filament\Resources\Sungais\SungaiResource;
use App\Filament\Resources\Tahuns\Pages\CreateTahun;
use App\Filament\Resources\Tahuns\TahunResource;
use App\Models\AktaKelahiranDesa;
use App\Models\Tahun;
use App\Models\User;
use Database\Seeders\AdministrasiKependudukanSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\BackfillUserRoleSeeder;
use Database\Seeders\InfrastrukturSeeder;
use Database\Seeders\PendidikanKesehatanSeeder;
use Database\Seeders\PotensiDesaMbgSeeder;
use Database\Seeders\ProfilDanPemerintahanSeeder;
use Database\Seeders\TahunDesaSeeder;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

/**
 * Resource admin yang harus bisa dibuka di halaman panel.
 *
 * Dipakai bersama oleh test halaman daftar dan halaman formulir.
 */
function resourceClasses(): array
{
    return [
        TahunResource::class,
        DesaResource::class,
        ProfilKecamatanResource::class,
        PemerintahanResource::class,
        GeografiDesaResource::class,
        AktaKematianDesaResource::class,
        AktaKelahiranDesaResource::class,
        JalanResource::class,
        SungaiResource::class,
        PasarResource::class,
        SekolahResource::class,
        GuruResource::class,
        MuridResource::class,
        KesehatanResource::class,
        PotensiDesaResource::class,
        MbgResource::class,
    ];
}

uses(RefreshDatabase::class);

/**
 * Password khusus untuk test.
 *
 * AdminUserSeeder membuat password acak, jadi nilainya tidak diketahui test.
 * Test yang butuh login memakai password ini, bukan password bawaan seeder.
 */
const TEST_PASSWORD = 'password-untuk-test';

function adminUser(): User
{
    $user = User::where('email', 'admin@cicalengka.go.id')->firstOrFail();
    $user->password = TEST_PASSWORD;
    $user->save();

    return $user;
}

beforeEach(function () {
    $this->seed([
        TahunDesaSeeder::class,
        ProfilDanPemerintahanSeeder::class,
        AdministrasiKependudukanSeeder::class,
        InfrastrukturSeeder::class,
        PendidikanKesehatanSeeder::class,
        PotensiDesaMbgSeeder::class,
        AdminUserSeeder::class,
        BackfillUserRoleSeeder::class,
    ]);
});

it('bisa login ke panel admin', function () {
    adminUser();

    $this->get('/admin/login')->assertSuccessful();

    // Formulir login Filament adalah komponen Livewire, bukan route POST biasa.
    Livewire::test(Login::class)
        ->fillForm([
            'email' => 'admin@cicalengka.go.id',
            'password' => TEST_PASSWORD,
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(auth()->check())->toBeTrue();
});

it('menolak login dengan kata sandi salah', function () {
    adminUser();

    Livewire::test(Login::class)
        ->fillForm([
            'email' => 'admin@cicalengka.go.id',
            'password' => 'sandi-yang-salah',
        ])
        ->call('authenticate')
        ->assertHasFormErrors();

    expect(auth()->check())->toBeFalse();
});

it('menampilkan seluruh resource di sidebar', function () {
    $this->actingAs(User::first());

    $response = $this->get('/admin');

    $response->assertSuccessful();

    // Setiap resource harus punya tautan navigasi di sidebar.
    foreach (resourceClasses() as $resource) {
        $response->assertSee($resource::getUrl('index'), escape: false);
    }
});

it('mendaftarkan seluruh resource ke panel admin', function () {
    // getResources() mengembalikan daftar class resource, bukan peta.
    $registered = Filament::getPanel('admin')->getResources();

    foreach (resourceClasses() as $resource) {
        expect($registered)->toContain($resource);
    }
});

it('menaruh setiap resource di grup navigasi yang terdaftar di panel', function () {
    // Resource yang grupnya tidak terdaftar akan muncul sendirian di luar
    // urutan yang diinginkan, jadi penjagaannya perlu diuji, bukan diharap.
    $grupPanel = Filament::getPanel('admin')->getNavigationGroups();

    foreach (resourceClasses() as $resource) {
        $grup = $resource::getNavigationGroup();

        expect($grup)->not->toBeNull()
            ->and($grupPanel)->toContain($grup);
    }
});

it('membuka halaman daftar untuk setiap resource', function () {
    $this->actingAs(User::first());

    foreach (resourceClasses() as $resource) {
        $this->get($resource::getUrl('index'))
            ->assertSuccessful();
    }
});

it('membuka halaman formulir tambah untuk setiap resource', function () {
    $this->actingAs(User::first());

    foreach (resourceClasses() as $resource) {
        $this->get($resource::getUrl('create'))
            ->assertSuccessful();
    }
});

it('menyimpan record baru dari formulir', function () {
    $this->actingAs(User::first());

    Livewire::test(CreateTahun::class)
        ->fillForm([
            'tahun' => 2030,
            'judul' => 'Cicalengka Dalam Angka 2030',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Tahun::where('tahun', 2030)->exists())->toBeTrue();
});

it('menampilkan data yang sudah di-seed pada tabel akta kelahiran', function () {
    $this->actingAs(User::first());

    // Tabel menampilkan 10 baris per halaman secara default, jadi naikkan
    // jumlah baris dulu supaya seluruh 12 data bisa diperiksa.
    Livewire::test(ListAktaKelahiranDesas::class)
        ->set('tableRecordsPerPage', 25)
        ->assertCountTableRecords(12)
        ->assertCanSeeTableRecords(AktaKelahiranDesa::all());
});
