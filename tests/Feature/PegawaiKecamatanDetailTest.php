<?php

use App\Enums\UserRole;
use App\Filament\Resources\PegawaiKecamatans\Pages\CreatePegawaiKecamatan;
use App\Filament\Resources\PegawaiKecamatans\Pages\EditPegawaiKecamatan;
use App\Filament\Resources\PegawaiKecamatans\RelationManagers\PegawaiDetailsRelationManager;
use App\Models\PegawaiKecamatan;
use App\Models\PegawaiKecamatanDetail;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));

    $this->pegawai = PegawaiKecamatan::create([
        'status' => 'PNS',
        'laki_laki' => 13,
        'perempuan' => 4,
    ]);
});

it('menyimpan data rekap pegawai kecamatan', function () {
    Livewire::test(CreatePegawaiKecamatan::class)
        ->fillForm([
            'status' => 'PPPK',
            'laki_laki' => 2,
            'perempuan' => 3,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(PegawaiKecamatan::where('status', 'PPPK')->exists())->toBeTrue();
});

it('menampilkan rincian pegawai pada halaman ubah', function () {
    Livewire::test(EditPegawaiKecamatan::class, [
        'record' => $this->pegawai->getKey(),
    ])
        ->assertSeeLivewire(PegawaiDetailsRelationManager::class);
});

it('menyimpan rincian pegawai lengkap', function () {
    Livewire::test(PegawaiDetailsRelationManager::class, [
        'ownerRecord' => $this->pegawai,
        'pageClass' => EditPegawaiKecamatan::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'nama' => 'Budi Santoso',
            'nip' => '199001012020011001',
            'jabatan' => 'Staf Kecamatan',
            'eselon' => 'Eselon III',
            'golongan' => 'III/a',
        ])
        ->assertNotified();

    $rincian = $this->pegawai->details()
        ->where('nip', '199001012020011001')
        ->firstOrFail();

    expect($rincian->nama)->toBe('Budi Santoso')
        ->and($rincian->jabatan)->toBe('Staf Kecamatan')
        ->and($rincian->eselon)->toBe('Eselon III')
        ->and($rincian->golongan)->toBe('III/a');
});

it('menolak NIP kosong', function () {
    Livewire::test(PegawaiDetailsRelationManager::class, [
        'ownerRecord' => $this->pegawai,
        'pageClass' => EditPegawaiKecamatan::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'nama' => 'Budi Santoso',
            'jabatan' => 'Staf Kecamatan',
            'eselon' => 'Eselon III',
            'golongan' => 'III/a',
        ])
        ->assertHasFormErrors(['nip']);
});

it('menolak NIP yang bukan 18 digit', function () {
    Livewire::test(PegawaiDetailsRelationManager::class, [
        'ownerRecord' => $this->pegawai,
        'pageClass' => EditPegawaiKecamatan::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'nama' => 'Budi Santoso',
            'nip' => '123',
            'jabatan' => 'Staf Kecamatan',
            'eselon' => 'Eselon III',
            'golongan' => 'III/a',
        ])
        ->assertHasFormErrors(['nip']);
});

it('menolak NIP duplikat', function () {
    $this->pegawai->details()->create([
        'nama' => 'Pegawai Pertama',
        'nip' => '199001012020011001',
        'jabatan' => 'Staf Kecamatan',
        'eselon' => 'Eselon III',
        'golongan' => 'III/a',
    ]);

    Livewire::test(PegawaiDetailsRelationManager::class, [
        'ownerRecord' => $this->pegawai,
        'pageClass' => EditPegawaiKecamatan::class,
    ])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'nama' => 'Pegawai Kedua',
            'nip' => '199001012020011001',
            'jabatan' => 'Staf Kecamatan',
            'eselon' => 'Eselon III',
            'golongan' => 'III/a',
        ])
        ->assertHasFormErrors(['nip' => 'unique']);
});

it('mengizinkan editor mengelola rincian tetapi tidak menghapus', function () {
    $editor = User::factory()->create(['role' => UserRole::Editor]);
    $rincian = $this->pegawai->details()->create([
        'nama' => 'Siti Aminah',
        'nip' => '198502022015031002',
        'jabatan' => 'Operator',
        'eselon' => 'Non-Eselon',
        'golongan' => 'Non-Golongan',
    ]);

    expect(Gate::forUser($editor)->allows('viewAny', PegawaiKecamatanDetail::class))->toBeTrue()
        ->and(Gate::forUser($editor)->allows('create', PegawaiKecamatanDetail::class))->toBeTrue()
        ->and(Gate::forUser($editor)->allows('update', $rincian))->toBeTrue()
        ->and(Gate::forUser($editor)->allows('delete', $rincian))->toBeFalse();
});

it('menolak viewer melihat rincian', function () {
    $viewer = User::factory()->create(['role' => UserRole::Viewer]);

    expect(Gate::forUser($viewer)->allows('viewAny', PegawaiKecamatanDetail::class))->toBeFalse();
});

it('menghapus rincian saat rekap pegawai dihapus', function () {
    $rincian = $this->pegawai->details()->create([
        'nama' => 'Siti Aminah',
        'nip' => '198502022015031002',
        'jabatan' => 'Operator',
        'eselon' => 'Non-Eselon',
        'golongan' => 'Non-Golongan',
    ]);

    $this->pegawai->delete();

    expect(PegawaiKecamatanDetail::find($rincian->getKey()))->toBeNull();
});

it('tidak menampilkan rincian pegawai di dashboard publik', function () {
    $rincian = $this->pegawai->details()->create([
        'nama' => 'Budi Santoso',
        'nip' => '199001012020011001',
        'jabatan' => 'Staf Kecamatan',
        'eselon' => 'Eselon III',
        'golongan' => 'III/a',
    ]);

    $this->get('/')
        ->assertOk()
        ->assertDontSee($rincian->nama)
        ->assertDontSee($rincian->nip);
});
