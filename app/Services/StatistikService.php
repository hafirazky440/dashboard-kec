<?php

namespace App\Services;

use App\Models\AktaKelahiranDesa;
use App\Models\AktaKematianDesa;
use App\Models\Desa;
use App\Models\GeografiDesa;
use App\Models\Guru;
use App\Models\Jalan;
use App\Models\Mbg;
use App\Models\Murid;
use App\Models\Pasar;
use App\Models\Pemerintahan;
use App\Models\PotensiDesa;
use App\Models\ProfilKecamatan;
use App\Models\Sekolah;
use App\Models\Sungai;
use App\Models\Tahun;
use Illuminate\Support\Collection;

/**
 * Mengumpulkan angka statistik untuk dashboard publik.
 *
 * Prinsip yang dipegang di sini:
 * - Angka selalu dibaca dari database untuk tahun yang dipilih, bukan ditulis
 *   di dalam view. Kalau sumbernya diperbaiki, dashboard ikut berubah tanpa
 *   perlu menyentuh kode.
 * - Nilai yang tidak tersedia di sumber tetap null, bukan diisi nol. Bedanya
 *   penting: nol berarti "tidak ada", null berarti "tidak diketahui".
 * - Persentase selalu dihitung ulang dari nilai aslinya, tidak disimpan.
 *
 * Kelas ini sengaja hanya mengembalikan array biasa, tanpa objek khusus,
 * supaya view tinggal memakai $data['nilai'] tanpa perlu tambahan property.
 */
class StatistikService
{
    /**
     * Tahun yang tersedia untuk dipilih pengguna.
     *
     * @return Collection<int, Tahun>
     */
    public function daftarTahun(): Collection
    {
        return Tahun::orderByDesc('tahun')->get();
    }

    /**
     * Mencari tahun dari nilai yang dikirim pengguna.
     *
     * Nilai yang tidak dikenal tidak ditolak, tapi diganti tahun terbaru.
     * Melempar 404 untuk ketikan yang salah terasa membingungkan dibanding
     * dengan diam-diam menampilkan data tahun lain.
     */
    public function cariTahun(?string $nilai): ?Tahun
    {
        if ($nilai === null || $nilai === '') {
            return Tahun::orderByDesc('tahun')->first();
        }

        return Tahun::where('tahun', (int) $nilai)
            ->first()
            ?? Tahun::orderByDesc('tahun')->first();
    }

    /**
     * Semua statistik untuk satu tahun.
     *
     * @return array<string, mixed>
     */
    public function untukTahun(?Tahun $tahun): array
    {
        $tahunId = $tahun?->id;
        $adaTahun = $tahunId !== null;

        // Profil kecamatan dipakai oleh kartu dan oleh seksi profil sekaligus,
        // jadi dibaca sekali lalu dipakai dua kali.
        $profil = $adaTahun
            ? ProfilKecamatan::where('tahun_id', $tahunId)->first()
            : null;

        return [
            'tahun' => $tahun,
            'daftarTahun' => $this->daftarTahun(),
            'kartu' => $this->kartu($tahunId, $adaTahun, $profil),
            'profil' => $profil,
            'sebaranDesa' => $adaTahun ? $this->sebaranDesa($tahunId) : collect(),
            'sekolahPerJenjang' => $adaTahun ? $this->pendidikanPerJenjang($tahunId) : collect(),
            'muridPerJenjang' => $adaTahun ? $this->muridPerJenjang($tahunId) : collect(),
            'guruPerJenis' => $adaTahun ? $this->guruPerJenis($tahunId) : collect(),
            'totalMurid' => $adaTahun ? (int) Murid::where('tahun_id', $tahunId)->sum('jumlah') : 0,
            'totalGuru' => $adaTahun ? (int) Guru::where('tahun_id', $tahunId)->sum('jumlah') : 0,
            'aktaKelahiran' => $adaTahun ? $this->aktaKelahiran($tahunId) : [],
            'aktaKematian' => $adaTahun ? $this->aktaKematian($tahunId) : [],
            'potensiPerKategori' => $adaTahun ? $this->potensiPerKategori($tahunId) : collect(),
            'pemerintahan' => $adaTahun
                ? Pemerintahan::where('tahun_id', $tahunId)->orderBy('jenis')->get()
                : collect(),
            'mbg' => $adaTahun ? Mbg::where('tahun_id', $tahunId)->orderBy('jenis')->get() : collect(),
            'fasilitas' => $adaTahun ? $this->fasilitas($tahunId) : [],
            'catatanVerifikasi' => $adaTahun ? $this->catatanVerifikasi($tahunId) : [],
        ];
    }

