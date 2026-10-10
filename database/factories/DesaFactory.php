<?php

namespace Database\Factories;

use App\Models\Desa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Desa>
 */
class DesaFactory extends Factory
{
    protected $model = Desa::class;

    /**
     * Urutan dipakai agar nama desa yang dibuat tidak bertabrakan dengan data
     * asli. Nama acak dari factory tidak realistis untuk tabel yang isinya
     * hanya nama desa wilayah.
     */
    public function definition(): array
    {
        $urutan = fake()->unique()->numberBetween(1, 1000);

        return [
            'nama' => 'Desa Uji '.$urutan,
            'luas_km2' => fake()->randomFloat(2, 1, 100),
            'potensi' => null,
        ];
    }
}
