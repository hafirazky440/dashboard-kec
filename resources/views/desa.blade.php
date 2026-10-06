@extends('layouts.public')

@section('judul', $desa->nama)

@section('konten')
    {{--
        Sama seperti dashboard utama: kalau belum ada data tahun sama sekali,
        halaman ini harus tetap informatif, bukan melempar error.
    --}}
    @if ($tahun === null)
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <h1 class="text-xl font-semibold text-slate-900">Belum ada data untuk {{ $desa->nama }}</h1>
            <p class="mx-auto mt-2 max-w-lg text-sm text-slate-600">
                Database belum memuat data tahun apa pun, jadi belum ada statistik yang bisa
                ditampilkan untuk desa ini.
            </p>
            <a
                href="{{ route('dashboard') }}"
                class="mt-4 inline-flex items-center rounded-lg bg-dashboard-600 px-4 py-2 text-sm font-medium text-white"
            >
                Kembali ke dashboard
            </a>
        </div>
    @else
        {{-- Navigasi kembali --}}
    <nav aria-label="Navigasi" class="mb-6">
        <a
            href="{{ route('dashboard', ['tahun' => $tahun->tahun]) }}"
            class="inline-flex items-center gap-1.5 text-sm text-slate-600 hover:text-dashboard-700"
        >
            <span aria-hidden="true">&larr;</span>
            Kembali ke dashboard
        </a>
    </nav>

    {{-- Kepala halaman --}}
    <div class="mb-8">
        <p class="text-sm font-medium text-dashboard-700">Statistik per Desa</p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
            {{ $desa->nama }}
        </h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Data administrasi kependudukan dan luas wilayah Desa {{ $desa->nama }} pada
            publikasi <span class="font-medium text-slate-800">Cicalengka Dalam Angka {{ $tahun->tahun }}</span>.
        </p>
    </div>

    {{-- Luas wilayah dan potensi --}}
    <section aria-labelledby="profil-desa" class="mb-12">
        <x-judul-bagian id="profil-desa" judul="Profil Desa" />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-kartu-statistik
                label="Luas wilayah"
                :nilai="$geografi?->luas_km2 !== null ? number_format((float) $geografi->luas_km2, 2, ',', '.').' km²' : null"
                keterangan="Hanya sebagian desa yang luasnya tercantum pada sumber cetakan"
            />

            <x-kartu-statistik
                label="Urutan desa"
                :nilai="(string) $desa->urutan"
                keterangan="Sesuai urutan resmi Desa di Kecamatan Cicalengka"
            />

            <x-kartu-statistik
                label="Kelahiran wajib terdaftar"
                :nilai="$aktaKelahiran?->wajib_total !== null ? number_format((int) $aktaKelahiran->wajib_total, 0, ',', '.') : null"
                keterangan="Jumlah kelahiran di desa ini yang wajib memiliki akta"
            />

            <x-kartu-statistik
                label="Persentase sudah punya akta"
                :nilai="$aktaKelahiran?->persen_memiliki !== null ? number_format($aktaKelahiran->persen_memiliki, 2, ',', '.').'%' : null"
                keterangan="Dihitung ulang dari rincian jumlah akta"
            />
        </div>

        <div class="mt-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="font-medium text-slate-900">Potensi Desa {{ $desa->nama }}</h3>

            <div class="mt-3 flex flex-wrap gap-2">
                @forelse ($potensi as $item)
                    <span class="rounded-full bg-dashboard-100 px-3 py-1 text-sm text-dashboard-800">
                        {{ $item }}
                    </span>
                @empty
                    <span class="nilai-kosong text-sm">Jenis potensi tidak tercatat pada sumber cetakan.</span>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Administrasi kependudukan --}}
    <section aria-labelledby="akta-desa" class="mb-12">
        <x-judul-bagian
            id="akta-desa"
            judul="Administrasi Kependudukan"
            keterangan="Rincian akta kelahiran dan kematian Desa {{ $desa->nama }}."
        />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Akta Kelahiran</h3>

                @if ($aktaKelahiran === null)
                    <p class="nilai-kosong mt-4 text-sm">Data akta kelahiran tidak tersedia untuk tahun ini.</p>
                @else
                    @if ($aktaKelahiran->totalKonsisten() === false)
                        <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                            <p class="text-xs leading-relaxed text-amber-900">
                                Total dan rincian pada sumber cetakan tidak cocok untuk desa ini
                                ({{ $aktaKelahiran->daftarSelisih()->implode(', ') }}). Angka
                                ditampilkan apa adanya, lihat bagian Catatan Verifikasi pada dashboard.
                            </p>
                        </div>
                    @endif

                    <table class="mt-4 w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                                <th scope="col" class="pb-2 font-medium">Kelompok</th>
                                <th scope="col" class="pb-2 text-right font-medium">Laki-laki</th>
                                <th scope="col" class="pb-2 text-right font-medium">Perempuan</th>
                                <th scope="col" class="pb-2 text-right font-medium">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach (['wajib' => 'Wajib terdaftar', 'memiliki' => 'Sudah memiliki', 'belum' => 'Belum memiliki'] as $kunci => $judul)
                                <tr>
                                    <th scope="row" class="py-2 text-left font-normal text-slate-700">{{ $judul }}</th>
                                    <td class="angka-tabular py-2 text-right">{{ number_format($aktaKelahiran->{$kunci.'_laki_laki'}, 0, ',', '.') }}</td>
                                    <td class="angka-tabular py-2 text-right">{{ number_format($aktaKelahiran->{$kunci.'_perempuan'}, 0, ',', '.') }}</td>
                                    <td class="angka-tabular py-2 text-right font-medium">{{ number_format($aktaKelahiran->{$kunci.'_total'}, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Akta Kematian</h3>

                @if ($aktaKematian === null)
                    <p class="nilai-kosong mt-4 text-sm">Data akta kematian tidak tersedia untuk tahun ini.</p>
                @else
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                            <dt class="text-slate-600">Laki-laki</dt>
                            <dd class="angka-tabular font-semibold text-slate-900">
                                {{ number_format($aktaKematian->laki_laki, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 border-b border-slate-100 pb-3">
                            <dt class="text-slate-600">Perempuan</dt>
                            <dd class="angka-tabular font-semibold text-slate-900">
                                {{ number_format($aktaKematian->perempuan, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-slate-600">Total</dt>
                            <dd class="angka-tabular text-xl font-semibold text-slate-900">
                                {{ number_format($aktaKematian->total, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>
                @endif
            </div>
        </div>
    </section>

    {{-- Data kecamatan di sekitar desa --}}
    <section aria-labelledby="sekitar" class="mb-12">
        <x-judul-bagian
            id="sekitar"
            judul="Sarana di Sekitar Desa"
            keterangan="Sumber cetakan hanya mencatat data ini secara tingkat kecamatan, bukan per desa. Karena itu tabel di bawah berlaku untuk seluruh Kecamatan Cicalengka, bukan khusus Desa {{ $desa->nama }}."
        />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Sekolah</h3>

                <table class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                            <th scope="col" class="pb-2 font-medium">Jenjang</th>
                            <th scope="col" class="pb-2 font-medium">Status</th>
                            <th scope="col" class="pb-2 text-right font-medium">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($sekolahTerdekat as $baris)
                            <tr>
                                <th scope="row" class="py-2 text-left font-normal text-slate-700">{{ $baris->jenjang }}</th>
                                <td class="py-2 text-slate-600">{{ $baris->jenis }}</td>
                                <td class="angka-tabular py-2 text-right font-medium">
                                    {{ number_format($baris->total, 0, ',', '.') }}
                                </td>
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
                <h3 class="font-medium text-slate-900">Jalan</h3>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                                <th scope="col" class="pb-2 font-medium">Nama jalan</th>
                                <th scope="col" class="pb-2 font-medium">Tingkat</th>
                                <th scope="col" class="pb-2 text-right font-medium">Panjang</th>
                                <th scope="col" class="pb-2 font-medium">Batas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($jalanSekitar as $baris)
                                <tr>
                                    <th scope="row" class="py-2 text-left font-normal text-slate-700">{{ $baris->nama }}</th>
                                    <td class="py-2 text-slate-600">{{ $baris->tingkat }}</td>
                                    <td class="angka-tabular py-2 text-right">
                                        @if ($baris->panjang_km === null)
                                            <span class="nilai-kosong">tidak tersedia</span>
                                        @else
                                            {{ number_format((float) $baris->panjang_km, 1, ',', '.') }} km
                                        @endif
                                    </td>
                                    <td class="py-2 text-slate-600">{{ $baris->batas ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-slate-400">Data tidak tersedia</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Pasar</h3>

                <table class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                            <th scope="col" class="pb-2 font-medium">Nama</th>
                            <th scope="col" class="pb-2 font-medium">Lokasi</th>
                            <th scope="col" class="pb-2 font-medium">Hari</th>
                            <th scope="col" class="pb-2 text-right font-medium">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pasarSekitar as $baris)
                            <tr>
                                <th scope="row" class="py-2 text-left font-normal text-slate-700">{{ $baris->nama }}</th>
                                <td class="py-2 text-slate-600">{{ $baris->lokasi ?: '—' }}</td>
                                <td class="py-2 text-slate-600">{{ $baris->hari_operasi ?: '—' }}</td>
                                <td class="angka-tabular py-2 text-right">
                                    @if ($baris->jumlah === null)
                                        <span class="nilai-kosong">tidak tersedia</span>
                                    @else
                                        {{ number_format($baris->jumlah, 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400">Data tidak tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium text-slate-900">Sungai</h3>

                <table class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wide text-slate-500">
                            <th scope="col" class="pb-2 font-medium">Nama sungai</th>
                            <th scope="col" class="pb-2 font-medium">Status</th>
                            <th scope="col" class="pb-2 text-right font-medium">Panjang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($sungaiSekitar as $baris)
                            <tr>
                                <th scope="row" class="py-2 text-left font-normal text-slate-700">{{ $baris->nama }}</th>
                                <td class="py-2 text-slate-600">{{ $baris->status ?: '—' }}</td>
                                <td class="angka-tabular py-2 text-right">
                                    @if ($baris->panjang_km === null)
                                        <span class="nilai-kosong">tidak tersedia</span>
                                    @else
                                        {{ number_format((float) $baris->panjang_km, 1, ',', '.') }} km
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-slate-400">Data tidak tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    @endif
@endsection