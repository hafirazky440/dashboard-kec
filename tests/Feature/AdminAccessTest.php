<?php

use App\Enums\UserRole;
use App\Filament\Resources\Desas\DesaResource;
use App\Models\Desa;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\BackfillUserRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

/**
 * Test keamanan panel admin: peran pengguna dan password awal.
 *
 * Berdiri sendiri dari AdminResourceTest supaya concerns yang diuji jelas
 * terpisah: file itu menguji halaman, file ini menguji akses.
 */
uses(RefreshDatabase::class);

it('memberikan peran admin saat seeder berjalan', function () {
    $this->seed(AdminUserSeeder::class);
    $this->seed(BackfillUserRoleSeeder::class);

    expect(User::where('email', 'admin@cicalengka.go.id')->firstOrFail()->role)
        ->toBe(UserRole::Admin);
});

it('mengisi peran admin yang dibuat sebelum migrasi role', function () {
    // Meniru keadaan nyata: akun sudah ada, kolom role baru ditambahkan
    // sehingga isinya kosong.
    User::factory()->create([
        'email' => 'admin@cicalengka.go.id',
        'role' => null,
    ]);

    $this->seed(BackfillUserRoleSeeder::class);

    expect(User::where('email', 'admin@cicalengka.go.id')->firstOrFail()->role)
        ->toBe(UserRole::Admin);
});

it('tidak memberi peran admin otomatis ke akun lain', function () {
    // Akun selain admin bawaan tidak boleh otomatis jadi admin.
    User::factory()->create([
        'email' => 'operator@cicalengka.go.id',
        'role' => null,
    ]);

    $this->seed(BackfillUserRoleSeeder::class);

    $operator = User::where('email', 'operator@cicalengka.go.id')->firstOrFail();

    expect($operator->role)->toBeNull()
        ->and($operator->canDeleteData())->toBeFalse();
});

it('tidak memakai password yang tertulis di repository', function () {
    $this->seed(AdminUserSeeder::class);

    $user = User::where('email', 'admin@cicalengka.go.id')->firstOrFail();

    // Password lama pernah ditulis langsung di seeder. Kalau nilai ini masih
    // cocok, berarti seeder belum benar-benar memakai password acak.
    expect(Hash::check('Cicalengka2026!', $user->password))->toBeFalse();

    // Password juga harus disimpan dalam bentuk hash bcrypt, bukan teks biasa.
    expect(Hash::isHashed($user->password))->toBeTrue();
});

it('tidak mengubah password saat seeder dijalankan ulang', function () {
    $this->seed(AdminUserSeeder::class);

    $user = User::where('email', 'admin@cicalengka.go.id')->firstOrFail();
    $user->password = 'password-yang-sudah-diubah-admin';
    $user->save();

    $hashSebelum = $user->password;

    $this->seed(AdminUserSeeder::class);

    $hashSesudah = User::where('email', 'admin@cicalengka.go.id')->firstOrFail()->password;

    expect($hashSesudah)->toBe($hashSebelum);
});

it('admin boleh menghapus data', function () {
    $this->seed(AdminUserSeeder::class);

    $admin = User::where('email', 'admin@cicalengka.go.id')->firstOrFail();

    expect($admin->canDeleteData())->toBeTrue();
    expect($admin->canAccessPanel(filament()->getPanel('admin')))->toBeTrue();
});

it('editor boleh menambah dan mengubah tetapi tidak menghapus', function () {
    $editor = User::factory()->create(['role' => UserRole::Editor]);

    expect($editor->canDeleteData())->toBeFalse();
    expect($editor->isEditor())->toBeTrue();
});

it('pengamat hanya boleh membaca di level model', function () {
    $viewer = User::factory()->create(['role' => UserRole::Viewer]);

    expect($viewer->canDeleteData())->toBeFalse();
    expect($viewer->isViewer())->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Test izin yang benar-benar diuji lewat policy Filament
|--------------------------------------------------------------------------
|
| Test sebelumnya hanya memanggil helper di model. Itu belum membuktikan apa
| apa, karena helper di model tidak dipakai Filament saat menampilkan tombol.
| Test di bawah membaca hasil render halaman sungguhan dan memeriksa apakah
| tautan atau tombol aksinya benar-benar ada atau tidak.
|
| Ini penting karena ResourcePolicy sempat terdaftar pada class dasar
| Filament\Resources\Resource, padahal Filament mencari policy berdasarkan
| model. Akibatnya semua aksi terizinkan untuk semua peran tanpa error apa
| pun, dan test lama tetap lulus.
|
*/

it('menyembunyikan tombol tambah, ubah, dan hapus untuk pengamat', function () {
    $pengamat = User::factory()->create(['role' => UserRole::Viewer]);

    $this->actingAs($pengamat);

    $daftar = $this->get(DesaResource::getUrl('index'))->assertSuccessful();

    // Tombol tambah tidak boleh muncul, sehingga halaman create harus 403.
    $daftar->assertDontSee('Desa baru', escape: false);
    $this->get(DesaResource::getUrl('create'))->assertForbidden();
});

it('menyembunyikan halaman ubah untuk pengamat', function () {
    $desa = Desa::factory()->create();

    $this->actingAs(User::factory()->create(['role' => UserRole::Viewer]));

    $this->get(DesaResource::getUrl('edit', ['record' => $desa]))->assertForbidden();
});

it('menyembunyikan aksi hapus untuk editor', function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Editor]));

    // Editor boleh menambah dan mengubah, jadi halaman create harus tetap
    // bisa dibuka.
    $this->get(DesaResource::getUrl('create'))->assertSuccessful();

    // activism Penghapusan harus ditolak.
    $desa = Desa::factory()->create();

    expect(Gate::forUser(auth()->user())->allows('delete', $desa))->toBeFalse();
});

it('mengizinkan admin menambah, mengubah, dan menghapus', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin);

    $this->get(DesaResource::getUrl('index'))->assertSuccessful();
    $this->get(DesaResource::getUrl('create'))->assertSuccessful();

    $desa = Desa::factory()->create();

    expect(Gate::forUser($admin)->allows('create', Desa::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('update', $desa))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('delete', $desa))->toBeTrue();
});

it('user tanpa peran diperlakukan sama seperti pengamat', function () {
    // User tanpa role adalah data lama sebelum migrasi. Aksesnya dibatasi
    // menjadi baca saja supaya tidak lebih berbahaya daripada sebelumnya.
    $tanpaPeran = User::factory()->create(['role' => null]);

    expect($tanpaPeran->canAccessPanel(filament()->getPanel('admin')))->toBeTrue();
    expect($tanpaPeran->canDeleteData())->toBeFalse();

    $this->actingAs($tanpaPeran);

    $this->get(DesaResource::getUrl('create'))->assertForbidden();
});

it('menutup akses halaman create untuk peran yang tidak boleh menambah', function (?string $peran) {
    $this->actingAs(User::factory()->create(['role' => $peran]));

    $this->get(DesaResource::getUrl('create'))->assertForbidden();
})->with([
    UserRole::Viewer->value,
    null,
]);
