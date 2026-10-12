@props(['compact' => false, 'icon' => true])

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
