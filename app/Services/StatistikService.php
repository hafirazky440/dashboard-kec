<?php

namespace App\Services;

use App\Models\AktaKelahiran;
use App\Models\AktaKematian;
use App\Models\DataPenduduk;
use App\Models\Desa;
use App\Models\Guru;
use App\Models\Kecamatan;
use App\Models\Mbg;
use App\Models\Murid;
use App\Models\PegawaiKecamatan;
use App\Models\Pengairan;
use App\Models\RuasJalan;
use App\Models\SaranaPerdagangan;
use App\Models\Sekolah;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Mengumpulkan angka statistik untuk dashboard publik.
 *
 * Prinsip yang dipegang di sini:
 * - Angka selalu dibaca dari database, bukan ditulis di dalam view. Kalau
 *   sumbernya diperbaiki, dashboard ikut berubah tanpa perlu menyentuh kode.
 * - Nilai yang tidak tersedia di sumber tetap null, bukan diisi nol. Bedanya
 *   penting: nol berarti "tidak ada", null berarti "tidak diketahui".
 * - Skema tabel mengikuti kolom pada file CSV persis, tanpa dimensi tahun.
 *
 * Kelas ini sengaja hanya mengembalikan array biasa, tanpa objek khusus,
 * supaya view tinggal memakai $data['nilai'] tanpa perlu tambahan property.
 */
class StatistikService
{
    /**
     * Semua statistik untuk halaman dashboard.
     *
     * @return array<string, mixed>
     */
    public function ringkasan(): array
    {
        return [
            'kecamatan' => Kecamatan::first(),
            'penduduk' => $this->penduduk(),
            'sebaranDesa' => $this->sebaranDesa(),
            'sekolahPerJenis' => $this->sekolahPerJenis(),
            'muridPerJenjang' => $this->muridPerJenjang(),
            'guruPerJenis' => $this->guruPerJenis(),
            'totalSekolah' => (int) Sekolah::sum('jumlah'),
            'totalMurid' => (int) Murid::sum('jumlah'),
            'totalGuru' => (int) Guru::sum('jumlah'),
            'aktaKelahiran' => $this->aktaKelahiran(),
            'aktaKematian' => $this->aktaKematian(),
            'potensiPerKategori' => $this->potensiPerKategori(),
            'pegawai' => PegawaiKecamatan::orderBy('status')->get(),
            'mbg' => Mbg::orderBy('nama')->get(),
            // Daftar jalan, pengairan, dan sarana perdagangan ikut dikirim
            // supaya dashboard bisa menampilkan rincian, bukan hanya
            // jumlahnya. Semuanya dibaca dari database.
            'jalan' => RuasJalan::orderByDesc('panjang_km')->get(),
            'pengairan' => Pengairan::orderByDesc('panjang_km')->get(),
            'sarana' => SaranaPerdagangan::orderBy('nama')->get(),
            'fasilitas' => $this->fasilitas(),
            'catatanVerifikasi' => $this->catatanVerifikasi(),
        ];
    }

    /**
     * Indikator penduduk per kecamatan, dikunci berdasarkan nama_datanya.
     *
     * @return Collection<string, int>
     */
    protected function penduduk(): Collection
    {
        return DataPenduduk::pluck('jumlah', 'nama_data')
            ->map(fn ($nilai) => (int) $nilai);
    }

    /**
     * Rekapitulasi jalan, pengairan, dan sarana perdagangan se-kecamatan.
     *
     * @return array{jalan: int, panjangJalan: float, pengairan: int, sarana: int}
     */
    protected function fasilitas(): array
    {
        return [
            'jalan' => RuasJalan::count(),
            'panjangJalan' => (float) RuasJalan::sum('panjang_km'),
            'pengairan' => Pengairan::count(),
            'sarana' => SaranaPerdagangan::count(),
        ];
    }

    /**
     * Luas wilayah dan potensi tiap desa.
     *
     * @return Collection<int, object>
     */
    protected function sebaranDesa(): Collection
    {
        return Desa::query()
            ->orderBy('nama')
            ->get()
            ->map(fn (Desa $desa): object => (object) [
                'desa' => $desa,
                'luas' => $desa->luas_km2,
                'potensi' => $desa->potensi,
            ]);
    }

