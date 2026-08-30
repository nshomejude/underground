<x-layout :title="$insight->title">
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <a href="{{ route('insights.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
            Back to Insights
        </a>

        <div class="mt-8 flex flex-col gap-4">
            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold">
                <x-icon name="newspaper" class="h-3.5 w-3.5" />
                {{ $insight->category }}
            </span>

            <h1 class="font-serif text-3xl font-semibold leading-tight text-cream sm:text-4xl lg:text-5xl">
                {{ $insight->title }}
            </h1>

            <div class="flex items-center gap-3 text-xs uppercase tracking-widest text-muted">
                @if ($insight->publishedAt)
                    <span>{{ $insight->publishedAt->format('F j, Y') }}</span>
                    <span class="h-1 w-1 rounded-full bg-muted"></span>
                @endif
                <span>{{ $insight->readingMinutes() }} min read</span>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-6 border-t border-border pt-10 text-base leading-relaxed text-body">
            @foreach (preg_split('/\n{2,}/', trim($insight->body)) as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
    </article>
</x-layout>