    /**
     * Rekapitulasi jalan, pasar, dan sungai se-kecamatan.
     *
     * @return array{jalan: int, panjangJalan: float, pasar: int, sungai: int}
     */
    protected function fasilitas(int $tahunId): array
    {
        return [
            'jalan' => Jalan::where('tahun_id', $tahunId)->count(),
            'panjangJalan' => (float) Jalan::where('tahun_id', $tahunId)->sum('panjang_km'),
            'pasar' => Pasar::where('tahun_id', $tahunId)->count(),
            'sungai' => Sungai::where('tahun_id', $tahunId)->count(),
        ];
    }

    /**
     * Angka untuk kartu statistik di bagian atas halaman.
     *
     * @param  ?ProfilKecamatan  $profil  Sudah dibaca oleh pemanggil karena juga dipakai untuk seksi profil
     * @return array<string, array{label: string, nilai: ?string, keterangan: string}>
     */
    protected function kartu(?int $tahunId, bool $adaTahun, mixed $profil): array
    {
        if (! $adaTahun) {
            return [];
        }

        $totalSekolah = (int) Sekolah::where('tahun_id', $tahunId)->sum('jumlah');
        $totalMurid = (int) Murid::where('tahun_id', $tahunId)->sum('jumlah');
        $totalPanjangJalan = (float) Jalan::where('tahun_id', $tahunId)->sum('panjang_km');
        $totalAkta = (int) AktaKelahiranDesa::where('tahun_id', $tahunId)->sum('wajib_total');

        return [
            'penduduk' => [
                'label' => 'Total Penduduk',
                'nilai' => $profil?->total_penduduk !== null
                    ? number_format((int) $profil->total_penduduk, 0, ',', '.')
                    : null,
                'keterangan' => 'Jumlah penduduk laki-laki dan perempuan',
            ],
            'desa' => [
                'label' => 'Desa',
                'nilai' => (string) Desa::count(),
                'keterangan' => 'Desa di wilayah Kecamatan Cicalengka',
            ],
            'luas' => [
                'label' => 'Luas Wilayah',
                'nilai' => $profil?->luas_wilayah_km2 !== null
                    ? number_format((float) $profil->luas_wilayah_km2, 2, ',', '.').' km²'
                    : null,
                'keterangan' => 'Luas wilayah dalam kilometer persegi',
            ],
            'sekolah' => [
                'label' => 'Sekolah',
                'nilai' => number_format($totalSekolah, 0, ',', '.'),
                'keterangan' => 'SD, SMP, SMA, dan Madrasah Ibtidaiyah',
            ],
            'murid' => [
                'label' => 'Murid',
                'nilai' => number_format($totalMurid, 0, ',', '.'),
                'keterangan' => 'Jumlah murid pada semua jenjang',
            ],
            'akta' => [
                // Label menyebut "wajib" karena angkanya diambil dari
                // wajib_total, yaitu seluruh kelahiran yang wajib berakta.
                // Menulisnya sebagai "Kelahiran Terdaftar" saja akan dibaca
                // sebagai jumlah akta yang sudah terbit, padahal yang
                // dimaksud justru sebaliknya.
                'label' => 'Kelahiran Wajib Terdaftar',
                'nilai' => number_format($totalAkta, 0, ',', '.'),
                'keterangan' => 'Seluruh kelahiran yang wajib memiliki akta, sebelum dikurangi yang belum menerbitkannya',
            ],
            'jalan' => [
                'label' => 'Panjang Jalan',
                'nilai' => number_format($totalPanjangJalan, 1, ',', '.').' km',
                'keterangan' => 'Jalan status desa dan setingkat desa',
            ],
        ];
    }