    /**
     * Jumlah sekolah per jenis.
     *
     * Pada sumber, kolom jenis sudah memuat jenjang sekaligus status, misalnya
     * "SD Negeri" atau "TK Swasta", jadi tidak dipisah lagi.
     *
     * @return Collection<int, object>
     */
    protected function sekolahPerJenis(): Collection
    {
        return Sekolah::selectRaw('jenis, SUM(jumlah) as total')
            ->groupBy('jenis')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Jumlah murid per jenjang, disertai guru untuk jenjang yang sama.
     *
     * Catatan penting: tabel guru tidak punya kolom jenjang. Guru hanya
     * dipecah menjadi sekolah negeri dan swasta, sedangkan murid dipecah per
     * jenjang. Karena itu angka guru tidak bisa dipasangkan per jenjang seperti
     * biasanya, dan hanya ditampilkan sebagai total keseluruhan.
     *
     * @return Collection<int, object>
     */
    protected function muridPerJenjang(): Collection
    {
        return Murid::selectRaw('jenjang, SUM(jumlah) as total')
            ->groupBy('jenjang')
            ->get()
            ->sortBy(function ($row): int {
                // Urutan mengikuti jenjang pendidikan, bukan abjad, supaya
                // grafik menampilkan TK di awal dan Perguruan Tinggi di akhir.
                $urutan = ['TK dan sederajat' => 1, 'SD dan sederajat' => 2, 'SMP dan sederajat' => 3, 'SMA dan sederajat' => 4];

                return $urutan[$row->jenjang] ?? 99;
            })
            ->values()
            ->map(fn ($row): object => (object) [
                'jenjang' => $row->jenjang,
                'murid' => (int) $row->total,
            ]);
    }

    /**
     * Jumlah guru berdasarkan status sekolah.
     *
     * @return Collection<int, object>
     */
    protected function guruPerJenis(): Collection
    {
        return Guru::selectRaw('jenis, SUM(jumlah) as total')
            ->groupBy('jenis')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Ringkasan akta kelahiran untuk seluruh kecamatan.
     *
     * @return array<string, int|float|null>
     */
    protected function aktaKelahiran(): array
    {
        $total = AktaKelahiran::selectRaw('
                SUM(wajib_laki_laki) as wajib_lk,
                SUM(wajib_perempuan) as wajib_pt,
                SUM(wajib_total) as wajib,
                SUM(memiliki_laki_laki) as memiliki_lk,
                SUM(memiliki_perempuan) as memiliki_pt,
                SUM(memiliki_total) as memiliki,
                SUM(belum_laki_laki) as belum_lk,
                SUM(belum_perempuan) as belum_pt,
                SUM(belum_total) as belum
            ')
            ->first();

        if ($total === null) {
            return [];
        }

        return [
            'wajib_lk' => (int) $total->wajib_lk,
            'wajib_pt' => (int) $total->wajib_pt,
            'wajib' => (int) $total->wajib,
            'memiliki_lk' => (int) $total->memiliki_lk,
            'memiliki_pt' => (int) $total->memiliki_pt,
            'memiliki' => (int) $total->memiliki,
            'belum_lk' => (int) $total->belum_lk,
            'belum_pt' => (int) $total->belum_pt,
            'belum' => (int) $total->belum,
            'persen_memiliki' => $total->wajib > 0
                ? round($total->memiliki / $total->wajib * 100, 2)
                : null,
        ];
    }

    /**
     * Ringkasan akta kematian untuk seluruh kecamatan.
     *
     * @return array<string, int>
     */
    protected function aktaKematian(): array
    {
        $total = AktaKematian::selectRaw('SUM(laki_laki) as lk, SUM(perempuan) as pt, SUM(total) as total')
            ->first();

        if ($total === null) {
            return [];
        }

        return [
            'laki_laki' => (int) $total->lk,
            'perempuan' => (int) $total->pt,
            'total' => (int) $total->total,
        ];
    }

    /**
     * Jumlah desa per jenis potensi.
     *
     * Kolom potensi di tabel desa berisi gabungan kata, misalnya "Pertanian dan
     * UMKM". Supaya bisa dibuat grafik per potensi, nilai itu dipecah pada kata
     * "dan" lalu dihitung per kunci. Satu desa karena itu bisa terhitung pada
     * lebih dari satu kategori.
     *
     * @return Collection<int, object>
     */
    protected function potensiPerKategori(): Collection
    {
        $hitung = [];

        foreach (Desa::whereNotNull('potensi')->pluck('potensi') as $teks) {
            foreach (preg_split('/\s+dan\s+/iu', (string) $teks) ?: [] as $potensi) {
                $potensi = trim($potensi);

                if ($potensi === '') {
                    continue;
                }

                $hitung[$potensi] = ($hitung[$potensi] ?? 0) + 1;
            }
        }

        arsort($hitung);

        return collect($hitung)
            ->map(fn (int $jumlah, string $kategori): object => (object) [
                'kategori' => $kategori,
                'jumlah' => $jumlah,
            ])
            ->values();
    }

    /**
     * Catatan dari sumber yang perlu dibaca pembaca laporan.
     *
     * Ini bukan daftar barang rusak. Ini informasi bahwa angka tertentu pada
     * sumber cetakan punya ketidaksesuaian, dan dashboard lebih baik
     * mengatakannya daripada menyembunyikannya.
     *
     * @return array<int, object>
     */
    protected function catatanVerifikasi(): array
    {
        $catatan = [];

        // Baris akta kelahiran yang totalnya berbeda dari penjumlahannya.
        $selisih = AktaKelahiran::query()
            ->get()
            ->filter(fn (AktaKelahiran $row): bool => ! $row->totalKonsisten());

        foreach ($selisih as $row) {
            $detail = $row->daftarSelisih()
                ->filter(fn (?int $nilai): bool => $nilai !== null && $nilai !== 0)
                ->map(fn (int $nilai, string $prefix): string => $prefix.' '.$nilai)
                ->implode(', ');

            $catatan[] = (object) [
                'sumber' => 'Administrasi Kependudukan',
                'pesan' => "Jumlah akta kelahiran di Desa {$row->desa} berbeda antara total dan rinciannya (selisih: {$detail}).",
            ];
        }

        // Jumlah penduduk total yang tidak sama dengan jumlah laki-laki dan
        // perempuannya. Pada sumber cetakan angka ini memang berbeda.
        $penduduk = $this->penduduk();
        $total = $penduduk->get('Total Penduduk');
        $laki = $penduduk->get('Laki-laki');
        $perempuan = $penduduk->get('Perempuan');

        if ($total !== null && $laki !== null && $perempuan !== null && $total !== $laki + $perempuan) {
            $catatan[] = (object) [
                'sumber' => 'Data Penduduk',
                'pesan' => 'Total penduduk '.number_format($total, 0, ',', '.')
                    .' tidak sama dengan jumlah laki-laki dan perempuan ('
                    .number_format($laki + $perempuan, 0, ',', '.')
                    .'), selisih '.number_format(abs($total - $laki - $perempuan), 0, ',', '.').'.',
            ];
        }

        // Baris infrastruktur yang panjangnya tidak tercetak pada sumber.
        foreach ([
            ['ruas_jalan', 'Jalan'],
            ['pengairan', 'Pengairan'],
        ] as [$tabel, $judul]) {
            $kosong = DB::table($tabel)->whereNull('panjang_km')->pluck('nama');

            foreach ($kosong as $nama) {
                $catatan[] = (object) [
                    'sumber' => $judul,
                    'pesan' => "Panjang {$nama} tidak tercetak pada sumber cetakan, jadi kolomnya dibiarkan kosong.",
                ];
            }
        }

        return $catatan;
    }

    /**
     * Semua data untuk halaman detail satu desa.
     *
     * Data infrastruktur, sekolah, dan sarana perdagangan memang hanya ada
     * pada tingkat kecamatan, bukan per desa, sehingga seluruh desa berbagi
     * daftar yang sama.
     *
     * @return array<string, mixed>
     */
    public function untukDesa(Desa $desa): array
    {
        return [
            'desa' => $desa,
            'luas' => $desa->luas_km2,
            'potensi' => $desa->potensi,

            'aktaKelahiran' => AktaKelahiran::where('desa', $desa->nama)->first(),
            'aktaKematian' => AktaKematian::where('desa', $desa->nama)->first(),

            'sekolah' => $this->sekolahPerJenis(),
            'jalan' => RuasJalan::orderByDesc('panjang_km')->get(),
            'pengairan' => Pengairan::orderByDesc('panjang_km')->get(),
            'sarana' => SaranaPerdagangan::orderBy('nama')->get(),
        ];
    }
}
