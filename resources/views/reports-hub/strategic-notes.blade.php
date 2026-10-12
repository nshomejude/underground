<x-layout title="Strategic Notes" description="Underground's Strategic Notes: shorter-form strategic analysis from the Reports Hub, filtered to the Strategic Notes category.">
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <a href="{{ route('reports-hub.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
            Back to Reports Hub
        </a>

        <x-section-heading tag="h1" eyebrow="Strategic Notes" class="mt-8">
            Strategic Notes
        </x-section-heading>

        <p class="mt-6 max-w-2xl text-base leading-relaxed text-body">
            Shorter-form strategic analysis from the Reports Hub, held to the same
            editorial standard as our longer pieces.
        </p>

        @if ($reports === [])
            <p class="mt-12 text-sm text-muted">No Strategic Notes have been published yet.</p>
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
</x-layout>
