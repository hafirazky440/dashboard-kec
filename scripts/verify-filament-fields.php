<?php

/**
 * Validasi silang nama field Filament terhadap skema database.
 *
 * Aplikasi ini memakai nama tabel dan kolom berbahasa Indonesia. Salah ketik
 * seperti "panjang" alih-alih "panjang_km" tidak caught oleh PHP, hanya
 * muncul sebagai halaman kosong atau error saat admin membuka tabel.
 * Skrip ini membaca daftar kolom langsung dari database lalu mencocokkannya
 * dengan setiap nama field yang dipakai di form dan tabel Filament.
 *
 * Jalankan dari root project:
 *
 *     php artisan tinker --execute="require base_path('scripts/verify-filament-fields.php');"
 *
 * Field yang bukan kolom (accessor, relasi, atau kolom nilai hitung) sengaja
 * dikecualikan lewat daftar $virtual di bawah.
 */

use Illuminate\Support\Facades\Schema;

$resourceToTable = [
    'Desas' => 'desa',
    'ProfilKecamatans' => 'profil_kecamatan',
    'Pemerintahans' => 'pemerintahan',
    'GeografiDesas' => 'geografi_desa',
    'AktaKematianDesas' => 'akta_kematian_desa',
    'AktaKelahiranDesas' => 'akta_kelahiran_desa',
    'Jalans' => 'jalan',
    'Sungais' => 'sungai',
    'Pasars' => 'pasar',
    'Sekolahs' => 'sekolah',
    'Gurus' => 'guru',
    'Murids' => 'murid',
    'Kesehatans' => 'kesehatan',
    'PotensiDesas' => 'potensi_desa',
    'Mbgs' => 'mbg',
    'Tahuns' => 'tahun',
];

// Nama field yang memang bukan kolom database.
$virtual = [
    // Accessor pada model.
    'persen_memiliki',
    // Method pada model.
    'total',
    // Nama arbitrer untuk kolom yang nilai diambil dari relasi, query, atau
    // perhitungan di model. Tidak ada kolomnya di database.
    'total_tahun', 'subtotal', 'persentase', 'selisih', 'selisih_total',
    'jumlah_data', 'jumlah_sekolah', 'jumlah_murid',
    // Nama relasi belongsTo dan kolom milik relasi tersebut.
    'tahun', 'tahun.tahun', 'desa', 'desa.nama',
    // Kolom bawaan Laravel.
    'slug', 'created_at', 'updated_at',
];

$problems = [];
$checked = 0;
$base = app_path('Filament/Resources');

foreach ($resourceToTable as $folder => $table) {
    $tableColumns = Schema::getColumnListing($table);

    $files = array_merge(
        glob($base.'/'.$folder.'/Schemas/*.php') ?: [],
        glob($base.'/'.$folder.'/Tables/*.php') ?: [],
    );

    foreach ($files as $file) {
        $content = file_get_contents($file);

        preg_match_all("/make\('([a-z_0-9\.]+)'/", $content, $matches);

        foreach ($matches[1] as $field) {
            $checked++;

            $root = explode('.', $field)[0];

            if (in_array($root, $virtual, true)) {
                continue;
            }

            if (in_array($root, $tableColumns, true)) {
                continue;
            }

            $problems[] = sprintf(
                '%s: "%s" tidak ada di tabel %s (kolom tersedia: %s)',
                basename($file),
                $field,
                $table,
                implode(', ', $tableColumns),
            );
        }
    }
}

echo 'Field diperiksa: '.$checked."\n";
echo 'Field bermasalah: '.count($problems)."\n";

foreach ($problems as $problem) {
    echo "  - {$problem}\n";
}

if ($problems === []) {
    echo "OK: semua nama field cocok dengan skema database.\n";
}
