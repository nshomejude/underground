<x-layout title="Insights">
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Insights">
            Thinking From the Underground
        </x-section-heading>

        <p class="mt-6 max-w-2xl text-base leading-relaxed text-body">
            Perspective from the practice &mdash; on geopolitics, capital, and the quiet
            mechanics of influence.
        </p>

        @if ($insights === [])
            <p class="mt-12 text-sm text-muted">No insights have been published yet.</p>
        @else
            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($insights as $insight)
                    <a
                        href="{{ route('insights.show', $insight->slug->value) }}"
                        class="group flex flex-col gap-4 border border-border bg-surface p-6 transition-colors hover:border-gold"
                    >
                        <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold">
                            <x-icon name="newspaper" class="h-3.5 w-3.5" />
                            {{ $insight->category }}
                        </span>

                        <h3 class="font-serif text-xl font-semibold leading-snug text-cream group-hover:text-gold">
                            {{ $insight->title }}
                        </h3>

                        <p class="line-clamp-3 text-sm leading-relaxed text-body">
                            {{ $insight->excerpt }}
                        </p>

                        <div class="mt-auto flex items-center gap-3 text-xs uppercase tracking-widest text-muted">
                            @if ($insight->publishedAt)
                                <span>{{ $insight->publishedAt->format('M j, Y') }}</span>
                                <span class="h-1 w-1 rounded-full bg-muted"></span>
                            @endif
                            <span>{{ $insight->readingMinutes() }} min read</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</x-layout>
