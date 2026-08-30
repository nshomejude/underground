@props(['compact' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    <span class="flex h-9 w-9 shrink-0 items-center justify-center border border-gold font-serif text-lg font-bold text-gold">
        U
    </span>
    <span class="flex flex-col leading-none">
        <span class="font-serif text-base font-semibold tracking-wide text-cream">UNDERGROUND</span>
        @unless ($compact)
            <span class="mt-1 text-[9px] font-semibold uppercase tracking-[0.3em] text-muted">
                &mdash; Power Beneath The Surface
            </span>
        @endunless
    </span>
</span>
