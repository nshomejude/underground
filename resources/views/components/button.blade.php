@props([
    'variant' => 'primary',
    'href' => null,
    'type' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 px-6 py-3 text-xs font-semibold uppercase tracking-widest transition-colors duration-150';

    $variants = [
        'primary' => 'bg-gold text-ink hover:bg-gold-bright',
        'secondary' => 'border border-gold text-gold hover:bg-gold hover:text-ink',
    ];

    $classes = trim($base . ' ' . ($variants[$variant] ?? $variants['primary']));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type ?? 'button' }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
