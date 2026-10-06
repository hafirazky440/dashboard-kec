<?php

namespace App\Providers;

use App\Models\AktaKelahiranDesa;
use App\Models\AktaKematianDesa;
use App\Models\Desa;
use App\Models\GeografiDesa;
use App\Models\Guru;
use App\Models\Jalan;
use App\Models\Kesehatan;
use App\Models\Mbg;
use App\Models\Murid;
use App\Models\Pasar;
use App\Models\Pemerintahan;
use App\Models\PotensiDesa;
use App\Models\ProfilKecamatan;
use App\Models\Sekolah;
use App\Models\Sungai;
use App\Models\Tahun;
use App\Models\User;
use App\Policies\ResourcePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Model yang datanya boleh dikelola lewat panel admin.
     *
     * Daftar ini disengaja ditulis manual, bukan diambil otomatis dari isi
     * folder app/Models. Alasannya, kalau ada model baru yang tidak sengaja
     * ikut terambil, model barunya tidak akan diam-diam terbuka untuk diedit.
     * Model baru harus ditambahkan di sini dengan sadar.
     *
     * @var array<int, class-string<Model>>
     */
    protected array $modelTerpolicy = [
        Desa::class,
        Tahun::class,
        ProfilKecamatan::class,
        Pemerintahan::class,
        GeografiDesa::class,
        AktaKelahiranDesa::class,
        AktaKematianDesa::class,
        Jalan::class,
        Sungai::class,
        Pasar::class,
        Sekolah::class,
        Guru::class,
        Murid::class,
        Kesehatan::class,
        PotensiDesa::class,
        Mbg::class,
    ];

    public function register(): void
    {
        //
    }

    /**
     * Mendaftarkan policy untuk setiap model.
     *
     * Penting: Filament mencari policy berdasarkan MODEL, bukan berdasarkan
     * class resource. static::can('delete') di dalam Filament/resource memanggil
     * Gate::getPolicyFor(static::getModel()), sehingga mendaftarkan policy pada
     * Filament\Resources\Resource sama sekali tidak berpengaruh.
     *
     * Alternatifnya mendaftarkan policy satu per model secara manual di file
     * ini, lalu Gate akan menebaknya lewat konvensi nama. Cara eksplisit di
     * bawah dipakai supaya jelas model mana saja yang ikut aturan.
     */
    public function boot(): void
    {
        foreach ($this->modelTerpolicy as $model) {
            Gate::policy($model, ResourcePolicy::class);
        }

        // User tidak punya resource CRUD, jadi cukup aturan dasarnya saja.
        Gate::policy(User::class, ResourcePolicy::class);
    }
}
