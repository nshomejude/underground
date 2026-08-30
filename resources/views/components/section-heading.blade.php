@props([
    'eyebrow' => null,
    'align' => 'left',
])

@php
    $alignClasses = $align === 'center' ? 'items-center text-center' : 'items-start text-left';
@endphp

<div {{ $attributes->merge(['class' => "flex flex-col gap-3 {$alignClasses}"]) }}>
    @if ($eyebrow)
        <span class="inline-flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-gold">
            @unless ($align === 'center')
                <span class="h-px w-8 bg-gold"></span>
            @endunless
            {{ $eyebrow }}
        </span>
    @endif

    <h2 class="font-serif text-3xl font-semibold leading-tight text-cream sm:text-4xl lg:text-5xl">
        {{ $slot }}
    </h2>
</div>
