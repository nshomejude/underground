@props([
    'title',
    'eyebrow' => 'Content Admin',
])

@php
    $sections = [
        ['label' => 'Sectors', 'route' => 'admin.sectors.index', 'pattern' => 'admin.sectors.*'],
        ['label' => 'Metrics', 'route' => 'admin.metrics.index', 'pattern' => 'admin.metrics.*'],
        ['label' => 'Engagement Models', 'route' => 'admin.engagement-models.index', 'pattern' => 'admin.engagement-models.*'],
        ['label' => 'Pillars', 'route' => 'admin.pillars.index', 'pattern' => 'admin.pillars.*'],
        ['label' => 'Narrative', 'route' => 'admin.narrative.edit', 'pattern' => 'admin.narrative.*'],
    ];
@endphp

<x-layout :title="$title">
    <section class="mx-auto flex max-w-4xl flex-col gap-8 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="flex flex-col gap-6">
            <x-section-heading :eyebrow="$eyebrow">{{ $title }}</x-section-heading>

            <nav class="-mx-1 flex flex-wrap gap-1 overflow-x-auto border-b border-border pb-4" aria-label="Content admin sections">
                @foreach ($sections as $section)
                    <a
                        href="{{ route($section['route']) }}"
                        @class([
                            'shrink-0 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition-colors',
                            'bg-gold text-ink' => request()->routeIs($section['pattern']),
                            'text-body hover:text-gold' => ! request()->routeIs($section['pattern']),
                        ])
                    >
                        {{ $section['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        @if (session('status'))
            <div class="border border-success/40 bg-success/10 px-4 py-3 text-sm text-success">
                {{ session('status') }}
            </div>
        @endif

        {{ $slot }}
    </section>
</x-layout>
