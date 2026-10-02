@props([
    'eyebrow' => 'Questions',
    'heading' => 'Frequently Asked',
    'items' => [],
    'tone' => 'surface',
])

<section class="border-t border-border {{ $tone === 'ink' ? 'bg-ink' : 'bg-surface' }}">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:gap-16 lg:px-8 lg:py-20">
        <x-section-heading :eyebrow="$eyebrow" class="lg:col-span-1">{{ $heading }}</x-section-heading>

        <div class="flex flex-col divide-y divide-border border-y border-border lg:col-span-2">
            @foreach ($items as $item)
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-base font-semibold text-cream marker:hidden">
                        {{ $item['q'] }}
                        <x-icon name="chevron-down" class="h-4 w-4 shrink-0 text-gold transition-transform group-open:rotate-180" />
                    </summary>
                    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-body">{{ $item['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(static fn (array $item): array => [
        '@type' => 'Question',
        'name' => $item['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
    ], $items),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
