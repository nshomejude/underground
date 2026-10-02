@props([
    'size' => 96,
    'variant' => 'gold',
    'alt' => 'Underground Network seal',
])

@php
    $variant = in_array($variant, ['gold', 'platinum', 'ink'], true) ? $variant : 'gold';
@endphp

{{-- The Underground platform seal. PNG (1x/2x) so it renders identically in the
     browser, in print and in documents; fixed width/height so it never distorts. --}}
<img
    src="{{ asset('images/seal/seal-'.$variant.'.png') }}"
    srcset="{{ asset('images/seal/seal-'.$variant.'.png') }} 1x, {{ asset('images/seal/seal-'.$variant.'@2x.png') }} 2x"
    width="{{ $size }}"
    height="{{ $size }}"
    alt="{{ $alt }}"
    decoding="async"
    {{ $attributes->merge(['class' => 'inline-block shrink-0']) }}
    style="width:{{ $size }}px;height:{{ $size }}px"
>
