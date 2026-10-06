<?php

use App\Models\AktaKelahiranDesa;
use Illuminate\Support\Collection;

/**
 * Test perhitungan selisih antara total tersimpan dan penjumlahannya.
 *
 * Selisih ini yang membuat ketidaksesuaian sumber PDF tetap terlihat, jadi
 * perlu dijamin angkanya benar.
 */
it('menghitung selisih total terhadap penjumlahannya', function () {
    $data = new AktaKelahiranDesa([
        'wajib_laki_laki' => 100,
        'wajib_perempuan' => 50,
        'wajib_total' => 150,
        'memiliki_laki_laki' => 80,
        'memiliki_perempuan' => 40,
        'memiliki_total' => 130,
        'belum_laki_laki' => 20,
        'belum_perempuan' => 10,
        'belum_total' => 25,
    ]);

    expect($data->hitungSelisih('wajib'))->toBe(0);
    expect($data->hitungSelisih('memiliki'))->toBe(10);
    expect($data->hitungSelisih('belum'))->toBe(-5);

    expect($data->daftarSelisih())->toBeInstanceOf(Collection::class)
        ->and($data->daftarSelisih()->get('wajib'))->toBe(0);

    // Karena ada dua kelompok yang tidak nol, data dianggap tidak konsisten.
    expect($data->totalKonsisten())->toBeFalse();
});

it('menyatakan konsisten bila semua total sama dengan penjumlahannya', function () {
    $data = new AktaKelahiranDesa([
        'wajib_laki_laki' => 100,
        'wajib_perempuan' => 50,
        'wajib_total' => 150,
        'memiliki_laki_laki' => 80,
        'memiliki_perempuan' => 40,
        'memiliki_total' => 120,
        'belum_laki_laki' => 20,
        'belum_perempuan' => 10,
        'belum_total' => 30,
    ]);

    expect($data->totalKonsisten())->toBeTrue();
});

it('mengembalikan null bila salah satu angkanya kosong', function () {
    $data = new AktaKelahiranDesa([
        'wajib_laki_laki' => 100,
        'wajib_perempuan' => null,
        'wajib_total' => 150,
    ]);

    // Tidak bisa dibandingkan karena salah satu rincian belum diisi.
    expect($data->hitungSelisih('wajib'))->toBeNull();
});
