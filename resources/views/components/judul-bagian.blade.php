{{--
    Judul bagian dengan keterangan opsional.

    Dipakai berulang di halaman dashboard supaya ukuran dan jarak antar
    bagian selalu sama, bukan ditentukan ulang di tiap tempat.
--}}
@props([
    'judul' => '',
    'keterangan' => null,
    'id' => null,
    'ikon' => null,
])

<div class="mb-space-md scroll-mt-24" @if ($id) id="{{ $id }}" @endif>
    <div class="flex items-center gap-3">
        @if ($ikon)
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface-container-low text-primary">
                <span class="material-symbols-outlined text-[22px]">{{ $ikon }}</span>
            </div>
        @endif
        <h2 class="font-headline-lg text-headline-lg tracking-tight text-primary">{{ $judul }}</h2>
    </div>

    @if ($keterangan)
        <p class="mt-1 max-w-4xl font-body-sm text-body-sm leading-relaxed text-on-surface-variant">{{ $keterangan }}</p>
    @endif
</div>
