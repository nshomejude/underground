<x-layout title="Flex Media Group">
    <section class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Underground Network">
            Flex Media Group
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Flex Media Group is how Underground speaks in its own voice &mdash; livestream briefings,
            long-form conversations, and Underground Magazine &mdash; built around strategic advisory,
            economic diplomacy, and our continued focus on Africa.
        </p>
    </section>

    {{-- Livestream --}}
    <section class="border-t border-border bg-surface">
        <div class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center border border-gold text-gold">
                    <x-icon name="camera" class="h-5 w-5" />
                </span>
                <x-section-heading eyebrow="Watch">Livestream</x-section-heading>
            </div>

            @if ($livestreams === [])
                <p class="text-sm text-muted">No livestream briefings have been scheduled yet.</p>
            @else
                <div class="flex flex-col divide-y divide-border border-y border-border">
                    @foreach ($livestreams as $item)
                        @php
                            $tone = match ($item['status']) {
                                'upcoming' => 'success',
                                'published' => 'info',
                                default => 'neutral',
                            };
                        @endphp
                        <div class="flex flex-col gap-4 py-8 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
                            <div class="flex flex-col gap-2">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="font-serif text-lg font-semibold leading-snug text-cream">
                                        {{ $item['title'] }}
                                    </h3>
                                    <x-status-badge :label="$item['status_label']" :tone="$tone" />
                                </div>
                                <p class="max-w-2xl text-sm leading-relaxed text-body">{{ $item['description'] }}</p>
                                @if ($item['speaker'])
                                    <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $item['speaker'] }}</p>
                                @endif
                            </div>

                            @if ($item['date'])
                                <span class="inline-flex shrink-0 items-center gap-2 text-xs font-semibold uppercase tracking-wide text-muted sm:text-right">
                                    <x-icon name="clock" class="h-3.5 w-3.5 text-gold" />
                                    {{ $item['date']->format('F j, Y') }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Podcast --}}
    <section class="border-t border-border bg-ink">
        <div class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center border border-gold text-gold">
                    <x-icon name="megaphone" class="h-5 w-5" />
                </span>
                <x-section-heading eyebrow="Listen">Podcast</x-section-heading>
            </div>

            @if ($podcasts === [])
                <p class="text-sm text-muted">No podcast episodes have been published yet.</p>
            @else
                <div class="flex flex-col divide-y divide-border border-y border-border">
                    @foreach ($podcasts as $item)
                        @php
                            $tone = match ($item['status']) {
                                'upcoming' => 'success',
                                'published' => 'info',
                                default => 'neutral',
                            };
                        @endphp
                        <div class="flex flex-col gap-4 py-8 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
                            <div class="flex flex-col gap-2">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="font-serif text-lg font-semibold leading-snug text-cream">
                                        {{ $item['title'] }}
                                    </h3>
                                    <x-status-badge :label="$item['status_label']" :tone="$tone" />
                                </div>
                                <p class="max-w-2xl text-sm leading-relaxed text-body">{{ $item['description'] }}</p>
                                @if ($item['speaker'])
                                    <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $item['speaker'] }}</p>
                                @endif
                            </div>

                            @if ($item['date'])
                                <span class="inline-flex shrink-0 items-center gap-2 text-xs font-semibold uppercase tracking-wide text-muted sm:text-right">
                                    <x-icon name="clock" class="h-3.5 w-3.5 text-gold" />
                                    {{ $item['date']->format('F j, Y') }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Underground Magazine --}}
    <section class="border-t border-border bg-surface">
        <div class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center border border-gold text-gold">
                    <x-icon name="newspaper" class="h-5 w-5" />
                </span>
                <x-section-heading eyebrow="Read">Underground Magazine</x-section-heading>
            </div>

            @if ($magazineIssues === [])
                <p class="text-sm text-muted">No issues have been published yet.</p>
            @else
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($magazineIssues as $item)
                        @php
                            $tone = match ($item['status']) {
                                'upcoming' => 'success',
                                'published' => 'info',
                                default => 'neutral',
                            };
                        @endphp
                        <div class="flex flex-col gap-4 border border-border bg-ink p-6">
                            <div class="flex items-center justify-between gap-3">
                                <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold">
                                    <x-icon name="newspaper" class="h-3.5 w-3.5" />
                                    Magazine
                                </span>
                                <x-status-badge :label="$item['status_label']" :tone="$tone" />
                            </div>

                            <h3 class="font-serif text-xl font-semibold leading-snug text-cream">
                                {{ $item['title'] }}
                            </h3>

                            <p class="text-sm leading-relaxed text-body">{{ $item['description'] }}</p>

                            @if ($item['date'])
                                <span class="mt-auto inline-flex items-center gap-2 text-xs uppercase tracking-widest text-muted">
                                    <x-icon name="clock" class="h-3.5 w-3.5 text-gold" />
                                    {{ $item['date']->format('F j, Y') }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <x-faq
        tone="ink"
        eyebrow="About Flex Media Group"
        heading="Frequently Asked"
        :items="[
            ['q' => 'Who is Flex Media Group for?', 'a' => 'Principals, institutions and allocators who want our thinking directly, in long form, rather than secondhand.'],
            ['q' => 'Can I suggest a topic or pitch an episode?', 'a' => 'Yes. Write to our media team with a short outline and we will respond.'],
            ['q' => 'Is any of this confidential client work?', 'a' => 'No. Nothing published here discusses an actual client, mandate or matter without a confirmed, named engagement; illustrative examples are always identified as such.'],
        ]"
    />

    <x-cta-band tone="surface" heading="Have a story or a guest in mind?" text="Our media team reviews every pitch personally." primary-label="Contact Our Media Team" primary-href="mailto:media@un-der.com?subject=Flex%20Media%20Group" />
</x-layout>
