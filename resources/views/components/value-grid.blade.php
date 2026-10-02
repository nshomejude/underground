@props([
    'eyebrow' => null,
    'heading' => null,
    'items' => [],
    'columns' => 3,
    'tone' => 'ink',
])

@php
    $colClass = match ((int) $columns) {
        2 => 'sm:grid-cols-2',
        4 => 'sm:grid-cols-2 lg:grid-cols-4',
        default => 'sm:grid-cols-2 lg:grid-cols-3',
    };
@endphp

<section class="border-t border-border {{ $tone === 'surface' ? 'bg-surface' : 'bg-ink' }}">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
        @if ($heading)
            <x-section-heading :eyebrow="$eyebrow">{{ $heading }}</x-section-heading>
        @endif

        <div class="{{ $heading ? 'mt-10' : '' }} grid grid-cols-1 tile-grid {{ $colClass }}">
            @foreach ($items as $item)
                <div class="flex flex-col gap-3 {{ $tone === 'surface' ? 'bg-surface' : 'bg-ink' }} p-6 lg:p-8">
                    @if (! empty($item['icon']))
                        <span class="flex h-11 w-11 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $item['icon'] }}" class="h-5 w-5" />
                        </span>
                    @endif
                    <h3 class="font-serif text-lg font-semibold leading-snug text-cream">{{ $item['title'] }}</h3>
                    <p class="text-sm leading-relaxed text-body">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
