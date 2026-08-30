@php
    $narrative = $landingPage->narrative;
    $metrics = $landingPage->metrics;
    $capabilities = $landingPage->capabilities;
    $sectors = $landingPage->sectors;
    $engagementModels = $landingPage->engagementModels;
    $pillars = $landingPage->pillars;
    $insights = $landingPage->insights;

    // A deterministic, non-random dot texture standing in for a literal map —
    // no external image, just a loose "global network" mood.
    $reachDots = [];
    for ($row = 0; $row < 9; $row++) {
        for ($col = 0; $col < 26; $col++) {
            $noise = sin($row * 12.9898 + $col * 78.233) * 43758.5453;
            $fraction = $noise - floor($noise);

            if ($fraction > 0.58) {
                $reachDots[] = [
                    'cx' => $col * 16 + 8,
                    'cy' => $row * 16 + 8,
                    'r' => $fraction > 0.82 ? 2.4 : 1.3,
                ];
            }
        }
    }
@endphp

<x-layout title="Home">
    {{-- Hero --}}
    <section class="border-b border-border bg-ink">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:items-center lg:gap-16 lg:px-8 lg:py-24">
            <div class="flex flex-col items-start gap-6">
                <span class="inline-flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-gold">
                    <span class="h-px w-8 bg-gold"></span>
                    {{ $narrative->tagline }}
                </span>

                <h1 class="font-serif text-4xl font-semibold uppercase leading-[1.05] text-cream sm:text-5xl lg:text-6xl">
                    @foreach ($narrative->headline as $line)
                        <span class="block {{ $loop->last ? 'text-gold-bright' : '' }}">{{ $line }}</span>
                    @endforeach
                </h1>

                <p class="max-w-xl text-base leading-relaxed text-body">
                    {{ $narrative->intro }}
                </p>

                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <x-button variant="primary" href="#capabilities">
                        Our Capabilities
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                    </x-button>
                    <x-button variant="secondary" href="{{ $aboutHref }}">
                        Who We Are
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                    </x-button>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div class="overflow-hidden border border-border">
                    <img
                        src="{{ $founderPortraitSrc }}"
                        alt="Portrait of the Underground founder, with a government seat of power in the background"
                        class="h-full w-full object-cover"
                        width="1551"
                        height="2048"
                        loading="eager"
                    >
                </div>

                <div class="flex flex-col gap-2 border-t-2 border-gold px-1 pt-4">
                    <p class="font-serif text-lg font-semibold uppercase leading-snug tracking-wide text-gold">
                        {{ $narrative->creedTitle }}
                    </p>
                    <p class="text-xs uppercase leading-relaxed tracking-wide text-muted">
                        {{ $narrative->creedBody }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pillars strip --}}
    <section class="border-b border-border bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="text-center text-xs font-semibold uppercase tracking-[0.2em] text-gold">
                {{ $narrative->creedTitle }}
            </p>

            <div class="mt-8 grid grid-cols-2 divide-y divide-border border-border sm:divide-y-0 sm:divide-x lg:grid-cols-4">
                @foreach ($pillars as $pillar)
                    @php
                        $pillarLead = trim(str_ireplace($pillar->qualifier, '', $pillar->title));
                    @endphp
                    <div class="flex flex-col items-center gap-3 px-4 py-6 text-center">
                        <x-icon name="{{ $pillar->icon }}" class="h-7 w-7 text-gold" />
                        <p class="text-xs font-semibold uppercase leading-relaxed tracking-widest text-cream">
                            {{ $pillarLead }}<br>
                            <span class="text-muted">{{ $pillar->qualifier }}</span>
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Stat bar --}}
    <section class="border-b border-border bg-ink">
        <div class="mx-auto grid max-w-7xl grid-cols-2 divide-y divide-border sm:grid-cols-3 sm:divide-y-0 sm:divide-x lg:grid-cols-5">
            @foreach ($metrics as $metric)
                <div class="flex flex-col items-center gap-2 px-4 py-8 text-center">
                    <x-icon name="{{ $metric->icon }}" class="h-6 w-6 text-gold" />
                    <p class="font-serif text-3xl font-semibold text-gold sm:text-4xl">{{ $metric->value }}</p>
                    <p class="max-w-[10rem] text-[11px] font-semibold uppercase leading-snug tracking-wide text-muted">
                        @foreach ($metric->labelLines() as $line)
                            {{ $line }}@if (!$loop->last)<br>@endif
                        @endforeach
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Capabilities --}}
    <section id="capabilities" class="scroll-mt-20 border-b border-border bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <x-section-heading :eyebrow="$narrative->capabilitiesEyebrow" align="center" class="mx-auto max-w-2xl">
                {{ $narrative->capabilitiesHeading }}
            </x-section-heading>

            {{-- Mobile: stacked list --}}
            <div class="mt-10 flex flex-col divide-y divide-border border-y border-border lg:hidden">
                @foreach ($capabilities as $capability)
                    <a
                        href="{{ route('capabilities.show', $capability->slug->value) }}"
                        class="flex min-h-[44px] items-center gap-4 py-5 transition-colors hover:text-gold"
                    >
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $capability->icon }}" class="h-5 w-5" />
                        </span>
                        <span class="flex flex-1 flex-col gap-1">
                            <span class="font-serif text-base font-semibold leading-snug text-cream">{{ $capability->title }}</span>
                            <span class="text-sm leading-snug text-body">{{ $capability->summary }}</span>
                        </span>
                        <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gold" />
                    </a>
                @endforeach
            </div>

            {{-- Desktop: grid up to four columns --}}
            <div class="mt-12 hidden grid-cols-2 gap-px bg-border lg:grid lg:grid-cols-4">
                @foreach ($capabilities as $capability)
                    <a
                        href="{{ route('capabilities.show', $capability->slug->value) }}"
                        class="group flex flex-col items-start gap-4 bg-surface p-8 transition-colors hover:bg-surface-raised"
                    >
                        <span class="flex h-12 w-12 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $capability->icon }}" class="h-6 w-6" />
                        </span>
                        <h3 class="font-serif text-lg font-semibold leading-snug text-cream group-hover:text-gold">
                            @foreach ($capability->titleLines() as $line)
                                {{ $line }}@if (!$loop->last)<br>@endif
                            @endforeach
                        </h3>
                        <p class="text-sm leading-relaxed text-body">{{ $capability->summary }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Sectors --}}
    <section id="sectors" class="scroll-mt-20 border-b border-border bg-ink">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <x-section-heading eyebrow="Where We Operate">
                {{ $narrative->sectorsHeading }}
            </x-section-heading>

            <div class="mt-10 grid grid-cols-2 gap-px bg-border sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($sectors as $sector)
                    <div class="flex aspect-square flex-col justify-end bg-gradient-to-b from-surface to-ink p-4">
                        <p class="text-xs font-semibold uppercase leading-snug tracking-wide text-cream">
                            @foreach ($sector->nameLines() as $line)
                                {{ $line }}@if (!$loop->last)<br>@endif
                            @endforeach
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Global reach + engagement models --}}
    <section id="reach" class="scroll-mt-20 border-b border-border bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:gap-16">
                <div class="flex flex-col gap-6">
                    <x-section-heading>{{ $narrative->reachHeading }}</x-section-heading>

                    <p class="max-w-md text-base leading-relaxed text-body">
                        {{ $narrative->reachBody }}
                    </p>

                    <svg viewBox="0 0 424 152" class="h-auto w-full max-w-md text-gold/50" aria-hidden="true">
                        @foreach ($reachDots as $dot)
                            <circle cx="{{ $dot['cx'] }}" cy="{{ $dot['cy'] }}" r="{{ $dot['r'] }}" fill="currentColor" />
                        @endforeach
                    </svg>
                </div>

                <div class="flex flex-col gap-px border border-border bg-border">
                    <p class="bg-ink px-6 py-4 text-xs font-semibold uppercase tracking-[0.2em] text-gold">
                        {{ $narrative->engagementHeading }}
                    </p>

                    @foreach ($engagementModels as $model)
                        <div class="flex min-h-[44px] items-center gap-4 bg-ink px-6 py-5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center border border-gold text-gold">
                                <x-icon name="{{ $model->icon }}" class="h-4 w-4" />
                            </span>
                            <span class="flex-1 text-sm font-semibold uppercase tracking-wide text-cream">
                                {{ $model->name }}
                            </span>
                            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gold" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Latest thinking --}}
    @if ($insights !== [])
        <section id="insights" class="scroll-mt-20 border-b border-border bg-ink">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-section-heading eyebrow="Perspective">Latest Thinking</x-section-heading>

                    <a href="{{ route('insights.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
                        View All Insights
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                    </a>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($insights as $insight)
                        <a
                            href="{{ route('insights.show', $insight->slug->value) }}"
                            class="group flex flex-col gap-3 border border-border bg-surface p-6 transition-colors hover:border-gold"
                        >
                            <span class="text-xs font-semibold uppercase tracking-widest text-gold">{{ $insight->category }}</span>
                            <h3 class="font-serif text-lg font-semibold leading-snug text-cream group-hover:text-gold">
                                {{ $insight->title }}
                            </h3>
                            <p class="line-clamp-2 text-sm leading-relaxed text-body">{{ $insight->excerpt }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Closing CTA --}}
    <section class="bg-ink">
        <div class="mx-auto flex max-w-7xl flex-col items-start gap-8 border-t border-gold/40 px-4 py-16 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 lg:py-20">
            <div class="flex flex-col gap-3">
                <h2 class="max-w-xl font-serif text-2xl font-semibold uppercase leading-tight text-cream sm:text-3xl">
                    {{ $narrative->closingHeading }}
                </h2>
                <p class="text-sm uppercase tracking-wide text-muted">{{ $narrative->closingSupport }}</p>
            </div>

            <x-button variant="primary" href="{{ route('inquiries.create') }}" class="shrink-0">
                Start a Confidential Conversation
                <x-icon name="lock" class="h-3.5 w-3.5" />
            </x-button>
        </div>
    </section>
</x-layout>
