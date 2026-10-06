{{--
    Judul bagian dengan keterangan opsional.

    Dipakai berulang di halaman dashboard supaya ukuran dan jarak antar
    bagian selalu sama, bukan ditentukan ulang di tiap tempat.
--}}
@props([
    'judul' => '',
    'keterangan' => null,
    'id' => null,
])

<div class="mb-4" @if ($id) id="{{ $id }}" @endif>
    <h2 class="text-lg font-semibold tracking-tight text-slate-900">{{ $judul }}</h2>

    @if ($keterangan)
        <p class="mt-1 max-w-3xl text-sm leading-relaxed text-slate-500">{{ $keterangan }}</p>
    @endif
</div>