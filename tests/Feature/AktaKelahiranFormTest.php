<?php

use App\Enums\UserRole;
use App\Filament\Resources\AktaKelahirans\Pages\CreateAktaKelahiran;
use App\Models\AktaKelahiran;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

/**
 * Test formulir akta kelahiran, khususnya pengisian total otomatis.
 *
 * Total dihitung dari rincian laki-laki dan perempuan. Bila penjumlahannya
 * tidak cocok dengan yang diketik admin, angka tetap boleh tersimpan karena
 * sumber cetakan memang punya ketidaksesuaian seperti itu. Yang diuji di sini
 * adalah mekanismenya, bukan pelarangannya.
 */
uses(RefreshDatabase::class);

beforeEach(function () {
    // Panel memakai strict authorization, jadi halaman Create hanya bisa
    // dibuka oleh pengguna yang benar-benar punya peran. Tanpa ini, mount()
    // halaman ditolak dan pengujian formulir gagal dengan pesan yang menyesatkan.
    Filament::setCurrentPanel('admin');

    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));
});

it('mengisi total otomatis dari kolom rincian', function () {
    Livewire::test(CreateAktaKelahiran::class)
        ->fillForm([
            'desa' => 'Cibodas',
            'wajib_laki_laki' => 100,
            'wajib_perempuan' => 50,
            'memiliki_laki_laki' => 80,
            'memiliki_perempuan' => 40,
            'belum_laki_laki' => 20,
            'belum_perempuan' => 10,
            'persen_memiliki' => 80,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $data = AktaKelahiran::firstOrFail();

    expect((int) $data->wajib_total)->toBe(150)
        ->and((int) $data->memiliki_total)->toBe(120)
        ->and((int) $data->belum_total)->toBe(30)
        ->and($data->totalKonsisten())->toBeTrue();
});

it('menyimpan total yang diketik manual walau berbeda dari penjumlahan', function () {
    // Angka di bawah meniru keadaan sumber cetakan: total lebih besar dari
    // penjumlahan rinciannya. Ini harus tetap bisa tersimpan.
    Livewire::test(CreateAktaKelahiran::class)
        ->fillForm([
            'desa' => 'Cibodas',
            'wajib_laki_laki' => 2593,
            'wajib_perempuan' => 3445,
            'memiliki_laki_laki' => 100,
            'memiliki_perempuan' => 100,
            'belum_laki_laki' => 2493,
            'belum_perempuan' => 3345,
            'belum_total' => 6038,
            'persen_memiliki' => 10,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $data = AktaKelahiran::firstOrFail();

    expect((int) $data->belum_total)->toBe(6038)
        ->and($data->hitungSelisih('belum'))->toBe(200)
        ->and($data->totalKonsisten())->toBeFalse();
});
