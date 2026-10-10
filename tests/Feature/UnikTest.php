<?php

use App\Enums\UserRole;
use App\Filament\Resources\AktaKelahirans\Pages\CreateAktaKelahiran;
use App\Filament\Resources\Desas\Pages\CreateDesa;
use App\Filament\Resources\PegawaiKecamatans\Pages\CreatePegawaiKecamatan;
use App\Models\AktaKelahiran;
use App\Models\Desa;
use App\Models\PegawaiKecamatan;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

/**
 * Test keunikan data setelah dimensi tahun dihapus.
 *
 * Dulu hampir semua tabel menyimpan satu baris per tahun, sehingga keunikan
 * dihitung sebagai kombinasi tahun dan kolom lain. Sekarang skema mengikuti
 * file CSV tanpa tahun, jadi keunikan berlaku penuh pada kolomnya sendiri:
 * satu desa hanya boleh punya satu baris akta kelahiran, satu status pegawai
 * hanya boleh muncul sekali, dan seterusnya.
 *
 * Test ini memakai form sungguhan lewat Livewire supaya yang diuji adalah
 * aturan yang benar-benar dipakai admin, bukan salinan aturan di sini.
 */
uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');

    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));
});

/**
 * Isian akta kelahiran yang sah untuk satu desa.
 */
function isianAktaKelahiran(string $desa): array
{
    return [
        'desa' => $desa,
        'wajib_laki_laki' => 10,
        'wajib_perempuan' => 8,
        'memiliki_laki_laki' => 6,
        'memiliki_perempuan' => 5,
        'belum_laki_laki' => 4,
        'belum_perempuan' => 3,
        'persen_memiliki' => 60,
    ];
}

it('menerima desa yang berbeda pada akta kelahiran', function () {
    Livewire::test(CreateAktaKelahiran::class)
        ->fillForm(isianAktaKelahiran('Nagrog'))
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreateAktaKelahiran::class)
        ->fillForm(isianAktaKelahiran('Cicalengka Kulon'))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(AktaKelahiran::count())->toBe(2);
});

it('menolak dua baris akta kelahiran untuk desa yang sama', function () {
    Livewire::test(CreateAktaKelahiran::class)
        ->fillForm(isianAktaKelahiran('Nagrog'))
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreateAktaKelahiran::class)
        ->fillForm(isianAktaKelahiran('Nagrog'))
        ->call('create')
        ->assertHasFormErrors(['desa']);

    expect(AktaKelahiran::count())->toBe(1);
});

it('menolak dua pegawai dengan status yang sama', function () {
    Livewire::test(CreatePegawaiKecamatan::class)
        ->fillForm(['status' => 'PNS', 'laki_laki' => 10, 'perempuan' => 8])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreatePegawaiKecamatan::class)
        ->fillForm(['status' => 'PPPK', 'laki_laki' => 1, 'perempuan' => 1])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreatePegawaiKecamatan::class)
        ->fillForm(['status' => 'PNS', 'laki_laki' => 11, 'perempuan' => 9])
        ->call('create')
        ->assertHasFormErrors(['status']);

    expect(PegawaiKecamatan::count())->toBe(2);
});

it('menolak dua desa dengan nama yang sama', function () {
    Livewire::test(CreateDesa::class)
        ->fillForm(['nama' => 'Nagrog'])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreateDesa::class)
        ->fillForm(['nama' => 'Nagrog'])
        ->call('create')
        ->assertHasFormErrors(['nama']);

    expect(Desa::count())->toBe(1);
});
