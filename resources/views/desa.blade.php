@extends('layouts.public')

@section('judul', $desa->nama)

@section('konten')
    {{-- Kepala halaman --}}
    <header class="mb-space-xl">
        <a
            href="{{ route('dashboard') }}"
            class="inline-flex items-center gap-1.5 font-label-md text-label-md text-secondary transition hover:text-primary"
        >
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke dashboard
        </a>

        <div class="mt-space-md flex flex-wrap items-end justify-between gap-gutter">
            <div>
                <p class="font-label-sm text-label-sm uppercase tracking-wider text-secondary">
                    Statistik per Desa
                </p>
                <h1 class="mt-1 font-headline-xl text-headline-xl-mobile tracking-tight text-primary sm:text-headline-xl">
                    {{ $desa->nama }}
                </h1>
                <p class="mt-2 max-w-3xl font-body-md text-body-md leading-relaxed text-on-surface-variant">
                    Data administrasi kependudukan dan luas wilayah Desa {{ $desa->nama }} pada
                    publikasi <span class="font-semibold text-on-surface">Cicalengka Dalam Angka</span>.
                </p>
            </div>
        </div>
    </header>

    {{-- Luas wilayah dan potensi --}}
    <section aria-labelledby="profil-desa" class="mb-space-xl">
        <x-judul-bagian id="profil-desa" judul="Profil Desa" ikon="location_on" />

        <div class="grid grid-cols-1 gap-gutter sm:grid-cols-2 lg:grid-cols-4">
            <x-kartu-statistik
                label="Luas wilayah"
                ikon="square_foot"
                aksen
                :nilai="$luas !== null ? number_format((float) $luas, 2, ',', '.').' km²' : null"
                keterangan="Hanya sebagian desa yang luasnya tercantum pada sumber cetakan"
            />

            <x-kartu-statistik
                label="Potensi utama"
                ikon="landscape"
                :nilai="$potensi"
                keterangan="Jenis potensi sesuai sumber cetakan"
            />

            <x-kartu-statistik
                label="Kelahiran wajib terdaftar"
                ikon="child_care"
                :nilai="$aktaKelahiran?->wajib_total !== null ? number_format((int) $aktaKelahiran->wajib_total, 0, ',', '.') : null"
                keterangan="Jumlah kelahiran di desa ini yang wajib memiliki akta"
            />

            <x-kartu-statistik
                label="Persentase sudah punya akta"
                ikon="percent"
                :nilai="$aktaKelahiran?->persen_memiliki !== null ? number_format($aktaKelahiran->persen_memiliki, 2, ',', '.').'%' : null"
                keterangan="Dihitung ulang dari rincian jumlah akta"
            />
        </div>

        <div class="mt-gutter rounded-[20px] border border-outline-variant/40 bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
            <h3 class="font-headline-md text-headline-md text-primary">Potensi Desa {{ $desa->nama }}</h3>

            <div class="mt-space-sm flex flex-wrap gap-2">
                @php
                    // Kolom potensi berisi gabungan kata, misalnya "Pertanian dan
                    // UMKM". Nilainya dipecah pada kata "dan" supaya tiap jenis
                    // tampil sebagai satu label.
                    $daftarPotensi = $potensi !== null
                        ? collect(preg_split('/\s+dan\s+/iu', $potensi) ?: [])
                            ->map(fn ($item) => trim($item))
                            ->filter()
                        : collect();
                @endphp

                @forelse ($daftarPotensi as $item)
                    <span class="rounded-full bg-tertiary-fixed px-3 py-1 font-body-sm text-body-sm font-medium text-on-tertiary-fixed">
                        {{ $item }}
                    </span>
                @empty
                    <span class="nilai-kosong font-body-sm text-body-sm">Jenis potensi tidak tercatat pada sumber cetakan.</span>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Administrasi kependudukan --}}
    <section aria-labelledby="akta-desa" class="mb-space-xl">
        <x-judul-bagian
            id="akta-desa"
            judul="Administrasi Kependudukan"
            ikon="description"
            keterangan="Rincian akta kelahiran dan kematian Desa {{ $desa->nama }}."
        />

        <div class="grid grid-cols-1 gap-gutter lg:grid-cols-2">
            <div class="rounded-[20px] border border-outline-variant/40 bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                <h3 class="font-headline-md text-headline-md text-primary">Akta Kelahiran</h3>

                @if ($aktaKelahiran === null)
                    <p class="nilai-kosong mt-space-md font-body-sm text-body-sm">Data akta kelahiran tidak tersedia untuk desa ini.</p>
                @else
                    @if ($aktaKelahiran->totalKonsisten() === false)
                        <div class="mt-space-sm flex gap-2 rounded-xl border border-error-container bg-error-container/50 px-3 py-2.5">
                            <span class="material-symbols-outlined text-[18px] text-error">warning</span>
                            <p class="font-body-sm text-body-sm leading-relaxed text-on-error-container">
                                Total dan rincian pada sumber cetakan tidak cocok untuk desa ini
                                ({{ $aktaKelahiran->daftarSelisih()->implode(', ') }}). Angka
                                ditampilkan apa adanya, lihat bagian Catatan Verifikasi pada dashboard.
                            </p>
                        </div>
                    @endif

                    <div class="mt-space-md overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-outline-variant/50 font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                                    <th scope="col" class="pb-2 font-semibold">Kelompok</th>
                                    <th scope="col" class="pb-2 text-right font-semibold">Laki-laki</th>
                                    <th scope="col" class="pb-2 text-right font-semibold">Perempuan</th>
                                    <th scope="col" class="pb-2 text-right font-semibold">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/30 font-body-md text-body-md">
                                @foreach (['wajib' => 'Wajib terdaftar', 'memiliki' => 'Sudah memiliki', 'belum' => 'Belum memiliki'] as $kunci => $judul)
                                    <tr class="hover:bg-surface-container-low">
                                        <th scope="row" class="py-2.5 text-left font-normal text-on-surface-variant">{{ $judul }}</th>
                                        <td class="angka-tabular py-2.5 text-right text-on-surface">{{ number_format($aktaKelahiran->{$kunci.'_laki_laki'}, 0, ',', '.') }}</td>
                                        <td class="angka-tabular py-2.5 text-right text-on-surface">{{ number_format($aktaKelahiran->{$kunci.'_perempuan'}, 0, ',', '.') }}</td>
                                        <td class="angka-tabular py-2.5 text-right font-semibold text-primary">{{ number_format($aktaKelahiran->{$kunci.'_total'}, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="rounded-[20px] border border-outline-variant/40 bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                <h3 class="font-headline-md text-headline-md text-primary">Akta Kematian</h3>

                @if ($aktaKematian === null)
                    <p class="nilai-kosong mt-space-md font-body-sm text-body-sm">Data akta kematian tidak tersedia untuk desa ini.</p>
                @else
                    <dl class="mt-space-md space-y-3 font-body-md text-body-md">
                        <div class="flex items-baseline justify-between gap-4 border-b border-outline-variant/30 pb-3">
                            <dt class="text-on-surface-variant">Laki-laki</dt>
                            <dd class="angka-tabular font-semibold text-on-surface">
                                {{ number_format($aktaKematian->laki_laki, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 border-b border-outline-variant/30 pb-3">
                            <dt class="text-on-surface-variant">Perempuan</dt>
                            <dd class="angka-tabular font-semibold text-on-surface">
                                {{ number_format($aktaKematian->perempuan, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-on-surface-variant">Total</dt>
                            <dd class="angka-tabular text-headline-lg font-semibold text-primary">
                                {{ number_format($aktaKematian->total, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>
                @endif
            </div>
        </div>
    </section>

    {{-- Data kecamatan di sekitar desa --}}
    <section aria-labelledby="sekitar" class="mb-space-xl">
        <x-judul-bagian
            id="sekitar"
            judul="Sarana di Sekitar Desa"
            ikon="map"
            keterangan="Sumber cetakan hanya mencatat data ini secara tingkat kecamatan, bukan per desa. Karena itu tabel di bawah berlaku untuk seluruh Kecamatan Cicalengka, bukan khusus Desa {{ $desa->nama }}."
        />

        <div class="grid grid-cols-1 gap-gutter lg:grid-cols-2">
            <div class="rounded-[20px] border border-outline-variant/40 bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                <h3 class="font-headline-md text-headline-md text-primary">Sekolah</h3>

                <div class="mt-space-md overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-outline-variant/50 font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                                <th scope="col" class="pb-2 font-semibold">Jenis</th>
                                <th scope="col" class="pb-2 text-right font-semibold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/30 font-body-md text-body-md">
                            @forelse ($sekolah as $baris)
                                <tr class="hover:bg-surface-container-low">
                                    <th scope="row" class="py-2.5 text-left font-normal text-on-surface-variant">{{ $baris->jenis }}</th>
                                    <td class="angka-tabular py-2.5 text-right font-semibold text-primary">
                                        {{ number_format($baris->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 text-center nilai-kosong">Data tidak tersedia</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-[20px] border border-outline-variant/40 bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                <h3 class="font-headline-md text-headline-md text-primary">Jalan</h3>

                <div class="mt-space-md overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-outline-variant/50 font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                                <th scope="col" class="pb-2 font-semibold">Nama jalan</th>
                                <th scope="col" class="pb-2 font-semibold">Status</th>
                                <th scope="col" class="pb-2 text-right font-semibold">Panjang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/30 font-body-md text-body-md">
                            @forelse ($jalan as $baris)
                                <tr class="hover:bg-surface-container-low">
                                    <th scope="row" class="py-2.5 text-left font-normal text-on-surface-variant">{{ $baris->nama }}</th>
                                    <td class="py-2.5 text-on-surface-variant">{{ $baris->status ?: '—' }}</td>
                                    <td class="angka-tabular py-2.5 text-right text-on-surface">
                                        @if ($baris->panjang_km === null)
                                            <span class="nilai-kosong">tidak tersedia</span>
                                        @else
                                            {{ number_format((float) $baris->panjang_km, 1, ',', '.') }} km
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center nilai-kosong">Data tidak tersedia</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-[20px] border border-outline-variant/40 bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                <h3 class="font-headline-md text-headline-md text-primary">Sarana Perdagangan</h3>

                <div class="mt-space-md overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-outline-variant/50 font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                                <th scope="col" class="pb-2 font-semibold">Nama</th>
                                <th scope="col" class="pb-2 font-semibold">Jenis</th>
                                <th scope="col" class="pb-2 font-semibold">Lokasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/30 font-body-md text-body-md">
                            @forelse ($sarana as $baris)
                                <tr class="hover:bg-surface-container-low">
                                    <th scope="row" class="py-2.5 text-left font-normal text-on-surface-variant">{{ $baris->nama }}</th>
                                    <td class="py-2.5 text-on-surface-variant">{{ $baris->jenis ?: '—' }}</td>
                                    <td class="py-2.5 text-on-surface-variant">{{ $baris->lokasi ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center nilai-kosong">Data tidak tersedia</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-[20px] border border-outline-variant/40 bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                <h3 class="font-headline-md text-headline-md text-primary">Pengairan</h3>

                <div class="mt-space-md overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-outline-variant/50 font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                                <th scope="col" class="pb-2 font-semibold">Nama</th>
                                <th scope="col" class="pb-2 font-semibold">Kewenangan</th>
                                <th scope="col" class="pb-2 text-right font-semibold">Panjang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/30 font-body-md text-body-md">
                            @forelse ($pengairan as $baris)
                                <tr class="hover:bg-surface-container-low">
                                    <th scope="row" class="py-2.5 text-left font-normal text-on-surface-variant">{{ $baris->nama }}</th>
                                    <td class="py-2.5 text-on-surface-variant">{{ $baris->kewenangan ?: '—' }}</td>
                                    <td class="angka-tabular py-2.5 text-right text-on-surface">
                                        @if ($baris->panjang_km === null)
                                            <span class="nilai-kosong">tidak tersedia</span>
                                        @else
                                            {{ number_format((float) $baris->panjang_km, 1, ',', '.') }} km
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center nilai-kosong">Data tidak tersedia</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
