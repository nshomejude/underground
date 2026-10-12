@props(['compact' => false, 'icon' => true, 'lockup' => false])

@if ($lockup)
    {{-- The full supplied logo lockup (mark + UNDERGROUND wordmark + tagline). White and
         gold artwork on transparent, so it is for the dark public chrome only — the admin
         panel has a light theme and keeps the themed mark + text below. --}}
    <img
        src="{{ asset('images/brand/underground-logo.png') }}"
        alt="Underground — Power Beneath The Surface"
        width="1600"
        height="288"
        decoding="async"
        {{ $attributes->merge(['class' => 'block h-9 w-auto sm:h-10']) }}
    >
@else
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
        @if ($icon)
            <x-seal :size="36" alt="" />
        @endif
        <span class="flex flex-col leading-none">
            <span class="font-serif text-base font-semibold tracking-wide text-cream">UNDERGROUND</span>
            @unless ($compact)
                <span class="mt-1 hidden text-[9px] min-[340px]:block font-semibold uppercase tracking-[0.3em] text-muted">
                    &mdash; Power Beneath The Surface
                </span>
            @endunless
        </span>
    </span>
@endif
