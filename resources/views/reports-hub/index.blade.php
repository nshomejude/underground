<x-layout title="Reports Hub" description="Original analysis from Underground on geopolitics, capital flows, government affairs and infrastructure, filterable by category, country and sector.">
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Reports Hub">
            Thinking From the Underground
        </x-section-heading>

        <p class="mt-6 max-w-2xl text-base leading-relaxed text-body">
            Perspective from the practice &mdash; on geopolitics, capital, and the quiet
            mechanics of influence. Formerly Insights, now with the firm's full
            editorial metadata attached.
        </p>

        <a
            href="{{ route('reports-hub.strategic-notes') }}"
            class="mt-6 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright"
        >
            <x-icon name="file-text" class="h-3.5 w-3.5" />
            Browse Strategic Notes only
            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
        </a>

        @if ($categories !== [] || $countries !== [] || $sectors !== [])
            <form method="GET" action="{{ route('reports-hub.index') }}" class="mt-10 flex flex-wrap items-end gap-4 border border-border bg-surface p-4 sm:p-6">
                <div class="flex flex-col gap-1.5">
                    <label for="category" class="text-[11px] font-semibold uppercase tracking-wider text-muted">Category</label>
                    <select id="category" name="category" class="min-w-[9rem] border border-border bg-ink px-3 py-2 text-sm text-cream focus:border-gold focus:outline-none">
                        <option value="">All categories</option>
                        @foreach ($categories as $option)
                            <option value="{{ $option }}" @selected($filters['category'] === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="country" class="text-[11px] font-semibold uppercase tracking-wider text-muted">Country</label>
                    <select id="country" name="country" class="min-w-[9rem] border border-border bg-ink px-3 py-2 text-sm text-cream focus:border-gold focus:outline-none">
                        <option value="">All countries</option>
                        @foreach ($countries as $option)
                            <option value="{{ $option }}" @selected($filters['country'] === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="sector" class="text-[11px] font-semibold uppercase tracking-wider text-muted">Sector</label>
                    <select id="sector" name="sector" class="min-w-[9rem] border border-border bg-ink px-3 py-2 text-sm text-cream focus:border-gold focus:outline-none">
                        <option value="">All sectors</option>
                        @foreach ($sectors as $option)
                            <option value="{{ $option }}" @selected($filters['sector'] === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="sort" class="text-[11px] font-semibold uppercase tracking-wider text-muted">Sort</label>
                    <select id="sort" name="sort" class="min-w-[9rem] border border-border bg-ink px-3 py-2 text-sm text-cream focus:border-gold focus:outline-none">
                        <option value="newest" @selected($filters['sort'] === 'newest')>Newest first</option>
                        <option value="oldest" @selected($filters['sort'] === 'oldest')>Oldest first</option>
                        <option value="title" @selected($filters['sort'] === 'title')>Title (A&ndash;Z)</option>
                    </select>
                </div>

                <div class="flex items-center gap-4">
                    <x-button type="submit" variant="primary">Apply</x-button>
                    @if ($filters['category'] || $filters['country'] || $filters['sector'] || $filters['sort'] !== 'newest')
                        <a href="{{ route('reports-hub.index') }}" class="text-xs font-semibold uppercase tracking-widest text-muted hover:text-gold">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        @endif

        @if ($reports === [])
            <p class="mt-12 text-sm text-muted">No reports match these filters yet.</p>
        @else
            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($reports as $report)
                    <a
                        href="{{ route('reports-hub.show', $report->slug->value) }}"
                        class="group flex flex-col gap-4 border border-border bg-surface p-6 transition-colors hover:border-gold"
                    >
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold">
                                <x-icon name="newspaper" class="h-3.5 w-3.5" />
                                {{ $report->category }}
                            </span>
                            @if ($report->requiresMembership())
                                <x-status-badge label="Member Access" tone="info" />
                            @endif
                        </div>

                        <h3 class="font-serif text-xl font-semibold leading-snug text-cream group-hover:text-gold">
                            {{ $report->title }}
                        </h3>

                        <p class="line-clamp-3 text-sm leading-relaxed text-body">
                            {{ $report->excerpt }}
                        </p>

                        <div class="mt-auto flex flex-col gap-1 text-xs uppercase tracking-widest text-muted">
                            <span>{{ $report->author }} &middot; {{ $report->sector }}</span>
                            <div class="flex items-center gap-3">
                                @if ($report->publishedAt)
                                    <span>{{ $report->publishedAt->format('M j, Y') }}</span>
                                    <span class="h-1 w-1 rounded-full bg-muted"></span>
                                @endif
                                <span>{{ $report->readingMinutes() }} min read</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="Our Perspective"
        heading="What We Write About"
        :items="[
            ['icon' => 'globe', 'title' => 'Geopolitics', 'text' => 'How shifting alliances and strategic competition change the terms of access, trade and security.'],
            ['icon' => 'coins', 'title' => 'Capital & markets', 'text' => 'Where capital is moving, why, and what political context investors should understand.'],
            ['icon' => 'landmark', 'title' => 'Government affairs', 'text' => 'How decisions are made inside public institutions, and how to engage them well.'],
            ['icon' => 'ship-wheel', 'title' => 'Infrastructure & energy', 'text' => 'The projects and transitions that are redrawing economic and strategic maps.'],
            ['icon' => 'shield-check', 'title' => 'Security & risk', 'text' => 'Emerging risks, and how institutions can prepare for them without overreacting.'],
            ['icon' => 'newspaper', 'title' => 'Strategy & influence', 'text' => 'The quiet mechanics of persuasion, reputation and relationships.'],
        ]"
    />

    <x-cta-band tone="ink" heading="Want to contribute or be featured?" text="We welcome pitches and perspectives from practitioners and institutions with something new to say." primary-label="Contact Our Media Team" primary-href="mailto:media@un-der.com" />
</x-layout>
