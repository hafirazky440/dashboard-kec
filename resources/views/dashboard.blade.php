@extends('layouts.public')

@section('judul', 'Dashboard Statistik')

@section('konten')
    {{--
        Empty state: database belum punya data tahun sama sekali, misalnya
        baru di-install sebelum seeder dijalankan. Halaman tetap harus tampil
        rapi, bukan error, supaya admin tahu apa yang perlu dilakukan.
    --}}
    @if ($tahun === null)
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <h1 class="text-xl font-semibold text-slate-900">Belum ada data statistik</h1>
            <p class="mx-auto mt-2 max-w-lg text-sm text-slate-600">
                Dashboard ini menampilkan data dari publikasi Cicalengka Dalam Angka. Database
                belum memuat satu pun data tahun, jadi belum ada yang bisa ditampilkan.
            </p>
            <p class="mt-4 text-sm text-slate-600">
                Jalankan <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">php artisan migrate --seed</code>
                untuk mengisi data awal, atau tambahkan lewat panel admin di
                <a href="/admin" class="underline">/admin</a>.
            </p>
        </div>
    @else
        {{-- Judul halaman --}}
        <div class="mb-8">
            <p class="text-sm font-medium text-dashboard-700">Kecamatan Cicalengka</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                Cicalengka Dalam Angka
            </h1>
            <p class="mt-3 max-w-3xl text-slate-600">
                Ringkasan kondisi kependudukan, pendidikan, administrasi warga, dan potensi desa di
                Kecamatan Cicalengka, Kabupaten Bandung, berdasarkan publikasi
                <span class="font-medium text-slate-800">Cicalengka Dalam Angka {{ $tahun->tahun }}</span>.
            </p>
        </div>

    {{-- Kartu ringkasan --}}
    <section aria-labelledby="ringkasan" class="mb-12">
        <x-judul-bagian
            id="ringkasan"
            judul="Ringkasan"
            keterangan="Angka ditampilkan utuh apa adanya dari sumber cetakan. Sebagian angka tidak konsisten pada sumber dan sengaja tidak dikoreksi; detailnya ada di bagian Catatan Verifikasi."
        />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($kartu as $datum)
                <x-kartu-statistik
                    :label="$datum['label']"
                    :nilai="$datum['nilai']"
                    :keterangan="$datum['keterangan']"
                />
            @endforeach
        </div>
    </section>

    {{-- Pendidikan --}}
    <section aria-labelledby="pendidikan" class="mb-12">
        <x-judul-bagian
            id="pendidikan"
            judul="Pendidikan"
            keterangan="Madrasah Ibtidaiyah, Kober, College, dan Perguruan Tinggi ikut dihitung sebagai sekolah karena seluruhnya berperan sebagai lembaga pendidikan."
        />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Sekolah per Jenjang</h3>
                <p class="mt-1 text-xs text-slate-500">
                    Dikelompokkan per status sekolah, negeri dan swasta.
                </p>
                <div class="mt-4 h-96">
                    <canvas data-grafik="sekolah" role="img" aria-label="Grafik batang jumlah sekolah per jenjang dan status"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Murid per Jenjang</h3>
                <p class="mt-1 text-xs text-slate-500">
                    Jenjang diurutkan mengikuti urutan pendidikan formal.
                </p>
                <div class="mt-4 h-96">
                    <canvas data-grafik="murid" role="img" aria-label="Grafik batang jumlah murid per jenjang"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Guru per Status Sekolah</h3>
                <p class="mt-1 text-xs text-slate-500">
                    Sumber hanya memisahkan guru sekolah negeri dan swasta, tanpa rincian per
                    jenjang.
                </p>
                <div class="mt-4 h-64">
                    <canvas data-grafik="guru" role="img" aria-label="Grafik lingkaran jumlah guru sekolah negeri dan swasta"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Total Murid dan Guru</h3>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                        <dt class="text-slate-600">Total murid</dt>
                        <dd class="angka-tabular text-xl font-semibold text-slate-900">
                            {{ number_format($totalMurid, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                        <dt class="text-slate-600">Total guru</dt>
                        <dd class="angka-tabular text-xl font-semibold text-slate-900">
                            {{ number_format($totalGuru, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-slate-600">Rasio murid per guru</dt>
                        <dd class="angka-tabular text-xl font-semibold text-slate-900">
                            {{ $totalGuru > 0 ? number_format($totalMurid / $totalGuru, 1, ',', '.') : '—' }}
                        </dd>
                    </div>
                </dl>

                <p class="mt-4 text-xs leading-relaxed text-slate-500">
                    Rasio ini hanya gambaran kasar. Data guru di sumber tidak dipecah per jenjang,
                    sehingga rasionya tidak bisa dibandingkan langsung dengan data pendidikan
                    nasional.
                </p>
            </div>
        </div>
    </section>

    {{-- Administrasi kependudukan --}}
    <section aria-labelledby="kependudukan" class="mb-12">
        <x-judul-bagian
            id="kependudukan"
            judul="Administrasi Kependudukan"
            keterangan="Data akta kelahiran dan kematian Kecamatan Cicalengka. Persentase kepemilikan akta dihitung ulang dari jumlah kelahiran dan jumlah akta yang sudah dimiliki, bukan diambil dari angka yang tercetak."
        />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Akta Kelahiran</h3>
                <p class="mt-1 text-xs text-slate-500">
                    Kelahiran yang wajib terdaftar, dikurangi yang belum memiliki akta.
                </p>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                        <dt class="text-slate-600">Kelahiran wajib terdaftar</dt>
                        <dd class="angka-tabular font-semibold text-slate-900">
                            {{ number_format($aktaKelahiran['wajib'], 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                        <dt class="text-slate-600">Sudah memiliki akta</dt>
                        <dd class="angka-tabular font-semibold text-dashboard-700">
                            {{ number_format($aktaKelahiran['memiliki'], 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                        <dt class="text-slate-600">Belum memiliki akta</dt>
                        <dd class="angka-tabular font-semibold text-amber-700">
                            {{ number_format($aktaKelahiran['belum'], 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-slate-600">Persentase sudah memiliki akta</dt>
                        <dd class="angka-tabular font-semibold text-slate-900">
                            {{ number_format($aktaKelahiran['persen_memiliki'], 2, ',', '.') }}%
                        </dd>
                    </div>
                </dl>

                <div class="mt-5 h-56">
                    <canvas data-grafik="akta" role="img" aria-label="Grafik batang kelahiran yang sudah dan belum memiliki akta"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Akta Kematian</h3>
                <p class="mt-1 text-xs text-slate-500">
                    Jumlah warga yang tercatat telah meninggal di wilayah kecamatan.
                </p>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                        <dt class="text-slate-600">Laki-laki</dt>
                        <dd class="angka-tabular font-semibold text-slate-900">
                            {{ number_format($aktaKematian['laki_laki'], 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                        <dt class="text-slate-600">Perempuan</dt>
                        <dd class="angka-tabular font-semibold text-slate-900">
                            {{ number_format($aktaKematian['perempuan'], 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-slate-600">Total</dt>
                        <dd class="angka-tabular text-xl font-semibold text-slate-900">
                            {{ number_format($aktaKematian['total'], 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-slate-100 pt-5">
                    <p class="text-sm font-medium text-slate-900">Fasilitas dan layanan</p>

                    <dl class="mt-3 space-y-3 text-sm">
                        <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                            <dt class="text-slate-600">Jalan status desa</dt>
                            <dd class="angka-tabular font-semibold text-slate-900">
                                {{ number_format($fasilitas['jalan'], 0, ',', '.') }} ruas
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                            <dt class="text-slate-600">Panjang jalan</dt>
                            <dd class="angka-tabular font-semibold text-slate-900">
                                {{ number_format($fasilitas['panjangJalan'], 1, ',', '.') }} km
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                            <dt class="text-slate-600">Pasar</dt>
                            <dd class="angka-tabular font-semibold text-slate-900">
                                {{ number_format($fasilitas['pasar'], 0, ',', '.') }} pasar
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-slate-600">Sungai</dt>
                            <dd class="angka-tabular font-semibold text-slate-900">
                                {{ number_format($fasilitas['sungai'], 0, ',', '.') }} sungai
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Pemerintahan</h3>
                <p class="mt-1 text-xs text-slate-500">Pegawai pemerintahan menurut jenis dan kelamin.</p>

                <table class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                            <th scope="col" class="pb-2 font-medium">Jenis</th>
                            <th scope="col" class="pb-2 text-right font-medium">Laki-laki</th>
                            <th scope="col" class="pb-2 text-right font-medium">Perempuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pemerintahan as $baris)
                            <tr>
                                <th scope="row" class="py-2 text-left font-normal text-slate-700">{{ $baris->jenis }}</th>
                                <td class="angka-tabular py-2 text-right">{{ number_format($baris->laki_laki, 0, ',', '.') }}</td>
                                <td class="angka-tabular py-2 text-right">{{ number_format($baris->perempuan, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-slate-400">Data tidak tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Makan Bergizi Gratis</h3>
                <p class="mt-1 text-xs text-slate-500">Penerima manfaat program MBG.</p>

                <table class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                            <th scope="col" class="pb-2 font-medium">Penerima</th>
                            <th scope="col" class="pb-2 text-right font-medium">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($mbg as $baris)
                            <tr>
                                <th scope="row" class="py-2 text-left font-normal text-slate-700">{{ $baris->jenis }}</th>
                                <td class="angka-tabular py-2 text-right">
                                    @if ($baris->jumlah === null)
                                        <span class="nilai-kosong">tidak tersedia</span>
                                    @else
                                        {{ number_format($baris->jumlah, 0, ',', '.') }} {{ $baris->satuan }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-4 text-center text-slate-400">Data tidak tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Statistik per desa --}}
    <section aria-labelledby="desa" class="mb-12">
        <x-judul-bagian
            id="desa"
            judul="Statistik per Desa"
            keterangan="Kelahiran yang wajib terdaftar dan persentase akta yang sudah dimiliki di setiap desa. Luas wilayah desa hanya terisi sebagian, karena sumber cetakan tidak mencantumkan luas untuk semua desa."
        />

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                            <th scope="col" class="px-4 py-3 font-medium">Desa</th>
                            <th scope="col" class="px-4 py-3 text-right font-medium">Luas wilayah</th>
                            <th scope="col" class="px-4 py-3 text-right font-medium">Kelahiran wajib</th>
                            <th scope="col" class="px-4 py-3 text-right font-medium">Sudah punya akta</th>
                            <th scope="col" class="px-4 py-3 text-right font-medium">Persentase</th>
                            <th scope="col" class="px-4 py-3 font-medium">Potensi</th>
                            <th scope="col" class="px-4 py-3 text-right font-medium">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($sebaranDesa as $baris)
                            @php($urlDesa = route('desa', ['slug' => $baris->desa->slug, 'tahun' => $tahun->tahun]))
                            <tr class="hover:bg-slate-50">
                                <th scope="row" class="px-4 py-3 text-left font-medium text-slate-900">
                                    <a href="{{ $urlDesa }}" class="hover:text-dashboard-700 hover:underline">
                                        {{ $baris->desa->nama }}
                                    </a>
                                </th>
                                <td class="angka-tabular px-4 py-3 text-right">
                                    @if ($baris->luas === null)
                                        <span class="nilai-kosong">tidak tersedia</span>
                                    @else
                                        {{ number_format($baris->luas, 2, ',', '.') }} km²
                                    @endif
                                </td>
                                <td class="angka-tabular px-4 py-3 text-right">
                                    @if ($baris->wajib === null)
                                        <span class="nilai-kosong">tidak tersedia</span>
                                    @else
                                        {{ number_format($baris->wajib, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="angka-tabular px-4 py-3 text-right">
                                    @if ($baris->memiliki === null)
                                        <span class="nilai-kosong">tidak tersedia</span>
                                    @else
                                        {{ number_format($baris->memiliki, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="angka-tabular px-4 py-3 text-right">
                                    @if ($baris->persenMemiliki === null)
                                        <span class="nilai-kosong">tidak tersedia</span>
                                    @else
                                        <span @class([
                                            'inline-flex rounded-full px-2 py-0.5 text-xs font-medium',
                                            'bg-dashboard-100 text-dashboard-800' => $baris->persenMemiliki >= 50,
                                            'bg-amber-100 text-amber-800' => $baris->persenMemiliki < 50,
                                        ])>
                                            {{ number_format($baris->persenMemiliki, 2, ',', '.') }}%
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    @forelse ($baris->potensi as $item)
                                        <span class="mb-1 mr-1 inline-block rounded bg-slate-100 px-2 py-0.5 text-xs">
                                            {{ $item }}
                                        </span>
                                    @empty
                                        <span class="nilai-kosong text-xs">tidak tersedia</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a
                                        href="{{ $urlDesa }}"
                                        class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50"
                                    >
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Potensi desa --}}
    <section aria-labelledby="potensi" class="mb-12">
        <x-judul-bagian
            id="potensi"
            judul="Potensi Desa"
            keterangan="Jumlah desa yang memiliki masing-masing jenis potensi. Satu desa dapat tercatat memiliki lebih dari satu jenis potensi."
        />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="h-72">
                    <canvas data-grafik="potensi" role="img" aria-label="Grafik batang jumlah desa per jenis potensi"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Rincian</h3>

                <table class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                            <th scope="col" class="pb-2 font-medium">Jenis potensi</th>
                            <th scope="col" class="pb-2 text-right font-medium">Jumlah desa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($potensiPerKategori as $baris)
                            <tr>
                                <th scope="row" class="py-2 text-left font-normal text-slate-700">{{ $baris->kategori }}</th>
                                <td class="angka-tabular py-2 text-right font-medium">
                                    {{ number_format($baris->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p class="mt-4 text-xs leading-relaxed text-slate-500">
                    Sumber hanya menandai jenis potensi setiap desa, tanpa nilai ekonomi atau
                    keterangan tambahan. Karena itu tabel ini belum bisa dipakai untuk
                    membandingkan efektivitas ekonomi antar desa.
                </p>
            </div>
        </div>
    </section>

    {{-- Catatan verifikasi --}}
    <section aria-labelledby="catatan-verifikasi" class="mb-12">
        <x-judul-bagian
            id="catatan-verifikasi"
            judul="Catatan Verifikasi"
            keterangan="Tempat mencatat angka yang tidak konsisten atau tidak lengkap pada sumber cetakan. Data tidak diubah diam-diam supaya asalnya tetap bisa ditelusuri."
        />

        <div class="space-y-3">
            @forelse ($catatanVerifikasi as $catatan)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                        {{ $catatan->sumber }}
                    </p>
                    <p class="mt-1.5 text-sm leading-relaxed text-amber-900">{{ $catatan->pesan }}</p>
                </div>
            @empty
                <div class="rounded-xl border border-dashboard-200 bg-dashboard-50 p-4">
                    <p class="text-sm text-dashboard-900">
                        Tidak ada ketidaksesuaian yang terdeteksi pada data tahun {{ $tahun->tahun }}.
                    </p>
                </div>
            @endforelse

            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Keterangan umum
                </p>
                <p class="mt-1.5 text-sm leading-relaxed text-slate-600">
                    Seluruh angka berasal dari publikasi cetakan
                    <span class="font-medium">Cicalengka Dalam Angka {{ $tahun->tahun }}</span>.
                    Nilai yang tidak tercantum pada sumber ditampilkan sebagai
                    <span class="nilai-kosong">tidak tersedia</span> dan bukan diisi nol, sebab
                    nol berarti benar-benar tidak ada, sedangkan kosong berarti tidak diketahui.
                </p>
            </div>
        </div>
    </section>
    @endif
@endsection

@push('skrip')
    {{-- Data grafik hanya dikirim kalau ada tahun, karena setiap grafik diisi dari agregasi tahun tersebut. --}}
    @if ($tahun !== null)
        <script>
            // Data grafik dikirim dari server supaya angka di grafik dan angka
            // di teks selalu berasal dari sumber yang sama.
            window.__dataDashboard = @js([
                'sekolah' => [
                    'jenjang' => $sekolahPerJenjang->pluck('jenjang')->unique()->values(),
                    'negeri' => $sekolahPerJenjang->where('jenis', 'like', '%Negeri%')->pluck('total', 'jenjang'),
                    'swasta' => $sekolahPerJenjang->where('jenis', 'like', '%Swasta%')->pluck('total', 'jenjang'),
                ],
                'murid' => [
                    'jenjang' => $muridPerJenjang->pluck('jenjang'),
                    'murid' => $muridPerJenjang->pluck('murid'),
                ],
                'guru' => [
                    'label' => $guruPerJenis->pluck('jenis'),
                    'total' => $guruPerJenis->pluck('total'),
                ],
                'akta' => [
                    'label' => ['Sudah memiliki akta', 'Belum memiliki akta'],
                    'total' => [$aktaKelahiran['memiliki'], $aktaKelahiran['belum']],
                ],
                'potensi' => [
                    'label' => $potensiPerKategori->pluck('kategori'),
                    'total' => $potensiPerKategori->pluck('jumlah'),
                ],
            ]);
        </script>
    @endif
@endpush