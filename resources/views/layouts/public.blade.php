<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>@yield('judul', 'Dashboard') — {{ config('app.name') }}</title>

        <meta
            name="description"
            content="Statistik kependudukan Kecamatan Cicalengka: penduduk, pendidikan, administrasi kependudukan, dan potensi desa."
        />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-slate-800 antialiased">
        <a
            href="#konten"
            class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-dashboard-600 focus:px-4 focus:py-2 focus:text-white"
        >
            Lompat ke konten utama
        </a>

        {{-- Header --}}
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('dashboard') }}" class="group flex items-center gap-3">
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-dashboard-600 font-bold text-white"
                    >
                        C
                    </span>
                    <span class="leading-tight">
                        <span class="block text-sm font-semibold tracking-tight text-slate-900 group-hover:text-dashboard-700">
                            {{ config('app.name') }}
                        </span>
                        <span class="block text-xs text-slate-500">Kecamatan Cicalengka, Kabupaten Bandung</span>
                    </span>
                </a>

                <div class="flex items-center gap-2">
                    {{-- Pilihan tahun --}}
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                        <label for="tahun" class="sr-only">Pilih tahun data</label>
                        <select
                            id="tahun"
                            name="tahun"
                            onchange="this.form.submit()"
                            class="rounded-lg border-slate-300 py-1.5 pl-3 pr-8 text-sm focus:border-dashboard-500 focus:ring-dashboard-500"
                        >
                            @foreach ($daftarTahun ?? [] as $item)
                                <option value="{{ $item->tahun }}" @selected(($tahun?->id ?? null) === $item->id)>
                                    {{ $item->tahun }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <a
                        href="{{ route('dashboard') }}"
                        @class([
                            'rounded-lg px-3 py-1.5 text-sm font-medium transition',
                            'bg-dashboard-600 text-white' => request()->routeIs('dashboard'),
                            'text-slate-600 hover:bg-slate-100' => ! request()->routeIs('dashboard'),
                        ])
                    >
                        Dashboard
                    </a>

                    <a href="/admin" class="rounded-lg px-3 py-1.5 text-sm text-slate-600 hover:bg-slate-100">
                        Admin
                    </a>
                </div>
            </div>
        </header>

        <main id="konten" class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @yield('konten')
        </main>

        {{-- Footer --}}
        <footer class="mt-12 border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-8 text-sm text-slate-600 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-start justify-between gap-6">
                    <div class="max-w-md">
                        <p class="font-medium text-slate-900">{{ config('app.name') }}</p>
                        <p class="mt-1">
                            Data statistik Kecamatan Cicalengka, Kabupaten Bandung. Seluruh angka
                            diambil dari publikasi
                            <span class="font-medium">Cicalengka Dalam Angka {{ $tahun?->tahun ?? '2026' }}</span>.
                        </p>
                    </div>

                    <div>
                        <p class="font-medium text-slate-900">Tentang angka di halaman ini</p>
                        <p class="mt-1">
                            Nilai yang tidak ada pada sumber cetakan ditampilkan sebagai
                            <span class="nilai-kosong">tidak tersedia</span>, bukan diisi nol.
                            Angka yang tidak konsisten pada sumber dicatat di bagian
                            <a href="{{ route('dashboard') }}#catatan-verifikasi" class="underline">Catatan Verifikasi</a>.
                        </p>
                    </div>
                </div>

                <p class="mt-8 border-t border-slate-100 pt-6 text-xs text-slate-400">
                    Dibangun dengan Laravel dan Filament. Panel admin tersedia di
                    <a href="/admin" class="underline">/admin</a>.
                </p>
            </div>
        </footer>

        @stack('skrip')
    </body>
</html>