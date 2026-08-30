<x-admin.layout title="Dashboard" eyebrow="Staff" max-width="max-w-7xl">
    <p class="max-w-2xl text-base leading-relaxed text-body">
        Everything staff need to review member activity and manage site content, in one place.
    </p>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="flex flex-col gap-2 border border-border bg-surface p-5">
            <span class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-muted">
                <x-icon name="briefcase" class="h-3.5 w-3.5 text-gold" />
                Pending Applications
            </span>
            <span class="font-serif text-3xl font-semibold text-cream">{{ $kpis['pending_applications'] }}</span>
        </div>

        <div class="flex flex-col gap-2 border border-border bg-surface p-5">
            <span class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-muted">
                <x-icon name="check-circle" class="h-3.5 w-3.5 text-gold" />
                Approval Rate
            </span>
            <span class="font-serif text-3xl font-semibold text-cream">
                {{ $kpis['approval_rate'] !== null ? $kpis['approval_rate'].'%' : '—' }}
            </span>
        </div>

        <div class="flex flex-col gap-2 border border-border bg-surface p-5">
            <span class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-muted">
                <x-icon name="lock" class="h-3.5 w-3.5 text-gold" />
                Open Inquiries
            </span>
            <span class="font-serif text-3xl font-semibold text-cream">{{ $kpis['open_inquiries'] }}</span>
        </div>

        <div class="flex flex-col gap-2 border border-border bg-surface p-5">
            <span class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-muted">
                <x-icon name="clock" class="h-3.5 w-3.5 text-gold" />
                Upcoming Events
            </span>
            <span class="font-serif text-3xl font-semibold text-cream">{{ $kpis['upcoming_events'] }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Pipeline performance --}}
        <div class="flex flex-col gap-6 border border-border bg-surface p-6 lg:col-span-2">
            <h3 class="text-xs font-semibold uppercase tracking-widest text-muted">Pipeline Performance</h3>

            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-body">Applications ({{ $pipeline['applications']['total'] }} total)</span>
                        <span class="text-muted">{{ $pipeline['applications']['pending'] }} pending</span>
                    </div>
                    @php
                        $appTotal = max(1, $pipeline['applications']['total']);
                        $approvedPct = $pipeline['applications']['approved'] / $appTotal * 100;
                        $declinedPct = $pipeline['applications']['declined'] / $appTotal * 100;
                        $pendingPct = $pipeline['applications']['pending'] / $appTotal * 100;
                    @endphp
                    <div class="flex h-2 w-full overflow-hidden bg-border">
                        <div class="h-full bg-success" style="width: {{ $approvedPct }}%"></div>
                        <div class="h-full bg-danger" style="width: {{ $declinedPct }}%"></div>
                        <div class="h-full bg-warning" style="width: {{ $pendingPct }}%"></div>
                    </div>
                    <div class="flex flex-wrap gap-4 text-xs text-muted">
                        <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 bg-success"></span> {{ $pipeline['applications']['approved'] }} approved</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 bg-danger"></span> {{ $pipeline['applications']['declined'] }} declined</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 bg-warning"></span> {{ $pipeline['applications']['pending'] }} pending</span>
                    </div>
                </div>

                <div class="flex flex-col gap-2 border-t border-border pt-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-body">Inquiries ({{ $pipeline['inquiries']['total'] }} total)</span>
                        <span class="text-muted">{{ $pipeline['inquiries']['open'] }} open</span>
                    </div>
                    @php
                        $inqTotal = max(1, $pipeline['inquiries']['total']);
                        $openPct = $pipeline['inquiries']['open'] / $inqTotal * 100;
                        $resolvedPct = $pipeline['inquiries']['resolved'] / $inqTotal * 100;
                    @endphp
                    <div class="flex h-2 w-full overflow-hidden bg-border">
                        <div class="h-full bg-info" style="width: {{ $openPct }}%"></div>
                        <div class="h-full bg-muted" style="width: {{ $resolvedPct }}%"></div>
                    </div>
                    <div class="flex flex-wrap gap-4 text-xs text-muted">
                        <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 bg-info"></span> {{ $pipeline['inquiries']['open'] }} open</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 bg-muted"></span> {{ $pipeline['inquiries']['resolved'] }} resolved</span>
                    </div>
                </div>

                <div class="flex flex-col gap-2 border-t border-border pt-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-body">Insights</span>
                        <span class="text-muted">{{ $kpis['published_insights'] }} published &middot; {{ $kpis['draft_insights'] }} draft</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upcoming events --}}
        <div class="flex flex-col gap-4 border border-border bg-surface p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-muted">Upcoming Events</h3>
                <a href="{{ route('admin.events.index') }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">Manage</a>
            </div>

            @forelse ($upcomingEvents as $event)
                <div class="flex flex-col gap-0.5 border-b border-border pb-3 last:border-b-0 last:pb-0">
                    <span class="text-sm font-semibold text-cream">{{ $event->name }}</span>
                    <span class="text-xs text-muted">{{ \Illuminate\Support\Carbon::parse($event->date)->format('M j, Y') }} &middot; {{ $event->location }}</span>
                </div>
            @empty
                <p class="text-sm text-muted">No upcoming events scheduled.</p>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Recent applications --}}
        <div class="flex flex-col gap-4 border border-border bg-surface p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-muted">Recent Applications</h3>
                <a href="{{ route('admin.applications.index') }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">Review Queue</a>
            </div>

            @forelse ($recentApplications as $application)
                <div class="flex items-center justify-between gap-3 border-b border-border pb-3 last:border-b-0 last:pb-0">
                    <div class="flex min-w-0 flex-col">
                        <span class="truncate text-sm font-semibold text-cream">{{ $application->applicant_name }}</span>
                        <span class="font-mono text-xs text-muted">{{ $application->reference }}</span>
                    </div>
                    <x-status-badge
                        :label="ucfirst(str_replace('_', ' ', $application->status))"
                        :tone="match ($application->status) {
                            'approved' => 'success',
                            'declined' => 'danger',
                            'under_review' => 'warning',
                            default => 'info',
                        }"
                    />
                </div>
            @empty
                <p class="text-sm text-muted">No applications yet.</p>
            @endforelse
        </div>

        {{-- Recent inquiries --}}
        <div class="flex flex-col gap-4 border border-border bg-surface p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-muted">Recent Inquiries</h3>
                <a href="{{ route('admin.inquiries.index') }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">Review Queue</a>
            </div>

            @forelse ($recentInquiries as $inquiry)
                <div class="flex items-center justify-between gap-3 border-b border-border pb-3 last:border-b-0 last:pb-0">
                    <div class="flex min-w-0 flex-col">
                        <span class="truncate text-sm font-semibold text-cream">{{ $inquiry->name }}</span>
                        <span class="font-mono text-xs text-muted">{{ $inquiry->reference }}</span>
                    </div>
                    <x-status-badge
                        :label="ucfirst(str_replace('_', ' ', $inquiry->status))"
                        :tone="match ($inquiry->status) {
                            'engaged' => 'success',
                            'declined' => 'danger',
                            'under_review' => 'warning',
                            'archived' => 'neutral',
                            default => 'info',
                        }"
                    />
                </div>
            @empty
                <p class="text-sm text-muted">No inquiries yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Content library quick access --}}
    <div class="flex flex-col gap-4">
        <h3 class="text-xs font-semibold uppercase tracking-widest text-muted">Content Library</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($contentCounts as $item)
                <a href="{{ route($item['route']) }}" class="group flex flex-col gap-1 border border-border bg-surface p-4 transition-colors hover:border-gold">
                    <span class="font-serif text-2xl font-semibold text-cream group-hover:text-gold-bright">{{ $item['count'] }}</span>
                    <span class="text-xs font-semibold uppercase tracking-wide text-muted group-hover:text-gold">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Quick access --}}
    <div class="flex flex-col gap-4">
        <h3 class="text-xs font-semibold uppercase tracking-widest text-muted">Quick Access</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $sections = [
                    ['label' => 'Applications', 'description' => 'Review and approve or decline membership applications.', 'route' => 'admin.applications.index', 'icon' => 'briefcase'],
                    ['label' => 'Inquiries', 'description' => 'Work confidential inquiries through the review pipeline.', 'route' => 'admin.inquiries.index', 'icon' => 'lock'],
                    ['label' => 'Insights', 'description' => 'Publish and manage editorial insights content.', 'route' => 'admin.insights.index', 'icon' => 'newspaper'],
                    ['label' => 'Capabilities', 'description' => 'Maintain the capabilities shown across the site.', 'route' => 'admin.capabilities.index', 'icon' => 'gem'],
                    ['label' => 'Sectors', 'description' => 'Manage the sectors the network operates across.', 'route' => 'admin.sectors.index', 'icon' => 'globe'],
                    ['label' => 'Metrics', 'description' => 'Update the headline metrics shown to members.', 'route' => 'admin.metrics.index', 'icon' => 'target'],
                    ['label' => 'Engagement Models', 'description' => 'Curate the ways members engage with the network.', 'route' => 'admin.engagement-models.index', 'icon' => 'handshake'],
                    ['label' => 'Pillars', 'description' => 'Manage the organization\'s foundational pillars.', 'route' => 'admin.pillars.index', 'icon' => 'landmark'],
                    ['label' => 'Team', 'description' => 'Manage the leadership bios shown on /team.', 'route' => 'admin.team.index', 'icon' => 'users'],
                    ['label' => 'Partners', 'description' => 'Manage the partner categories shown on /partners.', 'route' => 'admin.partners.index', 'icon' => 'building-2'],
                    ['label' => 'Portfolio', 'description' => 'Manage the past engagements shown on /portfolio.', 'route' => 'admin.portfolio.index', 'icon' => 'check-circle'],
                    ['label' => 'Projects', 'description' => 'Manage the ongoing initiatives shown on /projects.', 'route' => 'admin.projects.index', 'icon' => 'rotate-cw'],
                    ['label' => 'Events', 'description' => 'Manage the forums shown on /events.', 'route' => 'admin.events.index', 'icon' => 'clock'],
                    ['label' => 'Narrative', 'description' => 'Edit the singleton narrative copy block.', 'route' => 'admin.narrative.edit', 'icon' => 'library'],
                ];
            @endphp
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
    </div>
</x-admin.layout>
