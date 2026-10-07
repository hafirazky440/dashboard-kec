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

        {{-- Tipografi dan ikon dari desain system --}}
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
            rel="stylesheet"
        />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                overscroll-behavior: none;
            }
        </style>
    </head>
    <body class="bg-surface font-body-md text-body-md text-on-surface antialiased">
        <a
            href="#konten"
            class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-primary focus:px-4 focus:py-2 focus:text-on-primary"
        >
            Lompat ke konten utama
        </a>

        @php
            // Struktur navigasi mengikuti design system: Dashboard, lalu
            // kategori data yang dikelompokkan. Tautan menuju bagian di
            // halaman yang sama (satu halaman panjang), atau kembali ke
            // dashboard dengan membawa tahun yang sedang dipilih.
            $tautanDasbor = route('dashboard', $tahun?->tahun ? ['tahun' => $tahun->tahun] : []);
            $navigasi = [
                ['judul' => 'Dasbor', 'item' => [
                    ['id' => 'dashboard', 'label' => 'Dashboard', 'ikon' => 'dashboard'],
                ]],
                ['judul' => 'Data Utama', 'item' => [
                    ['id' => 'pemerintahan', 'label' => 'Pemerintahan', 'ikon' => 'account_balance'],
                    ['id' => 'geografi', 'label' => 'Geografi', 'ikon' => 'public'],
                    ['id' => 'kependudukan', 'label' => 'Kependudukan', 'ikon' => 'group'],
                    ['id' => 'administrasi-kependudukan', 'label' => 'Administrasi Kependudukan', 'ikon' => 'badge'],
                    ['id' => 'data-desa', 'label' => 'Data Desa', 'ikon' => 'holiday_village'],
                ]],
                ['judul' => 'Pendidikan', 'item' => [
                    ['id' => 'pendidikan', 'label' => 'Pendidikan', 'ikon' => 'school'],
                    ['id' => 'guru', 'label' => 'Guru', 'ikon' => 'person_apron'],
                    ['id' => 'murid', 'label' => 'Murid', 'ikon' => 'backpack'],
                ]],
                ['judul' => 'Infrastruktur', 'item' => [
                    ['id' => 'infrastruktur-jalan', 'label' => 'Infrastruktur Jalan', 'ikon' => 'alt_route'],
                    ['id' => 'pengairan', 'label' => 'Pengairan', 'ikon' => 'water_drop'],
                    ['id' => 'pasar-perdagangan', 'label' => 'Pasar & Perdagangan', 'ikon' => 'storefront'],
                ]],
                ['judul' => 'Potensi', 'item' => [
                    ['id' => 'potensi-desa', 'label' => 'Potensi Desa', 'ikon' => 'landscape'],
                    ['id' => 'mbg-sppg', 'label' => 'MBG / SPPG', 'ikon' => 'nutrition'],
                ]],
            ];

            $halamanDasbor = request()->routeIs('dashboard');
        @endphp

        {{-- Overlay untuk mobile --}}
        <div
            id="penutup-sidebar"
            class="fixed inset-0 z-40 hidden bg-inverse-surface/40 backdrop-blur-sm lg:hidden"
            onclick="tutupSidebar()"
        ></div>

        {{-- Sidebar --}}
        <aside
            id="sidebar"
            class="fixed left-0 top-0 z-50 flex h-full w-72 -translate-x-full flex-col bg-primary text-on-primary shadow-[0_1px_8px_rgba(0,0,0,0.06)] transition-transform duration-200 lg:translate-x-0"
        >
            <div class="flex items-center gap-space-sm bg-primary-container p-space-md">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-secondary-fixed font-headline-md text-headline-md font-bold tracking-tight text-on-secondary-fixed shadow-sm"
                >
                    CDA
                </div>
                <div class="flex min-w-0 flex-col">
                    <span class="truncate font-headline-md text-headline-md tracking-tight text-on-primary">
                        Cicalengka Dalam Angka
                    </span>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-primary-fixed-dim">
                        Kecamatan Cicalengka
                    </span>
                </div>
            </div>

            <div class="flex-1 space-y-space-md overflow-y-auto px-space-sm py-space-md">
                @foreach ($navigasi as $grup)
                    <nav class="space-y-1">
                        <div class="px-3 py-1 font-label-sm text-label-sm uppercase tracking-wider text-primary-fixed-dim">
                            {{ $grup['judul'] }}
                        </div>
                        @foreach ($grup['item'] as $item)
                            @php($aktif = $halamanDasbor && $item['id'] === 'dashboard')
                            <a
                                href="{{ $halamanDasbor ? '#' . $item['id'] : $tautanDasbor . '#' . $item['id'] }}"
                                @class([
                                    'flex items-center gap-3 rounded-lg px-3 py-2.5 transition-colors',
                                    'bg-primary-container font-headline-md text-headline-md text-on-primary shadow-sm' => $aktif,
                                    'font-body-md text-body-md text-on-primary-container hover:bg-primary-container hover:text-on-primary' => ! $aktif,
                                ])
                            >
                                <span class="material-symbols-outlined text-[20px]">{{ $item['ikon'] }}</span>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>
                @endforeach

                <nav class="space-y-1">
                    <div class="px-3 py-1 font-label-sm text-label-sm uppercase tracking-wider text-primary-fixed-dim">
                        Admin
                    </div>
                    <a
                        href="/admin"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-body-md text-body-md text-on-primary-container transition-colors hover:bg-primary-container hover:text-on-primary"
                    >
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                        <span>Login Admin</span>
                    </a>
                </nav>
            </div>

            <div class="border-t border-primary-container px-space-md py-space-sm">
                <p class="font-label-sm text-label-sm text-primary-fixed-dim">
                    BPS &amp; Profil Desa {{ $tahun?->tahun ?? '2026' }}
                </p>
            </div>
        </aside>

        {{-- Bar atas untuk mobile --}}
        <header
            class="sticky top-0 z-30 flex items-center gap-3 border-b border-surface-container-high bg-surface/95 px-gutter-sm py-3 backdrop-blur lg:hidden"
        >
            <button
                type="button"
                onclick="bukaSidebar()"
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface-container-low text-primary"
                aria-label="Buka menu"
            >
                <span class="material-symbols-outlined">menu</span>
            </button>
            <span class="font-headline-md text-headline-md text-primary">Cicalengka Dalam Angka</span>
        </header>

        <div class="lg:pl-72">
            <main
                id="konten"
                class="relative min-h-screen bg-surface px-gutter-sm py-gutter-sm sm:px-gutter sm:py-gutter lg:px-margin lg:py-margin"
            >
                @yield('konten')

                <footer class="mt-space-xl border-t border-surface-container-high pt-space-md">
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        Dibangun dengan Laravel dan Filament. Panel admin tersedia di
                        <a href="/admin" class="font-semibold text-secondary underline">/admin</a>.
                        Nilai yang tidak ada pada sumber ditampilkan sebagai
                        <span class="nilai-kosong">tidak tersedia</span>, bukan diisi nol.
                    </p>
                </footer>
            </main>
        </div>

        @stack('skrip')

        <script>
            function bukaSidebar() {
                document.getElementById('sidebar').classList.remove('-translate-x-full');
                document.getElementById('penutup-sidebar').classList.remove('hidden');
            }

            function tutupSidebar() {
                document.getElementById('sidebar').classList.add('-translate-x-full');
                document.getElementById('penutup-sidebar').classList.add('hidden');
            }

            // Setelah menekan tautan bagian pada mobile, sidebar ditutup lagi
            // supaya konten yang dituju langsung terlihat.
            document.querySelectorAll('#sidebar a[href*="#"]').forEach(function (tautan) {
                tautan.addEventListener('click', function () {
                    if (window.innerWidth < 1024) {
                        tutupSidebar();
                    }
                });
            });
        </script>
    </body>
</html>
