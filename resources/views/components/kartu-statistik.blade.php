{{--
    Satu kartu statistik.

    Parameter:
      $label      judul kartu
      $nilai      angka yang ditampilkan, boleh null
      $keterangan keterangan kecil di bawah angka

    Nilai null ditampilkan sebagai "tidak tersedia" dengan gaya miring, bukan
    sebagai angka nol. Ini distinction yang penting untuk laporan statistik:
    nol berarti "tidak ada", sedangkan "tidak tersedia" berarti "tidak
    diketahui" karena sumbernya memang tidak memuat angka tersebut.
--}}
@props([
    'label' => '',
    'nilai' => null,
    'keterangan' => '',
])

<div
    {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white p-5 shadow-sm']) }}
>
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>

    @if ($nilai === null)
        <p class="nilai-kosong mt-2 text-2xl font-semibold tracking-tight">Tidak tersedia</p>
    @else
        <p class="angka-tabular mt-2 text-3xl font-semibold tracking-tight text-slate-900">
            {{ $nilai }}
        </p>
    @endif

    @if ($keterangan !== '')
        <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ $keterangan }}</p>
    @endif
</div>