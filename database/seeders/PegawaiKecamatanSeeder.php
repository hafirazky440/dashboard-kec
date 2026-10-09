<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PegawaiKecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/pegawai_kecamatan.csv');
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException("Tidak dapat membuka file CSV: {$path}");
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);
            throw new RuntimeException("File CSV kosong: {$path}");
        }

        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== count($header) || $data === [null]) {
                continue;
            }

            $rows[] = array_map(
                static fn ($value) => $value === '' ? null : $value,
                array_combine($header, $data),
            );
        }

        fclose($handle);

        DB::table('pegawai_kecamatan')->insert($rows);
    }
}
