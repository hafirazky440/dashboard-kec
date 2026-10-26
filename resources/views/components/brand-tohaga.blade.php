@php
    $attributes = $attributes ?? new \Illuminate\View\ComponentAttributeBag;
@endphp

<span {{ $attributes->merge(['class' => 'brand-judul']) }}>
    Dashboard <span class="brand-tohaga">Tohaga</span> Cicalengka
</span>