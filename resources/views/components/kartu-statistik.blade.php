{{--
    Satu kartu statistik.

    Parameter:
      $label      judul kartu
      $nilai      angka yang ditampilkan, boleh null
      $keterangan keterangan kecil di bawah angka
      $ikon       nama ikon Material Symbols (opsional)
      $aksen      true untuk kartu hijau tua sebagai penanda utama

    Nilai null ditampilkan sebagai "tidak tersedia" dengan gaya miring, bukan
    sebagai angka nol. Ini perbedaan yang penting untuk laporan statistik:
    nol berarti "tidak ada", sedangkan "tidak tersedia" berarti "tidak
    diketahui" karena sumbernya memang tidak memuat angka tersebut.
--}}
@props([
    'label' => '',
    'nilai' => null,
    'keterangan' => '',
    'ikon' => 'insert_chart',
    'aksen' => false,
])

@php
    $judul = $aksen ? 'text-primary-fixed' : 'text-on-surface-variant';
    $angka = $aksen ? 'text-on-primary' : 'text-primary';
    $wadah = $aksen
        ? 'relative overflow-hidden bg-primary text-on-primary shadow-[0px_8px_24px_-4px_rgba(15,77,50,0.25)]'
        : 'bg-surface-container-lowest text-on-surface shadow-[0px_4px_20px_-2px_rgba(15,77,50,0.05)]';
    $lencanaIkon = $aksen
        ? 'bg-primary-container text-secondary-fixed'
        : 'bg-surface-container-low text-primary';
@endphp

<div {{ $attributes->merge(['class' => "rounded-[20px] p-space-lg flex flex-col justify-between {$wadah}"]) }}>
    <div class="flex items-center gap-2">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $lencanaIkon }}">
            <span class="material-symbols-outlined text-[22px]">{{ $ikon }}</span>
        </div>
        <span class="font-label-md text-label-md uppercase tracking-wider {{ $judul }}">{{ $label }}</span>
    </div>

    <div class="my-space-md">
        @if ($nilai === null)
            <p class="nilai-kosong text-headline-lg">Tidak tersedia</p>
        @else
            <p class="angka-tabular font-stat-metric text-stat-metric font-extrabold tracking-tight {{ $angka }}">
                {{ $nilai }}
            </p>
        @endif
    </div>

    @if ($keterangan !== '')
        <p class="font-body-sm text-body-sm {{ $aksen ? 'text-primary-fixed-dim' : 'text-on-surface-variant' }}">
            {{ $keterangan }}
        </p>
    @endif
</div>
