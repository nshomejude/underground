@php
    $groups = [
        'Overview' => [
            ['label' => 'Dashboard', 'route' => 'admin.index', 'pattern' => 'admin.index', 'icon' => 'home'],
        ],
        'Review Queue' => [
            ['label' => 'Applications', 'route' => 'admin.applications.index', 'pattern' => 'admin.applications.*', 'icon' => 'briefcase'],
            ['label' => 'Inquiries', 'route' => 'admin.inquiries.index', 'pattern' => 'admin.inquiries.*', 'icon' => 'lock'],
        ],
        'Content' => [
            ['label' => 'Insights', 'route' => 'admin.insights.index', 'pattern' => 'admin.insights.*', 'icon' => 'newspaper'],
            ['label' => 'Capabilities', 'route' => 'admin.capabilities.index', 'pattern' => 'admin.capabilities.*', 'icon' => 'gem'],
            ['label' => 'Sectors', 'route' => 'admin.sectors.index', 'pattern' => 'admin.sectors.*', 'icon' => 'globe'],
            ['label' => 'Metrics', 'route' => 'admin.metrics.index', 'pattern' => 'admin.metrics.*', 'icon' => 'target'],
            ['label' => 'Engagement Models', 'route' => 'admin.engagement-models.index', 'pattern' => 'admin.engagement-models.*', 'icon' => 'handshake'],
            ['label' => 'Pillars', 'route' => 'admin.pillars.index', 'pattern' => 'admin.pillars.*', 'icon' => 'landmark'],
            ['label' => 'Team', 'route' => 'admin.team.index', 'pattern' => 'admin.team.*', 'icon' => 'users'],
            ['label' => 'Partners', 'route' => 'admin.partners.index', 'pattern' => 'admin.partners.*', 'icon' => 'building-2'],
            ['label' => 'Portfolio', 'route' => 'admin.portfolio.index', 'pattern' => 'admin.portfolio.*', 'icon' => 'check-circle'],
            ['label' => 'Projects', 'route' => 'admin.projects.index', 'pattern' => 'admin.projects.*', 'icon' => 'rotate-cw'],
            ['label' => 'Events', 'route' => 'admin.events.index', 'pattern' => 'admin.events.*', 'icon' => 'clock'],
        ],
        'Settings' => [
            ['label' => 'Configuration', 'route' => 'admin.settings.edit', 'pattern' => 'admin.settings.*', 'icon' => 'sliders-horizontal'],
            ['label' => 'Narrative', 'route' => 'admin.narrative.edit', 'pattern' => 'admin.narrative.*', 'icon' => 'library'],
        ],
    ];
@endphp

<nav class="flex flex-col gap-6" aria-label="Admin">
    @foreach ($groups as $group => $items)
        <div class="flex flex-col gap-1">
            <p class="px-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted">{{ $group }}</p>
            @foreach ($items as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'flex items-center gap-2.5 px-3 py-2 text-sm font-medium transition-colors',
                        'bg-gold text-onlight' => request()->routeIs($item['pattern']),
                        'text-body hover:bg-surface-raised hover:text-cream' => ! request()->routeIs($item['pattern']),
                    ])
                >
                    <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    @endforeach
</nav>
