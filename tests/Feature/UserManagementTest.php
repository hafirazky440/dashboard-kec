<?php

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('hanya admin yang bisa membuka menu pengguna', function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Editor]));
    $this->get(UserResource::getUrl('index'))->assertForbidden();

    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));
    $this->get(UserResource::getUrl('index'))->assertSuccessful();
});

it('membuat pengguna baru dengan peran dan kata sandi', function () {
    $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Operator Baru',
            'email' => 'operator-baru@example.com',
            'role' => UserRole::Editor->value,
            'password' => 'sandi-baru-123',
            'password_confirmation' => 'sandi-baru-123',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $pengguna = User::where('email', 'operator-baru@example.com')->firstOrFail();

    expect($pengguna->role)->toBe(UserRole::Editor)
        ->and(Hash::check('sandi-baru-123', $pengguna->password))->toBeTrue();
});

it('mempertahankan kata sandi lama saat kolom kata sandi dikosongkan', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $pengguna = User::factory()->create(['role' => UserRole::Viewer]);

    $this->actingAs($admin);

    Livewire::test(EditUser::class, [
        'record' => $pengguna->getKey(),
    ])
        ->fillForm([
            'name' => 'Nama Diperbarui',
            'email' => $pengguna->email,
            'role' => UserRole::Viewer->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($pengguna->refresh()->name)->toBe('Nama Diperbarui')
        ->and(Hash::check('password', $pengguna->password))->toBeTrue();
});

it('tidak menurunkan peran admin terakhir', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin);

    Livewire::test(EditUser::class, [
        'record' => $admin->getKey(),
    ])
        ->fillForm([
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => UserRole::Viewer->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($admin->refresh()->role)->toBe(UserRole::Admin);
});

it('menolak admin menghapus dirinya sendiri', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    expect(Gate::forUser($admin)->allows('delete', $admin))->toBeFalse();
});

it('mengizinkan admin menghapus pengguna lain', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $pengguna = User::factory()->create(['role' => UserRole::Viewer]);

    expect(Gate::forUser($admin)->allows('delete', $pengguna))->toBeTrue();
});

it('menolak editor mengelola pengguna', function () {
    $editor = User::factory()->create(['role' => UserRole::Editor]);
    $pengguna = User::factory()->create(['role' => UserRole::Viewer]);

    expect(Gate::forUser($editor)->allows('create', User::class))->toBeFalse()
        ->and(Gate::forUser($editor)->allows('update', $pengguna))->toBeFalse()
        ->and(Gate::forUser($editor)->allows('delete', $pengguna))->toBeFalse();
});
