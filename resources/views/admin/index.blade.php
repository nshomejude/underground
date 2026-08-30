@php
    $sections = [
        [
            'label' => 'Applications',
            'description' => 'Review and approve or decline membership applications.',
            'route' => 'admin.applications.index',
            'icon' => 'briefcase',
        ],
        [
            'label' => 'Inquiries',
            'description' => 'Work confidential inquiries through the review pipeline.',
            'route' => 'admin.inquiries.index',
            'icon' => 'lock',
        ],
        [
            'label' => 'Insights',
            'description' => 'Publish and manage editorial insights content.',
            'route' => 'admin.insights.index',
            'icon' => 'newspaper',
        ],
        [
            'label' => 'Capabilities',
            'description' => 'Maintain the capabilities shown across the site.',
            'route' => 'admin.capabilities.index',
            'icon' => 'gem',
        ],
        [
            'label' => 'Sectors',
            'description' => 'Manage the sectors the network operates across.',
            'route' => 'admin.sectors.index',
            'icon' => 'globe',
        ],
        [
            'label' => 'Metrics',
            'description' => 'Update the headline metrics shown to members.',
            'route' => 'admin.metrics.index',
            'icon' => 'target',
        ],
        [
            'label' => 'Engagement Models',
            'description' => 'Curate the ways members engage with the network.',
            'route' => 'admin.engagement-models.index',
            'icon' => 'handshake',
        ],
        [
            'label' => 'Pillars',
            'description' => 'Manage the organization\'s foundational pillars.',
            'route' => 'admin.pillars.index',
            'icon' => 'landmark',
        ],
        [
            'label' => 'Narrative',
            'description' => 'Edit the singleton narrative copy block.',
            'route' => 'admin.narrative.edit',
            'icon' => 'library',
        ],
    ];
@endphp

<x-layout title="Admin">
    <section class="mx-auto flex max-w-5xl flex-col gap-8 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Staff">Admin</x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Everything staff need to review member activity and manage site content, in one place.
        </p>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($sections as $section)
                <a
                    href="{{ route($section['route']) }}"
                    class="group flex flex-col gap-3 border border-border bg-surface p-6 transition-colors hover:border-gold"
                >
                    <x-icon name="{{ $section['icon'] }}" class="h-6 w-6 text-gold" />
                    <span class="text-sm font-semibold uppercase tracking-widest text-cream group-hover:text-gold-bright">
                        {{ $section['label'] }}
                    </span>
                    <span class="text-sm leading-relaxed text-muted">
                        {{ $section['description'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </section>
</x-layout>