    /**
     * Luas wilayah dan jumlah penduduk per desa.
     *
     * Hanya dua desa yang punya data geografi pada sumber cetakan, jadi
     * daftar ini berisi dua baris, bukan dua belas. Itu murni keterbatasan
     * sumber, bukan kesalahan program.
     *
     * @return Collection<int, object>
     */
    protected function sebaranDesa(int $tahunId): Collection
    {
        // Ketiga tabel ini dibaca sekali untuk seluruh desa, bukan sekali per
        // desa. Versi sebelumnya menjalankan tiga query untuk setiap desa, jadi
        // dua belas desa berarti tiga puluh enam query untuk satu tabel saja.
        // Jumlahnya tidak banyak, tapi pola seperti itu akan langsung berat
        // begitu jumlah desa bertambah.
        $geografiPerDesa = GeografiDesa::where('tahun_id', $tahunId)
            ->get()
            ->keyBy('desa_id');

        $aktaPerDesa = AktaKelahiranDesa::where('tahun_id', $tahunId)
            ->get()
            ->keyBy('desa_id');

        $potensiPerDesa = PotensiDesa::where('tahun_id', $tahunId)
            ->get()
            ->groupBy('desa_id');

        return Desa::query()
            ->orderBy('urutan')
            ->get()
            ->map(function (Desa $desa) use ($geografiPerDesa, $aktaPerDesa, $potensiPerDesa): object {
                $geografi = $geografiPerDesa->get($desa->id);
                $kelahiran = $aktaPerDesa->get($desa->id);

                return (object) [
                    'desa' => $desa,
                    'luas' => $geografi?->luas_km2,
                    'wajib' => $kelahiran?->wajib_total,
                    'memiliki' => $kelahiran?->memiliki_total,
                    'persenMemiliki' => $kelahiran?->persen_memiliki,
                    // Koleksi kosong, bukan null, supaya view tidak perlu
                    // memeriksa dua bentuk data yang berbeda.
                    'potensi' => $potensiPerDesa->get($desa->id)?->pluck('kategori') ?? collect(),
                ];
            });
    }

