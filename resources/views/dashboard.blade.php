@extends('layouts.public')

@section('judul', 'Dashboard Statistik')

@section('konten')
    @if ($tahun === null)
        {{-- Empty state: database belum punya data tahun sama sekali. --}}
        <div class="rounded-[20px] border border-dashed border-outline-variant bg-surface-container-lowest p-10 text-center">
            <h1 class="font-headline-lg text-headline-lg text-primary">Belum ada data statistik</h1>
            <p class="mx-auto mt-2 max-w-lg font-body-md text-body-md text-on-surface-variant">
                Dashboard ini menampilkan data dari publikasi Cicalengka Dalam Angka. Database
                belum memuat satu pun data tahun, jadi belum ada yang bisa ditampilkan.
            </p>
            <p class="mt-4 font-body-md text-body-md text-on-surface-variant">
                Jalankan <code class="rounded bg-surface-container-low px-1.5 py-0.5 font-body-sm">php artisan migrate --seed</code>
                untuk mengisi data awal, atau tambahkan lewat panel admin di
                <a href="/admin" class="font-semibold text-secondary underline">/admin</a>.
            </p>
        </div>
    @else
        @php
            // Ringkasan angka yang dipakai di beberapa tempat sekaligus.
            $p = $profil;
            $berluas = $sebaranDesa->filter(fn ($r) => $r->luas !== null)->sortBy('luas')->values();
            $tersempit = $berluas->first();
            $terluas = $berluas->last();
            $idTersempit = $tersempit?->desa->id;
            $idTerluas = $terluas?->desa->id;

            $guruNegeri = $guruPerJenis->firstWhere('jenis', 'Sekolah Negeri')?->total;
            $guruSwasta = $guruPerJenis->firstWhere('jenis', 'Sekolah Swasta')?->total;

            $dapurOperasional = $mbg->firstWhere('jenis', 'Dapur Operasional')?->jumlah;
            $dapurSiap = $mbg->firstWhere('jenis', 'Dapur SPPG Siap Operasional')?->jumlah;
            $dapurPersiapan = $mbg->firstWhere('jenis', 'Dapur SPPG Siap Persiapan')?->jumlah;
            $penerimaMbg = $mbg->firstWhere('jenis', 'Total Penerima Manfaat')?->jumlah;

            $pendudukLk = (int) ($p?->penduduk_laki_laki ?? 0);
            $pendudukPr = (int) ($p?->penduduk_perempuan ?? 0);
            $totalGender = $pendudukLk + $pendudukPr;
            $persenPr = $totalGender > 0 ? $pendudukPr / $totalGender * 100 : 0;
            $persenLk = $totalGender > 0 ? $pendudukLk / $totalGender * 100 : 0;
            $rasioGuruMurid = $totalGuru > 0 ? (int) round($totalMurid / $totalGuru) : null;

            $jalanUtama = $jalan->take(3);
            $fmt = fn ($angka) => number_format((float) $angka, 0, ',', '.');
            $fmt2 = fn ($angka) => number_format((float) $angka, 2, ',', '.');
        @endphp

        <div class="flex w-full flex-col space-y-space-lg">
            {{-- HEADER --}}
            <div id="dashboard" class="flex scroll-mt-24 flex-col justify-between gap-space-md md:flex-row md:items-center">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="font-headline-xl text-headline-xl tracking-tight text-primary">
                            Cicalengka Dalam Angka
                        </h1>
                        <span class="inline-flex items-center rounded-full bg-secondary-fixed px-3 py-1 font-label-sm text-label-sm text-on-secondary-fixed">
                            Data Tahun {{ $tahun->tahun }}
                        </span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        Dashboard Statistik Resmi Kecamatan Cicalengka, Kabupaten Bandung
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 rounded-xl bg-surface-container-lowest px-3 py-2 shadow-sm">
                        <span class="material-symbols-outlined text-[20px] text-primary">filter_alt</span>
                        <label for="filter-tahun" class="font-label-sm text-label-sm font-bold uppercase tracking-wider text-on-surface-variant">
                            Tahun:
                        </label>
                        <select
                            id="filter-tahun"
                            name="tahun"
                            onchange="this.form.submit()"
                            class="cursor-pointer bg-transparent pr-2 font-headline-md text-headline-md text-primary focus:outline-none"
                        >
                            @foreach ($daftarTahun as $item)
                                <option value="{{ $item->tahun }}" @selected(($tahun?->id ?? null) === $item->id)>
                                    {{ $item->tahun }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="flex items-center gap-2 rounded-xl bg-primary px-space-md py-2.5 font-label-md text-label-md text-on-primary shadow-sm transition-all hover:bg-primary-container active:scale-[0.98]"
                    >
                        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                        <span>Export Data / PDF</span>
                    </button>
                </div>
            </div>

            {{-- ROW 1: EMPAT KARTU STATISTIK --}}
            <div class="grid grid-cols-1 gap-gutter md:grid-cols-2 2xl:grid-cols-4">
                {{-- Total Penduduk --}}
                <div class="relative flex flex-col justify-between overflow-hidden rounded-[20px] bg-primary p-space-lg text-on-primary shadow-[0px_8px_24px_-4px_rgba(15,77,50,0.25)]">
                    <div class="pointer-events-none absolute -bottom-4 -right-4 h-32 w-32 rounded-full bg-secondary/20 blur-2xl"></div>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex min-w-0 items-center gap-2">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-primary-container text-secondary-fixed">
                                <span class="material-symbols-outlined text-[22px]">groups</span>
                            </div>
                            <span class="font-label-md text-label-md uppercase tracking-wider text-primary-fixed">Total Penduduk</span>
                        </div>
                        <span class="whitespace-nowrap rounded-full bg-secondary-fixed px-2.5 py-1 font-label-sm text-label-sm font-bold text-on-secondary-fixed">
                            Resmi {{ $tahun->tahun }}
                        </span>
                    </div>
                    <div class="my-space-md">
                        <div class="flex items-baseline gap-2">
                            @if ($p?->total_penduduk !== null)
                                <span class="angka-tabular font-stat-metric text-stat-metric font-extrabold tracking-tight text-on-primary">
                                    {{ $fmt($p->total_penduduk) }}
                                </span>
                                <span class="font-body-md text-body-md font-medium text-primary-fixed-dim">Jiwa</span>
                            @else
                                <span class="nilai-kosong text-headline-lg">Tidak tersedia</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1 bg-primary-container/40 px-space-lg py-3 font-body-sm text-body-sm text-primary-fixed -mx-space-lg -mb-space-lg">
                        <span>Perempuan: <strong class="font-bold text-on-primary">{{ $p?->penduduk_perempuan !== null ? $fmt($p->penduduk_perempuan) : '—' }}</strong></span>
                        <span class="text-primary-fixed-dim/60">•</span>
                        <span>Laki-laki: <strong class="font-bold text-on-primary">{{ $p?->penduduk_laki_laki !== null ? $fmt($p->penduduk_laki_laki) : '—' }}</strong></span>
                    </div>
                </div>

                {{-- Wilayah Administratif --}}
                <div class="flex flex-col justify-between overflow-hidden rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex min-w-0 items-center gap-2">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-surface-container-low text-primary">
                                <span class="material-symbols-outlined text-[22px]">apartment</span>
                            </div>
                            <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Wilayah Administratif</span>
                        </div>
                        <span class="whitespace-nowrap rounded-full bg-surface-container px-2.5 py-1 font-label-sm text-label-sm font-bold text-on-surface-variant">
                            Lengkap
                        </span>
                    </div>
                    <div class="my-space-md">
                        <div class="flex items-baseline gap-2">
                            <span class="angka-tabular font-stat-metric text-stat-metric font-extrabold tracking-tight text-primary">
                                {{ $p?->jumlah_desa ?? \App\Models\Desa::count() }}
                            </span>
                            <span class="font-body-md text-body-md font-medium text-on-surface-variant">Desa</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 bg-surface-container-low px-space-lg py-3 font-body-sm text-body-sm text-on-surface-variant -mx-space-lg -mb-space-lg">
                        <span class="font-semibold text-primary">{{ $p?->jumlah_dusun !== null ? $fmt($p->jumlah_dusun) : '—' }}</span> Dusun
                        <span class="text-outline-variant">•</span>
                        <span class="font-semibold text-primary">{{ $p?->jumlah_rw !== null ? $fmt($p->jumlah_rw) : '—' }}</span> RW
                        <span class="text-outline-variant">•</span>
                        <span class="font-semibold text-primary">{{ $p?->jumlah_rt !== null ? $fmt($p->jumlah_rt) : '—' }}</span> RT
                    </div>
                </div>

                {{-- Luas Wilayah --}}
                <div class="flex flex-col justify-between overflow-hidden rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex min-w-0 items-center gap-2">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-surface-container-low text-secondary">
                                <span class="material-symbols-outlined text-[22px]">map</span>
                            </div>
                            <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Luas Wilayah</span>
                        </div>
                        <span class="whitespace-nowrap rounded-full bg-secondary-fixed/30 px-2.5 py-1 font-label-sm text-label-sm font-bold text-secondary">
                            Geografi
                        </span>
                    </div>
                    <div class="my-space-md">
                        <div class="flex items-baseline gap-2">
                            @if ($p?->luas_wilayah_km2 !== null)
                                <span class="angka-tabular font-stat-metric text-stat-metric font-extrabold tracking-tight text-primary">
                                    {{ $fmt2($p->luas_wilayah_km2) }}
                                </span>
                                <span class="font-body-md text-body-md font-medium text-on-surface-variant">km²</span>
                            @else
                                <span class="nilai-kosong text-headline-lg">Tidak tersedia</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1 bg-surface-container-low px-space-lg py-3 font-body-sm text-body-sm text-on-surface-variant -mx-space-lg -mb-space-lg">
                        <span class="truncate">
                            {{ $terluas?->desa->nama ?? '—' }}:
                            <strong class="text-on-surface">{{ $terluas?->luas !== null ? $fmt2($terluas->luas).' km²' : '—' }}</strong>
                        </span>
                        <span class="text-outline-variant">•</span>
                        <span class="truncate">
                            {{ $tersempit?->desa->nama ?? '—' }}:
                            <strong class="text-on-surface">{{ $tersempit?->luas !== null ? $fmt2($tersempit->luas).' km²' : '—' }}</strong>
                        </span>
                    </div>
                </div>

                {{-- Pendidikan --}}
                <div class="flex flex-col justify-between overflow-hidden rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex min-w-0 items-center gap-2">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-surface-container-low text-tertiary">
                                <span class="material-symbols-outlined text-[22px]">school</span>
                            </div>
                            <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Pendidikan</span>
                        </div>
                        <span class="whitespace-nowrap rounded-full bg-primary-fixed px-2.5 py-1 font-label-sm text-label-sm font-bold text-on-primary-fixed-variant">
                            {{ $fmt($totalGuru) }} Guru
                        </span>
                    </div>
                    <div class="my-space-md">
                        <div class="flex items-baseline gap-2">
                            <span class="angka-tabular font-stat-metric text-stat-metric font-extrabold tracking-tight text-primary">
                                {{ $fmt($totalMurid) }}
                            </span>
                            <span class="font-body-md text-body-md font-medium text-on-surface-variant">Murid</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1 bg-surface-container-low px-space-lg py-3 font-body-sm text-body-sm text-on-surface-variant -mx-space-lg -mb-space-lg">
                        <span>Guru Negeri: <strong class="text-on-surface">{{ $guruNegeri !== null ? $fmt($guruNegeri) : '—' }}</strong></span>
                        <span class="text-outline-variant">•</span>
                        <span>Swasta: <strong class="text-on-surface">{{ $guruSwasta !== null ? $fmt($guruSwasta) : '—' }}</strong></span>
                    </div>
                </div>
            </div>

            {{-- ROW 2: GRAFIK --}}
            <div class="grid grid-cols-1 gap-gutter xl:grid-cols-12">
                {{-- Murid per jenjang --}}
                <div id="murid" class="flex scroll-mt-24 flex-col justify-between rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)] xl:col-span-7">
                    <div>
                        <div class="mb-space-md flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                            <div>
                                <h2 class="font-headline-md text-headline-md text-primary">Komposisi Murid Berdasarkan Jenjang Pendidikan</h2>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">
                                    Distribusi {{ $fmt($totalMurid) }} murid di Kecamatan Cicalengka tahun {{ $tahun->tahun }}
                                </p>
                            </div>
                            <div class="self-start rounded-xl bg-surface-container-low px-3 py-1 font-label-sm text-label-sm font-semibold text-primary sm:self-center">
                                Tahun {{ $tahun->tahun }}
                            </div>
                        </div>
                        <div class="relative mt-2 h-72">
                            <canvas data-grafik="murid" role="img" aria-label="Grafik batang jumlah murid per jenjang"></canvas>
                        </div>
                    </div>
                    <div class="mt-space-md flex flex-wrap items-center justify-between gap-x-4 gap-y-1 rounded-xl bg-surface-container-low px-4 py-2.5">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-secondary"></span>
                            <span class="font-body-sm text-body-sm font-medium text-on-surface-variant">
                                Total: {{ $fmt($totalMurid) }} Murid di {{ \App\Models\Desa::count() }} Desa
                            </span>
                        </div>
                        <span class="font-label-sm text-label-sm font-bold text-secondary">
                            Rasio Guru : Murid = 1 : {{ $rasioGuruMurid ?? '—' }}
                        </span>
                    </div>
                </div>

                {{-- Demografi --}}
                <div id="kependudukan" class="flex scroll-mt-24 flex-col justify-between rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)] xl:col-span-5">
                    <div>
                        <div class="mb-4">
                            <h2 class="font-headline-md text-headline-md text-primary">Demografi Penduduk</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Proporsi gender &amp; kewilayahan</p>
                        </div>
                        <div class="my-2 flex flex-col items-center justify-center gap-6 sm:flex-row">
                            <div class="relative h-36 w-36 flex-shrink-0">
                                <canvas data-grafik="gender" role="img" aria-label="Grafik lingkaran proporsi penduduk laki-laki dan perempuan"></canvas>
                                <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center">
                                    <span class="angka-tabular font-headline-md text-headline-md font-bold text-primary">
                                        {{ $p?->total_penduduk !== null ? $fmt(round(($p->total_penduduk) / 1000)).'k' : '—' }}
                                    </span>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Jiwa</span>
                                </div>
                            </div>
                            <div class="min-w-0 w-full space-y-3">
                                <div class="flex items-center justify-between gap-3 rounded-xl bg-surface-container-low px-3 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full bg-secondary"></span>
                                        <span class="font-label-md text-label-md text-on-surface">Perempuan</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="angka-tabular font-headline-md text-headline-md font-bold text-primary">{{ $pendudukPr > 0 ? $fmt($pendudukPr) : '—' }}</div>
                                        <div class="font-label-sm text-label-sm text-on-surface-variant">{{ $totalGender > 0 ? $fmt2($persenPr) : '—' }}%</div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between gap-3 rounded-xl bg-surface-container-low px-3 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full bg-tertiary-fixed-dim"></span>
                                        <span class="font-label-md text-label-md text-on-surface">Laki-laki</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="angka-tabular font-headline-md text-headline-md font-bold text-primary">{{ $pendudukLk > 0 ? $fmt($pendudukLk) : '—' }}</div>
                                        <div class="font-label-sm text-label-sm text-on-surface-variant">{{ $totalGender > 0 ? $fmt2($persenLk) : '—' }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2 rounded-xl bg-surface-container p-4 text-center">
                        <div>
                            <span class="angka-tabular font-headline-md text-headline-md font-bold text-primary">{{ $p?->jumlah_dusun !== null ? $fmt($p->jumlah_dusun) : '—' }}</span>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">Dusun</p>
                        </div>
                        <div>
                            <span class="angka-tabular font-headline-md text-headline-md font-bold text-primary">{{ $p?->jumlah_rw !== null ? $fmt($p->jumlah_rw) : '—' }}</span>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">RW</p>
                        </div>
                        <div>
                            <span class="angka-tabular font-headline-md text-headline-md font-bold text-primary">{{ $p?->jumlah_rt !== null ? $fmt($p->jumlah_rt) : '—' }}</span>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">RT</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ROW 3: MBG & INFRASTRUKTUR --}}
            <div class="grid grid-cols-1 gap-gutter lg:grid-cols-2">
                {{-- MBG / SPPG --}}
                <div id="mbg-sppg" class="flex scroll-mt-24 flex-col justify-between rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                    <div>
                        <div class="flex items-center justify-between pb-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-secondary-fixed/30 text-secondary">
                                    <span class="material-symbols-outlined text-[22px]">restaurant</span>
                                </div>
                                <div>
                                    <h2 class="font-headline-md text-headline-md text-primary">Program Makan Bergizi Gratis (MBG / SPPG)</h2>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">Kesiapan Sentra Pelayanan Pangan Gizi Kecamatan</p>
                                </div>
                            </div>
                            <span class="hidden rounded-full bg-secondary-fixed px-2.5 py-1 font-label-sm text-label-sm font-bold text-on-secondary-fixed sm:inline-flex">
                                Prioritas {{ $tahun->tahun }}
                            </span>
                        </div>
                        <div class="my-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 rounded-xl bg-surface-container-low p-4">
                            <span class="font-body-md text-body-md text-on-surface-variant">Total Target Penerima Manfaat:</span>
                            <div class="flex items-baseline gap-1.5">
                                @if ($penerimaMbg !== null)
                                    <span class="angka-tabular font-stat-metric text-stat-metric font-extrabold text-primary">{{ $fmt($penerimaMbg) }}</span>
                                    <span class="font-label-md text-label-md font-semibold text-on-surface">Penerima</span>
                                @else
                                    <span class="nilai-kosong">Tidak tersedia</span>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            @foreach ([
                                ['label' => 'Dapur Operasional', 'nilai' => $dapurOperasional, 'warna' => 'bg-secondary', 'teks' => 'text-secondary'],
                                ['label' => 'Siap Operasional', 'nilai' => $dapurSiap, 'warna' => 'bg-secondary-fixed-dim', 'teks' => 'text-primary'],
                                ['label' => 'Tahap Persiapan', 'nilai' => $dapurPersiapan, 'warna' => 'bg-outline', 'teks' => 'text-on-surface-variant'],
                            ] as $kartuDapur)
                                <div class="flex flex-col items-center rounded-xl bg-surface px-3 py-4 text-center">
                                    <span class="mb-2 h-3 w-3 rounded-full {{ $kartuDapur['warna'] }}"></span>
                                    <span class="angka-tabular font-stat-metric text-stat-metric font-bold {{ $kartuDapur['teks'] }}">
                                        {{ $kartuDapur['nilai'] !== null ? $fmt($kartuDapur['nilai']) : '—' }}
                                    </span>
                                    <span class="mt-1 font-label-sm text-label-sm font-medium text-on-surface-variant">{{ $kartuDapur['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between font-body-sm text-body-sm text-on-surface-variant">
                        <span>Cakupan Program SPPG: Seluruh Murid &amp; Balita Rentan</span>
                    </div>
                </div>

                {{-- Infrastruktur & Pengairan --}}
                <div class="flex flex-col justify-between rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                    <div>
                        <div class="flex items-center gap-3 pb-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface-container-low text-primary">
                                <span class="material-symbols-outlined text-[22px]">alt_route</span>
                            </div>
                            <div>
                                <h2 class="font-headline-md text-headline-md text-primary">Konektivitas Jalan &amp; Sumber Daya Air</h2>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Arteri logistik, irigasi, dan perdagangan lokal</p>
                            </div>
                        </div>
                        <div class="my-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div id="infrastruktur-jalan" class="scroll-mt-24 space-y-2 rounded-xl bg-surface p-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-secondary">commute</span>
                                    <span class="font-label-sm text-label-sm font-bold uppercase tracking-wider text-on-surface-variant">Jaringan Jalan Utama</span>
                                </div>
                                <div class="space-y-1.5 font-body-sm text-body-sm">
                                    @forelse ($jalanUtama as $ruas)
                                        <div class="flex items-center justify-between">
                                            <span class="truncate pr-2 text-on-surface">{{ $ruas->nama }} ({{ $ruas->tingkat }})</span>
                                            <strong class="whitespace-nowrap font-semibold text-primary">
                                                {{ $ruas->panjang_km !== null ? number_format((float) $ruas->panjang_km, 2, ',', '.').' km' : '—' }}
                                            </strong>
                                        </div>
                                    @empty
                                        <p class="nilai-kosong text-body-sm">tidak tersedia</p>
                                    @endforelse
                                </div>
                            </div>
                            <div id="pengairan" class="scroll-mt-24 space-y-2 rounded-xl bg-surface p-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-secondary">water_drop</span>
                                    <span class="font-label-sm text-label-sm font-bold uppercase tracking-wider text-on-surface-variant">Sumber Pengairan</span>
                                </div>
                                @forelse ($sungai as $sungaian)
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                                        {{ $sungaian->nama }}{{ $sungaian->status ? ' ('.$sungaian->status.')' : '' }}
                                    </p>
                                @empty
                                    <p class="nilai-kosong text-body-sm">tidak tersedia</p>
                                @endforelse
                            </div>
                        </div>
                        <div id="pasar-perdagangan" class="scroll-mt-24 rounded-xl bg-surface p-4">
                            <div class="flex items-center gap-1 font-label-sm font-semibold text-on-surface">
                                <span class="material-symbols-outlined text-[16px] text-primary">store</span>
                                <span>Pusat Perdagangan Utama</span>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">
                                @forelse ($pasar as $pasarItem)
                                    {{ $pasarItem->nama }}{{ $pasarItem->lokasi ? ' ('.$pasarItem->lokasi.')' : '' }}@if (! $loop->last), @endif
                                @empty
                                    <span class="nilai-kosong">tidak tersedia</span>
                                @endforelse
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2 font-body-sm text-body-sm text-on-surface-variant">
                        <span>Terhubung ke Koridor Kereta Commuter Line Bandung Raya</span>
                    </div>
                </div>
            </div>

            {{-- ROW 4: TABEL DESA --}}
            <div id="data-desa" class="scroll-mt-24 space-y-space-md rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="font-headline-lg text-headline-lg text-primary">
                            Daftar {{ \App\Models\Desa::count() }} Desa di Kecamatan Cicalengka
                        </h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Data ringkasan kewilayahan dan status administrasi tahun {{ $tahun->tahun }}
                        </p>
                    </div>
                    <div class="relative w-full sm:w-72">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-outline">search</span>
                        <input
                            type="text"
                            id="search-desa"
                            onkeyup="saringTabelDesa()"
                            placeholder="Cari desa..."
                            class="w-full rounded-xl bg-surface-container-low py-2 pl-9 pr-4 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:bg-surface-container focus:outline-none"
                        />
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl">
                    <table class="w-full text-left font-body-sm text-body-sm">
                        <thead class="bg-surface-container-low font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                            <tr>
                                <th class="w-12 px-4 py-3.5 text-center">No</th>
                                <th class="px-4 py-3.5">Nama Desa</th>
                                <th class="px-4 py-3.5">Luas Wilayah</th>
                                <th class="px-4 py-3.5">Catatan Geografis / Karakteristik</th>
                                <th class="px-4 py-3.5">Status Kependudukan</th>
                                <th class="px-4 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-desa-body">
                            @foreach ($sebaranDesa as $baris)
                                @php($urlDesa = route('desa', ['slug' => $baris->desa->slug, 'tahun' => $tahun->tahun]))
                                <tr class="transition-colors hover:bg-surface-container-low/60">
                                    <td class="px-4 py-3.5 text-center font-bold text-on-surface-variant">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3.5 font-headline-md text-headline-md font-bold text-primary">
                                        <a href="{{ $urlDesa }}" class="hover:underline">{{ $baris->desa->nama }}</a>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if ($baris->luas === null)
                                            <span class="text-on-surface-variant">Tersedia di detail</span>
                                        @else
                                            <span class="angka-tabular font-semibold text-on-surface">{{ number_format((float) $baris->luas, 2, ',', '.') }} km²</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-on-surface-variant">
                                        @if ($baris->desa->id === $idTersempit)
                                            <span class="inline-flex items-center rounded-md bg-secondary-fixed/30 px-2 py-0.5 font-label-sm font-semibold text-secondary">
                                                Desa luas terkecil
                                            </span>
                                        @elseif ($baris->desa->id === $idTerluas)
                                            <span class="inline-flex items-center rounded-md bg-primary-fixed px-2 py-0.5 font-label-sm font-semibold text-on-primary-fixed-variant">
                                                Desa luas wilayah terbesar
                                            </span>
                                        @elseif ($baris->potensi->isNotEmpty())
                                            <span class="inline-flex items-center rounded-md bg-surface-container px-2 py-0.5 font-label-sm font-semibold text-on-surface-variant">
                                                {{ $baris->potensi->first() }}
                                            </span>
                                        @else
                                            <span class="nilai-kosong">tidak tersedia</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 font-semibold text-secondary">
                                            <span class="h-2 w-2 rounded-full bg-secondary"></span>
                                            Terdata
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <a
                                            href="{{ $urlDesa }}"
                                            class="inline-block rounded-lg bg-surface-container-low px-3 py-1.5 font-label-sm font-bold text-primary transition-all hover:bg-primary hover:text-on-primary"
                                        >
                                            Lihat Data
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col items-center justify-between gap-2 pt-2 font-body-sm text-body-sm text-on-surface-variant sm:flex-row">
                    <span>Menampilkan {{ $sebaranDesa->count() }} desa resmi dari Kecamatan Cicalengka, Kabupaten Bandung.</span>
                    <span class="font-semibold text-secondary">Sumber: BPS &amp; Profil Desa Kecamatan Cicalengka {{ $tahun->tahun }}</span>
                </div>
            </div>

            {{-- DATA UTAMA: PEMERINTAHAN --}}
            <section aria-labelledby="pemerintahan" class="scroll-mt-24">
                <x-judul-bagian
                    id="pemerintahan"
                    ikon="account_balance"
                    judul="Pemerintahan"
                    keterangan="Pegawai pemerintahan menurut jenis dan jenis kelamin, sesuai sumber cetakan."
                />
                <div class="rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-body-sm text-body-sm">
                            <thead class="bg-surface-container-low font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                                <tr>
                                    <th class="px-4 py-3.5">Jenis</th>
                                    <th class="px-4 py-3.5 text-right">Laki-laki</th>
                                    <th class="px-4 py-3.5 text-right">Perempuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pemerintahan as $baris)
                                    <tr class="transition-colors hover:bg-surface-container-low/60">
                                        <th scope="row" class="px-4 py-3.5 text-left font-normal text-on-surface">{{ $baris->jenis }}</th>
                                        <td class="angka-tabular px-4 py-3.5 text-right">{{ number_format((int) $baris->laki_laki, 0, ',', '.') }}</td>
                                        <td class="angka-tabular px-4 py-3.5 text-right">{{ number_format((int) $baris->perempuan, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-4 text-center text-on-surface-variant">Data tidak tersedia</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- GEOGRAFI --}}
            <section aria-labelledby="geografi" class="scroll-mt-24">
                <x-judul-bagian
                    id="geografi"
                    ikon="public"
                    judul="Geografi"
                    keterangan="Luas wilayah kecamatan dan luas per desa. Hanya desa yang luasnya tercantum pada sumber cetakan yang ditampilkan angkanya."
                />
                <div class="rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-body-sm text-body-sm">
                            <thead class="bg-surface-container-low font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                                <tr>
                                    <th class="px-4 py-3.5">Desa</th>
                                    <th class="px-4 py-3.5 text-right">Luas Wilayah</th>
                                    <th class="px-4 py-3.5">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sebaranDesa as $baris)
                                    <tr class="transition-colors hover:bg-surface-container-low/60">
                                        <th scope="row" class="px-4 py-3.5 text-left font-normal text-on-surface">{{ $baris->desa->nama }}</th>
                                        <td class="angka-tabular px-4 py-3.5 text-right">
                                            @if ($baris->luas === null)
                                                <span class="nilai-kosong">tidak tersedia</span>
                                            @else
                                                {{ number_format((float) $baris->luas, 2, ',', '.') }} km²
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 text-on-surface-variant">
                                            @if ($baris->desa->id === $idTersempit)
                                                Desa dengan wilayah terkecil
                                            @elseif ($baris->desa->id === $idTerluas)
                                                Desa dengan wilayah terbesar
                                            @elseif ($baris->luas === null)
                                                Luas tidak tercantum pada sumber
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- ADMINISTRASI KEPENDUDUKAN --}}
            <section aria-labelledby="administrasi-kependudukan" class="scroll-mt-24">
                <x-judul-bagian
                    id="administrasi-kependudukan"
                    ikon="badge"
                    judul="Administrasi Kependudukan"
                    keterangan="Data akta kelahiran dan kematian. Persentase kepemilikan akta dihitung ulang dari jumlah kelahiran dan jumlah akta, bukan diambil dari angka tercetak."
                />
                <div class="grid grid-cols-1 gap-gutter lg:grid-cols-2">
                    <div class="rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                        <h3 class="font-headline-md text-headline-md text-primary">Akta Kelahiran</h3>
                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Kelahiran yang wajib terdaftar, dikurangi yang belum memiliki akta.</p>
                        <dl class="mt-space-md space-y-3 font-body-sm text-body-sm">
                            <div class="flex items-baseline justify-between gap-4 border-b border-surface-container-high pb-3">
                                <dt class="text-on-surface-variant">Kelahiran wajib terdaftar</dt>
                                <dd class="angka-tabular font-semibold text-on-surface">{{ $fmt($aktaKelahiran['wajib'] ?? 0) }}</dd>
                            </div>
                            <div class="flex items-baseline justify-between gap-4 border-b border-surface-container-high pb-3">
                                <dt class="text-on-surface-variant">Sudah memiliki akta</dt>
                                <dd class="angka-tabular font-semibold text-secondary">{{ $fmt($aktaKelahiran['memiliki'] ?? 0) }}</dd>
                            </div>
                            <div class="flex items-baseline justify-between gap-4 border-b border-surface-container-high pb-3">
                                <dt class="text-on-surface-variant">Belum memiliki akta</dt>
                                <dd class="angka-tabular font-semibold text-error">{{ $fmt($aktaKelahiran['belum'] ?? 0) }}</dd>
                            </div>
                            <div class="flex items-baseline justify-between gap-4">
                                <dt class="text-on-surface-variant">Persentase sudah memiliki akta</dt>
                                <dd class="angka-tabular font-semibold text-primary">{{ $fmt2($aktaKelahiran['persen_memiliki'] ?? 0) }}%</dd>
                            </div>
                        </dl>
                        <div class="relative mt-5 h-56">
                            <canvas data-grafik="akta" role="img" aria-label="Grafik batang kelahiran yang sudah dan belum memiliki akta"></canvas>
                        </div>
                    </div>

                    <div class="flex flex-col rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                        <h3 class="font-headline-md text-headline-md text-primary">Akta Kematian</h3>
                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Jumlah warga yang tercatat telah meninggal di wilayah kecamatan.</p>
                        <dl class="mt-space-md space-y-3 font-body-sm text-body-sm">
                            <div class="flex items-baseline justify-between gap-4 border-b border-surface-container-high pb-3">
                                <dt class="text-on-surface-variant">Laki-laki</dt>
                                <dd class="angka-tabular font-semibold text-on-surface">{{ $fmt($aktaKematian['laki_laki'] ?? 0) }}</dd>
                            </div>
                            <div class="flex items-baseline justify-between gap-4 border-b border-surface-container-high pb-3">
                                <dt class="text-on-surface-variant">Perempuan</dt>
                                <dd class="angka-tabular font-semibold text-on-surface">{{ $fmt($aktaKematian['perempuan'] ?? 0) }}</dd>
                            </div>
                            <div class="flex items-baseline justify-between gap-4">
                                <dt class="text-on-surface-variant">Total</dt>
                                <dd class="angka-tabular font-headline-md text-headline-md font-semibold text-on-surface">{{ $fmt($aktaKematian['total'] ?? 0) }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6 border-t border-surface-container-high pt-5">
                            <p class="font-label-md text-label-md text-on-surface">Fasilitas dan layanan</p>
                            <dl class="mt-3 space-y-3 font-body-sm text-body-sm">
                                <div class="flex items-baseline justify-between gap-4 border-b border-surface-container-high pb-3">
                                    <dt class="text-on-surface-variant">Jalan status desa</dt>
                                    <dd class="angka-tabular font-semibold text-on-surface">{{ $fmt($fasilitas['jalan'] ?? 0) }} ruas</dd>
                                </div>
                                <div class="flex items-baseline justify-between gap-4 border-b border-surface-container-high pb-3">
                                    <dt class="text-on-surface-variant">Panjang jalan</dt>
                                    <dd class="angka-tabular font-semibold text-on-surface">{{ number_format((float) ($fasilitas['panjangJalan'] ?? 0), 1, ',', '.') }} km</dd>
                                </div>
                                <div class="flex items-baseline justify-between gap-4 border-b border-surface-container-high pb-3">
                                    <dt class="text-on-surface-variant">Pasar</dt>
                                    <dd class="angka-tabular font-semibold text-on-surface">{{ $fmt($fasilitas['pasar'] ?? 0) }} pasar</dd>
                                </div>
                                <div class="flex items-baseline justify-between gap-4">
                                    <dt class="text-on-surface-variant">Sungai</dt>
                                    <dd class="angka-tabular font-semibold text-on-surface">{{ $fmt($fasilitas['sungai'] ?? 0) }} sungai</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </section>

            {{-- PENDIDIKAN & GURU --}}
            <section aria-labelledby="pendidikan" class="scroll-mt-24">
                <x-judul-bagian
                    id="pendidikan"
                    ikon="school"
                    judul="Pendidikan &amp; Tenaga Didik"
                    keterangan="Jumlah sekolah dan murid per jenjang. Tabel guru pada sumber cetakan tidak dipecah per jenjang, hanya negeri dan swasta, sehingga angka guru ditampilkan terpisah."
                />
                <div class="grid grid-cols-1 gap-gutter lg:grid-cols-2">
                    <div class="rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                        <h3 class="font-headline-md text-headline-md text-primary">Sekolah per Jenjang</h3>
                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Dikelompokkan per status sekolah, negeri dan swasta.</p>
                        <div class="relative mt-4 h-72">
                            <canvas data-grafik="sekolah" role="img" aria-label="Grafik batang jumlah sekolah per jenjang dan status"></canvas>
                        </div>
                    </div>
                    <div id="guru" class="scroll-mt-24 rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                        <h3 class="font-headline-md text-headline-md text-primary">Guru per Status Sekolah</h3>
                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Sumber hanya memisahkan guru sekolah negeri dan swasta, tanpa rincian per jenjang.</p>
                        <div class="relative mt-4 h-72">
                            <canvas data-grafik="guru" role="img" aria-label="Grafik lingkaran jumlah guru sekolah negeri dan swasta"></canvas>
                        </div>
                    </div>
                </div>
            </section>

            {{-- POTENSI DESA --}}
            <section aria-labelledby="potensi-desa" class="scroll-mt-24">
                <x-judul-bagian
                    id="potensi-desa"
                    ikon="landscape"
                    judul="Potensi Desa"
                    keterangan="Jumlah desa yang memiliki masing-masing jenis potensi. Satu desa dapat memiliki lebih dari satu jenis potensi."
                />
                <div class="grid grid-cols-1 gap-gutter lg:grid-cols-2">
                    <div class="rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                        <div class="relative h-72">
                            <canvas data-grafik="potensi" role="img" aria-label="Grafik batang jumlah desa per jenis potensi"></canvas>
                        </div>
                    </div>
                    <div class="rounded-[20px] bg-surface-container-lowest p-space-lg shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                        <h3 class="font-headline-md text-headline-md text-primary">Rincian</h3>
                        <table class="mt-4 w-full font-body-sm text-body-sm">
                            <thead class="bg-surface-container-low font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium">Jenis potensi</th>
                                    <th class="px-4 py-3 text-right font-medium">Jumlah desa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($potensiPerKategori as $baris)
                                    <tr class="transition-colors hover:bg-surface-container-low/60">
                                        <th scope="row" class="px-4 py-3 text-left font-normal text-on-surface">{{ $baris->kategori }}</th>
                                        <td class="angka-tabular px-4 py-3 text-right font-medium">{{ number_format((int) $baris->jumlah, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="mt-4 font-body-sm text-body-sm leading-relaxed text-on-surface-variant">
                            Sumber hanya menandai jenis potensi setiap desa, tanpa nilai ekonomi atau keterangan tambahan.
                        </p>
                    </div>
                </div>
            </section>

            {{-- CATATAN VERIFIKASI --}}
            <section aria-labelledby="catatan-verifikasi" class="scroll-mt-24">
                <x-judul-bagian
                    id="catatan-verifikasi"
                    ikon="fact_check"
                    judul="Catatan Verifikasi"
                    keterangan="Tempat mencatat angka yang tidak konsisten atau tidak lengkap pada sumber cetakan. Data tidak diubah diam-diam supaya asalnya tetap bisa ditelusuri."
                />
                <div class="space-y-3">
                    @forelse ($catatanVerifikasi as $catatan)
                        <div class="rounded-[20px] border border-error-container bg-error-container/40 p-4">
                            <p class="font-label-sm text-label-sm font-semibold uppercase tracking-wide text-on-error-container">
                                {{ $catatan->sumber }}
                            </p>
                            <p class="mt-1.5 font-body-sm text-body-sm leading-relaxed text-on-error-container">{{ $catatan->pesan }}</p>
                        </div>
                    @empty
                        <div class="rounded-[20px] border border-primary-fixed bg-primary-fixed/40 p-4">
                            <p class="font-body-sm text-body-sm text-on-primary-fixed-variant">
                                Tidak ada ketidaksesuaian yang terdeteksi pada data tahun {{ $tahun->tahun }}.
                            </p>
                        </div>
                    @endforelse

                    <div class="rounded-[20px] bg-surface-container-lowest p-4 shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]">
                        <p class="font-label-sm text-label-sm font-semibold uppercase tracking-wide text-on-surface-variant">Keterangan umum</p>
                        <p class="mt-1.5 font-body-sm text-body-sm leading-relaxed text-on-surface-variant">
                            Seluruh angka berasal dari publikasi cetakan
                            <span class="font-medium text-on-surface">Cicalengka Dalam Angka {{ $tahun->tahun }}</span>.
                            Nilai yang tidak tercantum pada sumber ditampilkan sebagai
                            <span class="nilai-kosong">tidak tersedia</span> dan bukan diisi nol, sebab nol
                            berarti benar-benar tidak ada, sedangkan kosong berarti tidak diketahui.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    @endif
@endsection

@push('skrip')
    @if ($tahun !== null)
        <script>
            // Data grafik dikirim dari server supaya angka di grafik dan angka
            // di teks selalu berasal dari sumber yang sama.
            window.__dataDashboard = @js([
                'murid' => [
                    'jenjang' => $muridPerJenjang->pluck('jenjang'),
                    'murid' => $muridPerJenjang->pluck('murid'),
                ],
                'gender' => [
                    'label' => ['Perempuan', 'Laki-laki'],
                    'total' => [$pendudukPr, $pendudukLk],
                ],
                'sekolah' => [
                    'jenjang' => $sekolahPerJenjang->pluck('jenjang')->unique()->values(),
                    'negeri' => $sekolahPerJenjang->where('jenis', 'like', '%Negeri%')->pluck('total', 'jenjang'),
                    'swasta' => $sekolahPerJenjang->where('jenis', 'like', '%Swasta%')->pluck('total', 'jenjang'),
                ],
                'guru' => [
                    'label' => $guruPerJenis->pluck('jenis'),
                    'total' => $guruPerJenis->pluck('total'),
                ],
                'akta' => [
                    'label' => ['Sudah memiliki akta', 'Belum memiliki akta'],
                    'total' => [$aktaKelahiran['memiliki'] ?? 0, $aktaKelahiran['belum'] ?? 0],
                ],
                'potensi' => [
                    'label' => $potensiPerKategori->pluck('kategori'),
                    'total' => $potensiPerKategori->pluck('jumlah'),
                ],
            ]);
        </script>

        {{-- Pencarian pada tabel desa; tanpa library tambahan. --}}
        <script>
            function saringTabelDesa() {
                const kata = document.getElementById('search-desa').value.toLowerCase();
                const baris = document.getElementById('table-desa-body').getElementsByTagName('tr');

                for (let i = 0; i < baris.length; i++) {
                    const nama = baris[i].getElementsByTagName('td')[1];
                    const catatan = baris[i].getElementsByTagName('td')[3];
                    const teks = ((nama ? nama.textContent : '') + ' ' + (catatan ? catatan.textContent : '')).toLowerCase();
                    baris[i].style.display = teks.indexOf(kata) > -1 ? '' : 'none';
                }
            }
        </script>
    @endif
@endpush
