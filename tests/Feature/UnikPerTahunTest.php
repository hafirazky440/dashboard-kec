<?php

use App\Enums\UserRole;
use App\Filament\Resources\AktaKelahiranDesas\Pages\CreateAktaKelahiranDesa;
use App\Filament\Resources\Pemerintahans\Pages\CreatePemerintahan;
use App\Filament\Resources\PotensiDesas\Pages\CreatePotensiDesa;
use App\Models\AktaKelahiranDesa;
use App\Models\Desa;
use App\Models\Pemerintahan;
use App\Models\PotensiDesa;
use App\Models\Tahun;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

/**
 * Test keunikan data yang beracuan tahun.
 *
 * Hampir semua tabel menyimpan satu baris per tahun. Karena itu keunikan
 * selalu dihitung sebagai kombinasi tahun dan kolom lain, bukan per kolom
 * sendiri. Kalau hanya kolom yang diuji, dua baris untuk tahun berbeda akan
 * dianggap duplikat padahal keduanya benar.
 *
 * Test ini memakai form sungguhan lewat Livewire supaya yang diuji adalah
 * aturan yang benar-benar dipakai admin, bukan salinan aturan di sini.
 */
uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');

    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));

    $this->tahun2025 = Tahun::create(['tahun' => 2025, 'judul' => 'Cicalengka Dalam Angka 2025']);
    $this->tahun2026 = Tahun::create(['tahun' => 2026, 'judul' => 'Cicalengka Dalam Angka 2026']);
    $this->desa = Desa::create(['nama' => 'Nagrog', 'slug' => 'nagrog', 'urutan' => 1]);
});

/**
 * Data akta kelahiran yang sah untuk satu desa satu tahun.
 */
function isianAktaKelahiran(Tahun $tahun, Desa $desa): array
{
    return [
        'tahun_id' => $tahun->id,
        'desa_id' => $desa->id,
        'wajib_laki_laki' => 10,
        'wajib_perempuan' => 8,
        'memiliki_laki_laki' => 6,
        'memiliki_perempuan' => 5,
        'belum_laki_laki' => 4,
        'belum_perempuan' => 3,
    ];
}

it('menerima satu desa yang sama pada tahun yang berbeda', function () {
    Livewire::test(CreateAktaKelahiranDesa::class)
        ->fillForm(isianAktaKelahiran($this->tahun2025, $this->desa))
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreateAktaKelahiranDesa::class)
        ->fillForm(isianAktaKelahiran($this->tahun2026, $this->desa))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(AktaKelahiranDesa::count())->toBe(2);
});

it('menolak dua baris untuk desa dan tahun yang sama', function () {
    Livewire::test(CreateAktaKelahiranDesa::class)
        ->fillForm(isianAktaKelahiran($this->tahun2026, $this->desa))
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreateAktaKelahiranDesa::class)
        ->fillForm(isianAktaKelahiran($this->tahun2026, $this->desa))
        ->call('create')
        ->assertHasFormErrors(['desa_id']);

    // Baris kedua tidak boleh tersimpan meski pesan errornya baru muncul di form.
    expect(AktaKelahiranDesa::count())->toBe(1);
});

it('menegakkan satu kategori potensi per desa per tahun', function () {
    Livewire::test(CreatePotensiDesa::class)
        ->fillForm([
            'tahun_id' => $this->tahun2025->id,
            'desa_id' => $this->desa->id,
            'kategori' => 'Pertanian dan UMKM',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreatePotensiDesa::class)
        ->fillForm([
            'tahun_id' => $this->tahun2026->id,
            'desa_id' => $this->desa->id,
            'kategori' => 'UMKM',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreatePotensiDesa::class)
        ->fillForm([
            'tahun_id' => $this->tahun2026->id,
            'desa_id' => $this->desa->id,
            'kategori' => 'Pertanian dan Pariwisata',
        ])
        ->call('create')
        ->assertHasFormErrors(['desa_id']);

    expect(PotensiDesa::count())->toBe(2);
});

it('menerima jenis pegawai yang sama pada tahun berbeda', function () {
    Livewire::test(CreatePemerintahan::class)
        ->fillForm([
            'tahun_id' => $this->tahun2025->id,
            'jenis' => 'PNS',
            'laki_laki' => 10,
            'perempuan' => 8,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreatePemerintahan::class)
        ->fillForm([
            'tahun_id' => $this->tahun2026->id,
            'jenis' => 'PNS',
            'laki_laki' => 11,
            'perempuan' => 9,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreatePemerintahan::class)
        ->fillForm([
            'tahun_id' => $this->tahun2026->id,
            'jenis' => 'PNS',
            'laki_laki' => 12,
            'perempuan' => 10,
        ])
        ->call('create')
        ->assertHasFormErrors(['jenis']);

    expect(Pemerintahan::count())->toBe(2);
});
