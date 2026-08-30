<x-admin.layout title="Dashboard" eyebrow="Staff" description="Everything staff need to review member activity and manage site content, in one place." max-width="max-w-7xl">
    {{-- KPI cards --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-admin.stat-card label="Pending Applications" :value="$kpis['pending_applications']" icon="briefcase" tone="warning" />
        <x-admin.stat-card label="Approval Rate" :value="$kpis['approval_rate'] !== null ? $kpis['approval_rate'].'%' : '—'" icon="check-circle" tone="success" />
        <x-admin.stat-card label="Open Inquiries" :value="$kpis['open_inquiries']" icon="lock" tone="info" />
        <x-admin.stat-card label="Upcoming Events" :value="$kpis['upcoming_events']" icon="clock" tone="gold" />
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Pipeline performance --}}
        <div class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface p-5 shadow-adm-xs lg:col-span-2">
            <h3 class="text-[13px] font-semibold text-cream">Pipeline Performance</h3>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <x-admin.donut-chart
                    center-label="Applications"
                    :segments="[
                        ['label' => 'Approved', 'value' => $pipeline['applications']['approved'], 'color' => 'success'],
                        ['label' => 'Declined', 'value' => $pipeline['applications']['declined'], 'color' => 'danger'],
                        ['label' => 'Pending', 'value' => $pipeline['applications']['pending'], 'color' => 'warning'],
                    ]"
                />
                <x-admin.donut-chart
                    center-label="Inquiries"
                    :segments="[
                        ['label' => 'Open', 'value' => $pipeline['inquiries']['open'], 'color' => 'info'],
                        ['label' => 'Resolved', 'value' => $pipeline['inquiries']['resolved'], 'color' => 'muted'],
                    ]"
                />
            </div>

            <div class="flex items-center justify-between border-t border-hairline pt-4 text-[13px]">
                <span class="text-body">Insights</span>
                <span class="text-muted">{{ $kpis['published_insights'] }} published &middot; {{ $kpis['draft_insights'] }} draft</span>
            </div>
        </div>

        {{-- Upcoming events --}}
        <div class="flex flex-col gap-3 rounded-adm border border-hairline bg-surface p-5 shadow-adm-xs">
            <div class="flex items-center justify-between">
                <h3 class="text-[13px] font-semibold text-cream">Upcoming Events</h3>
                <a href="{{ route('admin.events.index') }}" class="text-xs font-medium text-gold hover:text-gold-bright">Manage</a>
            </div>

            @forelse ($upcomingEvents as $event)
                <div class="flex flex-col gap-0.5 border-b border-hairline pb-3 last:border-b-0 last:pb-0">
                    <span class="text-[13px] font-medium text-cream">{{ $event->name }}</span>
                    <span class="text-xs text-muted">{{ \Illuminate\Support\Carbon::parse($event->date)->format('M j, Y') }} &middot; {{ $event->location }}</span>
                </div>
            @empty
                <p class="text-[13px] text-muted">No upcoming events scheduled.</p>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        {{-- Recent applications --}}
        <div class="flex flex-col gap-3 rounded-adm border border-hairline bg-surface p-5 shadow-adm-xs">
            <div class="flex items-center justify-between">
                <h3 class="text-[13px] font-semibold text-cream">Recent Applications</h3>
                <a href="{{ route('admin.applications.index') }}" class="text-xs font-medium text-gold hover:text-gold-bright">Review Queue</a>
            </div>

            @forelse ($recentApplications as $application)
                <div class="flex items-center justify-between gap-3 border-b border-hairline pb-3 last:border-b-0 last:pb-0">
                    <div class="flex min-w-0 flex-col">
                        <span class="truncate text-[13px] font-medium text-cream">{{ $application->applicant_name }}</span>
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
                <p class="text-[13px] text-muted">No applications yet.</p>
            @endforelse
        </div>

        {{-- Recent inquiries --}}
        <div class="flex flex-col gap-3 rounded-adm border border-hairline bg-surface p-5 shadow-adm-xs">
            <div class="flex items-center justify-between">
                <h3 class="text-[13px] font-semibold text-cream">Recent Inquiries</h3>
                <a href="{{ route('admin.inquiries.index') }}" class="text-xs font-medium text-gold hover:text-gold-bright">Review Queue</a>
            </div>

            @forelse ($recentInquiries as $inquiry)
                <div class="flex items-center justify-between gap-3 border-b border-hairline pb-3 last:border-b-0 last:pb-0">
                    <div class="flex min-w-0 flex-col">
                        <span class="truncate text-[13px] font-medium text-cream">{{ $inquiry->name }}</span>
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
                <p class="text-[13px] text-muted">No inquiries yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Content library --}}
    <div class="flex flex-col gap-3 rounded-adm border border-hairline bg-surface p-5 shadow-adm-xs">
        <h3 class="text-[13px] font-semibold text-cream">Content Library</h3>
        <x-admin.bar-chart :items="$contentCounts" />
    </div>

    {{-- Quick access --}}
    <div class="flex flex-col gap-3">
        <h3 class="text-[13px] font-semibold text-cream">Quick Access</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
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
                    ['label' => 'Configuration', 'description' => 'General site info, social links, SEO, and maintenance mode.', 'route' => 'admin.settings.edit', 'icon' => 'sliders-horizontal'],
                ];
            @endphp
            @foreach ($sections as $section)
                <a
                    href="{{ route($section['route']) }}"
                    class="group flex flex-col gap-2.5 rounded-adm border border-hairline bg-surface p-4 shadow-adm-xs transition-colors hover:border-gold/40"
                >
                    <span class="flex h-8 w-8 items-center justify-center rounded-adm bg-gold/10 text-gold">
                        <x-icon name="{{ $section['icon'] }}" class="h-4 w-4" />
                    </span>
                    <span class="text-[13px] font-semibold text-cream">
                        {{ $section['label'] }}
                    </span>
                    <span class="text-[13px] leading-relaxed text-muted">
                        {{ $section['description'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</x-admin.layout>
