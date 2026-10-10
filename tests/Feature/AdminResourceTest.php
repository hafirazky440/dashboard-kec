<?php

use App\Filament\Resources\AktaKelahirans\AktaKelahiranResource;
use App\Filament\Resources\AktaKelahirans\Pages\ListAktaKelahirans;
use App\Filament\Resources\AktaKematians\AktaKematianResource;
use App\Filament\Resources\DataPenduduks\DataPendudukResource;
use App\Filament\Resources\Desas\DesaResource;
use App\Filament\Resources\Desas\Pages\CreateDesa;
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
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\AktaKelahiranSeeder;
use Database\Seeders\AktaKematianSeeder;
use Database\Seeders\BackfillUserRoleSeeder;
use Database\Seeders\DesaSeeder;
use Database\Seeders\GuruSeeder;
use Database\Seeders\MuridSeeder;
use Database\Seeders\SekolahSeeder;
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
        DesaResource::class,
        KecamatanResource::class,
        DataPendudukResource::class,
        PegawaiKecamatanResource::class,
        AktaKelahiranResource::class,
        AktaKematianResource::class,
        SekolahResource::class,
        GuruResource::class,
        MuridResource::class,
        RuasJalanResource::class,
        PengairanResource::class,
        SaranaPerdaganganResource::class,
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
        DesaSeeder::class,
        AktaKelahiranSeeder::class,
        AktaKematianSeeder::class,
        SekolahSeeder::class,
        GuruSeeder::class,
        MuridSeeder::class,
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

    Livewire::test(CreateDesa::class)
        ->fillForm([
            'nama' => 'Desa Baru Untuk Test',
            'luas_km2' => 1.25,
            'potensi' => 'Pertanian',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Desa::where('nama', 'Desa Baru Untuk Test')->exists())->toBeTrue();
});

it('menampilkan data yang sudah di-seed pada tabel akta kelahiran', function () {
    $this->actingAs(User::first());

    // Tabel menampilkan 10 baris per halaman secara default, jadi naikkan
    // jumlah baris dulu supaya seluruh data bisa diperiksa.
    Livewire::test(ListAktaKelahirans::class)
        ->set('tableRecordsPerPage', 25)
        ->assertCountTableRecords(12)
        ->assertCanSeeTableRecords(AktaKelahiran::all());
});
