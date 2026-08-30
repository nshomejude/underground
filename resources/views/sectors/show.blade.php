@php
    // Presentational-only framing per sector, keyed by slug. Not part of the
    // domain entity — purely additional copy for the detail page. Falls back
    // to a generic line for any sector without a bespoke entry.
    $whyWeOperate = [
        'government-public-sector' => 'Heads of state and public institutions operate under scrutiny most advisors '
            .'never see. We work inside that reality — building the discreet, durable relationships that let policy '
            .'and strategy move together.',
        'energy-natural-resources' => 'The energy transition is rewriting who holds leverage in resource-rich '
            .'markets. We sit at that rewrite, aligning capital, policy, and relationships before the shift is '
            .'obvious to anyone else.',
        'infrastructure-transportation' => 'Ports, corridors, and networks are where capital becomes strategic '
            .'outcome. We advise the institutions that build and control them, quietly, over the long arc of a '
            .'project.',
        'defense-security' => 'Defense and security relationships are built on trust that takes years to earn and '
            .'a moment to lose. Our counsel is calibrated for that stake — discreet, credentialed, and built for '
            .'the long term.',
        'technology-innovation' => 'Technology ventures increasingly need sovereigns, and sovereigns increasingly '
            .'need technology. We position each at the intersection, before the market catches up to where policy '
            .'is heading.',
        'finance-investments' => 'Capital allocators rarely see the political and strategic context their '
            .'investments depend on until it moves against them. We give that context a seat at the table from the '
            .'start.',
    ];

    $framing = $whyWeOperate[$sector->slug->value]
        ?? 'A vertical where discretion, relationships, and strategic patience compound into outcomes that don\'t '
            .'show up in a press release.';
@endphp

<x-layout :title="$sector->name">
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <a href="{{ route('sectors.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
            Back to Sectors
        </a>

        <div class="mt-8 flex flex-col gap-6">
            <span class="flex h-14 w-14 items-center justify-center border border-gold text-gold">
                <x-icon name="{{ $sector->motif }}" class="h-7 w-7" />
            </span>

            <h1 class="font-serif text-3xl font-semibold leading-tight text-cream sm:text-4xl lg:text-5xl">
                {{ $sector->name }}
            </h1>
        </div>

        <div class="mt-10 flex flex-col gap-6 border-t border-border pt-10">
            <p class="text-base leading-relaxed text-body">
                {{ $sector->summary }}
            </p>

            <p class="text-base leading-relaxed text-body">
                {{ $framing }}
            </p>
        </div>

        <div class="mt-12 flex flex-wrap items-center gap-4">
            <x-button variant="primary" href="{{ route('inquiries.create') }}">
                Start a Confidential Inquiry
                <x-icon name="lock" class="h-3.5 w-3.5" />
            </x-button>
            <x-button variant="secondary" href="{{ route('sectors.index') }}">
                Explore All Sectors
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
            </x-button>
        </div>
    </article>
</x-layout>