    /**
     * Jumlah sekolah per jenjang dan jenis.
     *
     * @return Collection<int, object>
     */
    protected function pendidikanPerJenjang(int $tahunId): Collection
    {
        return Sekolah::where('tahun_id', $tahunId)
            ->selectRaw('jenjang, jenis, SUM(jumlah) as total')
            ->groupBy('jenjang', 'jenis')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Jumlah murid per jenjang, disertai guru untuk jenjang yang sama.
     *
     * Catatan penting: tabel guru di sumber cetakan tidak punya kolom jenjang.
     * Guru hanya dipecah menjadi Sekolah Negeri dan Sekolah Swasta, sedangkan
     * murid dipecah per jenjang. Karena itu angka guru tidak bisa dipasangkan
     * per jenjang seperti biasanya, dan hanya ditampilkan sebagai
     * total keseluruhan.
     *
     * @return Collection<int, object>
     */
    protected function muridPerJenjang(int $tahunId): Collection
    {
        return Murid::where('tahun_id', $tahunId)
            ->selectRaw('jenjang, SUM(jumlah) as total')
            ->groupBy('jenjang')
            ->get()
            ->sortBy(function ($row): int {
                // Urutan mengikuti jenjang pendidikan, bukan abjad, supaya
                // grafik menampilkan TK di awal danPerguruan Tinggi di akhir.
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
    protected function guruPerJenis(int $tahunId): Collection
    {
        return Guru::where('tahun_id', $tahunId)
            ->selectRaw('jenis, SUM(jumlah) as total')
            ->groupBy('jenis')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Ringkasan akta kelahiran untuk seluruh kecamatan.
     *
     * @return array<string, int|float|null>
     */
    protected function aktaKelahiran(int $tahunId): array
    {
        $total = AktaKelahiranDesa::where('tahun_id', $tahunId)
            ->selectRaw('
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
            // Persentase dihitung ulang, tidak pernah disimpan, supaya angka
            // yang salah ketik di sumber tidak ikut terbawa ke laporan.
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
    protected function aktaKematian(int $tahunId): array
    {
        $total = AktaKematianDesa::where('tahun_id', $tahunId)
            ->selectRaw('SUM(laki_laki) as lk, SUM(perempuan) as pt, SUM(total) as total')
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
     * Jumlah desa per kategori potensi.
     *
     * @return Collection<int, object>
     */
    protected function potensiPerKategori(int $tahunId): Collection
    {
        return PotensiDesa::where('tahun_id', $tahunId)
            ->selectRaw('kategori, COUNT(*) as jumlah')
            ->groupBy('kategori')
            ->orderByDesc('jumlah')
            ->get();
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
    protected function catatanVerifikasi(int $tahunId): array
    {
        $catatan = [];

        // Baris akta kelahiran yang totalnya berbeda dari penjumlahannya.
        $selisih = AktaKelahiranDesa::where('tahun_id', $tahunId)
            ->with('desa')
            ->get()
            ->filter(fn (AktaKelahiranDesa $row): bool => ! $row->totalKonsisten());

        foreach ($selisih as $row) {
            $detail = $row->daftarSelisih()
                ->filter(fn (?int $nilai): bool => $nilai !== null && $nilai !== 0)
                ->map(fn (int $nilai, string $prefix): string => $prefix.' '.$nilai)
                ->implode(', ');

            $catatan[] = (object) [
                'sumber' => 'Administrasi Kependudukan',
                'pesan' => "Jumlah akta kelahiran di Desa {$row->desa->nama} berbeda antara total dan rinciannya (selisih: {$detail}).",
            ];
        }

        // Profil kecamatan yang catatannya terisi.
        $profil = ProfilKecamatan::where('tahun_id', $tahunId)
            ->whereNotNull('catatan')
            ->where('catatan', '!=', '')
            ->first();

        if ($profil !== null) {
            $catatan[] = (object) [
                'sumber' => 'Profil Kecamatan',
                'pesan' => $profil->catatan,
            ];
        }

        return $catatan;
    }

    /**
     * Semua data untuk halaman detail satu desa.
     *
     * @return array<string, mixed>
     */
    public function untukDesa(Desa $desa, ?Tahun $tahun): array
    {
        $tahunId = $tahun?->id;
        $adaTahun = $tahunId !== null;

        return [
            'desa' => $desa,
            'tahun' => $tahun,
            'daftarTahun' => $this->daftarTahun(),

            'geografi' => $adaTahun
                ? GeografiDesa::where('desa_id', $desa->id)->where('tahun_id', $tahunId)->first()
                : null,

            'aktaKelahiran' => $adaTahun
                ? AktaKelahiranDesa::where('desa_id', $desa->id)->where('tahun_id', $tahunId)->first()
                : null,

            'aktaKematian' => $adaTahun
                ? AktaKematianDesa::where('desa_id', $desa->id)
                    ->where('tahun_id', $tahunId)
                    ->first()
                : null,

            'potensi' => $adaTahun
                ? PotensiDesa::where('desa_id', $desa->id)->where('tahun_id', $tahunId)->pluck('kategori')
                : collect(),

            'sekolahTerdekat' => $adaTahun ? $this->sekolahSekitar($tahunId) : collect(),
            'jalanSekitar' => $adaTahun ? $this->jalanSekitar($tahunId) : collect(),
            'pasarSekitar' => $adaTahun ? $this->pasarSekitar($tahunId) : collect(),
            'sungaiSekitar' => $adaTahun ? $this->sungaiSekitar($tahunId) : collect(),
        ];
    }

    /**
     * Daftar sekolah untuk halaman desa.
     *
     * @return Collection<int, object>
     */
    protected function sekolahSekitar(int $tahunId): Collection
    {
        return Sekolah::where('tahun_id', $tahunId)
            ->selectRaw('jenjang, jenis, SUM(jumlah) as total')
            ->groupBy('jenjang', 'jenis')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * @return Collection<int, object>
     */
    protected function jalanSekitar(int $tahunId): Collection
    {
        return Jalan::where('tahun_id', $tahunId)
            ->orderByDesc('panjang_km')
            ->get(['id', 'nama', 'tingkat', 'panjang_km', 'batas']);
    }

    /**
     * @return Collection<int, object>
     */
    protected function pasarSekitar(int $tahunId): Collection
    {
        return Pasar::where('tahun_id', $tahunId)
            ->orderByDesc('jumlah')
            ->get(['id', 'nama', 'jumlah', 'lokasi', 'hari_operasi', 'catatan']);
    }

    /**
     * @return Collection<int, object>
     */
    protected function sungaiSekitar(int $tahunId): Collection
    {
        return Sungai::where('tahun_id', $tahunId)
            ->orderByDesc('panjang_km')
            ->get(['id', 'nama', 'panjang_km', 'status']);
    }
}
