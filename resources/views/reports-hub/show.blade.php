@php
    $seoSchema = [[
        '@type' => 'Article',
        'headline' => $report->title,
        'description' => $report->excerpt,
        'url' => route('reports-hub.show', $report->slug->value),
        'mainEntityOfPage' => route('reports-hub.show', $report->slug->value),
        'datePublished' => $report->publishedAt?->format(DATE_ATOM),
        'dateModified' => $report->publishedAt?->format(DATE_ATOM),
        'articleSection' => $report->category,
        'inLanguage' => 'en',
        'author' => ['@type' => 'Person', 'name' => $report->author],
        'publisher' => ['@id' => url('/').'#organization'],
        'image' => asset(config('seo.og_image')),
    ]];
@endphp
<x-layout :title="$report->title" :description="$report->excerpt" type="article" :schema="$seoSchema">
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <a href="{{ route('reports-hub.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
            Back to Reports Hub
        </a>

        <div class="mt-8 flex flex-col gap-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold">
                    <x-icon name="newspaper" class="h-3.5 w-3.5" />
                    {{ $report->category }}
                </span>
                @if ($report->requiresMembership())
                    <x-status-badge label="Member Access" tone="info" />
                @endif
            </div>

            <h1 class="font-serif text-3xl font-semibold leading-tight text-cream sm:text-4xl lg:text-5xl">
                {{ $report->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-widest text-muted">
                <span>By {{ $report->author }}</span>
                <span class="h-1 w-1 rounded-full bg-muted"></span>
                @if ($report->publishedAt)
                    <span>{{ $report->publishedAt->format('F j, Y') }}</span>
                    <span class="h-1 w-1 rounded-full bg-muted"></span>
                @endif
                <span>{{ $report->readingMinutes() }} min read</span>
            </div>

            <div class="flex flex-wrap items-center gap-4 border-t border-border pt-4 text-xs uppercase tracking-widest text-muted">
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="map-pin" class="h-3.5 w-3.5" />
                    {{ $report->country }}
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="briefcase" class="h-3.5 w-3.5" />
                    {{ $report->sector }}
                </span>
            </div>
        </div>

        @if ($report->requiresMembership())
            <div class="mt-10 flex items-start gap-3 border border-gold/40 bg-surface p-4 text-sm text-body">
                <x-icon name="lock" class="mt-0.5 h-4 w-4 shrink-0 text-gold" />
                <p>
                    This report is reserved for Underground members. The full text is shown below for this
                    lot — member-gated access is enforced in a later release.
                </p>
            </div>
        @endif

        <div class="mt-10 flex flex-col gap-6 border-t border-border pt-10 text-base leading-relaxed text-body">
            @foreach (preg_split('/\n{2,}/', trim($report->body)) as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
    </article>
</x-layout>
