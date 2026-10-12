@php
    $seoSchema = [[
        '@type' => 'GovernmentService',
        'name' => 'Government & Head-of-State Advisory',
        'description' => 'A discreet, prepared channel for heads of state, ministers and cabinet offices considering a cooperation framework with Underground Network.',
        'provider' => ['@id' => url('/').'#organization'],
        'areaServed' => config('seo.organization.area_served'),
        'serviceType' => 'Government and sovereign advisory',
    ]];

    $steps = [
        [
            'icon' => 'radar',
            'step' => 'Initial Diagnostic',
            'text' => 'A confidential first exchange to understand the decision or relationship a principal is weighing, the institutions already involved, and what success would concretely look like.',
        ],
        [
            'icon' => 'target',
            'step' => 'Cooperation Roadmap',
            'text' => 'A short, written roadmap setting out the sequence of conversations, the institutions and offices to involve, and a realistic timeline — agreed before any meeting is scheduled.',
        ],
        [
            'icon' => 'users',
            'step' => 'Engagement & Meeting Preparation',
            'text' => 'Every meeting is prepared in advance: a clear agenda, briefing notes for each principal, and protocol guidance so the conversation stays focused and productive.',
        ],
        [
            'icon' => 'handshake',
            'step' => 'Relevant Partners',
            'text' => 'Where a mandate calls for financial, technical or institutional partners, we draw on our existing network rather than introducing names at random.',
        ],
        [
            'icon' => 'file-text',
            'step' => 'Follow-Up Reporting',
            'text' => 'A written debrief after every meeting and a short status report at agreed intervals, so principals and their offices always know where things stand.',
        ],
    ];
@endphp
<x-layout
    title="Government & Head-of-State Advisory"
    description="How Underground Network prepares a government engagement: the initial diagnostic, the cooperation roadmap, meeting preparation, relevant partners and follow-up reporting."
    :schema="$seoSchema"
>
    {{-- Header --}}
    <section class="relative isolate overflow-hidden border-b border-border bg-ink">
        <x-capitol-vignette class="opacity-70" />
        <div class="hero-veil pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="relative z-10 mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <a href="{{ route('capabilities.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
                <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
                Back to Capabilities
            </a>

            <div class="mt-8 flex flex-col gap-6">
                <span class="flex h-14 w-14 items-center justify-center border border-gold text-gold">
                    <x-icon name="landmark" class="h-7 w-7" />
                </span>

                <div class="flex flex-col gap-3">
                    <x-status-badge label="For Heads of State & Cabinet Offices" tone="info" />

                    <h1 class="font-serif text-3xl font-semibold leading-tight text-cream sm:text-4xl lg:text-5xl">
                        Government &amp; Head-of-State Advisory
                    </h1>
                </div>

                <p class="max-w-3xl text-lg leading-relaxed text-body">
                    A dedicated channel for heads of state, ministers and cabinet offices considering a
                    cooperation framework with Underground Network — what to expect from the first
                    conversation, how a visit is prepared, and how we report back afterward.
                </p>
            </div>
        </div>
    </section>

    {{-- Overview --}}
    <section class="border-b border-border bg-surface">
        <div class="mx-auto grid max-w-5xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:gap-16 lg:px-8 lg:py-20">
            <x-section-heading eyebrow="Overview" class="lg:col-span-1">A Discreet, Prepared Channel</x-section-heading>
            <div class="flex flex-col gap-5 lg:col-span-2">
                <p class="text-base leading-relaxed text-body">
                    Underground Network is a strategic advisory and economic-diplomacy firm operating from
                    Washington, D.C. We help institutions, companies and project sponsors analyze, prepare
                    and coordinate the international partnerships and defined missions that matter most to
                    them, with a particular focus on Africa.
                </p>
                <p class="text-base leading-relaxed text-body">
                    For sovereign principals, that work follows an approach we call
                    <strong class="text-cream">Submarine Diplomacy</strong>: a discreet, thoroughly prepared
                    and confidential method of dialogue, conducted at the appropriate level and through a
                    country's own institutions. It commits us to no outcome we cannot responsibly deliver —
                    only to a conversation that is well prepared, well timed, and treated in confidence.
                </p>
                <p class="text-base leading-relaxed text-body">
                    This page sets out, concretely, how that engagement unfolds: the initial diagnostic, the
                    roadmap that follows it, how we prepare meetings, the partners we draw on, and how we
                    report back once a visit or a conversation has taken place.
                </p>
            </div>
        </div>
    </section>

    {{-- Process --}}
    <section class="border-b border-border bg-ink">
        <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <x-section-heading eyebrow="How We Prepare an Engagement">
                From First Contact to Follow-Up Report
            </x-section-heading>

            <ol class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($steps as $item)
                    <li class="flex flex-col gap-3 border-t-2 border-gold pt-5">
                        <span class="flex h-9 w-9 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $item['icon'] }}" class="h-4 w-4" />
                        </span>
                        <span class="text-xs font-semibold uppercase tracking-[0.25em] text-muted">Step {{ $loop->iteration }}</span>
                        <h3 class="font-serif text-xl font-semibold text-cream">{{ $item['step'] }}</h3>
                        <p class="text-sm leading-relaxed text-body">{{ $item['text'] }}</p>
                        @if ($item['step'] === 'Relevant Partners')
                            <a href="{{ route('partners') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
                                See our partner network
                                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                            </a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Illustrative scenario --}}
    <section class="border-b border-border bg-surface">
        <div class="mx-auto grid max-w-5xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:gap-16 lg:px-8 lg:py-20">
            <x-section-heading eyebrow="Illustrative Only" class="lg:col-span-1">How an Engagement Unfolds</x-section-heading>

            <div class="flex flex-col gap-5 lg:col-span-2">
                <p class="text-sm uppercase tracking-wide text-muted">
                    The following is a hypothetical example, not a description of a confirmed mandate or
                    client relationship.
                </p>

                <div class="flex flex-col divide-y divide-border border-y border-border">
                    <div class="flex items-start gap-4 py-5">
                        <x-icon name="clock" class="mt-1 h-5 w-5 shrink-0 text-gold" />
                        <div>
                            <p class="font-serif text-base font-semibold text-cream">A sovereign partner is considering a cooperation framework</p>
                            <p class="mt-1 text-sm leading-relaxed text-body">A cabinet office reaches out through a confidential inquiry to explore how Underground Network could support a specific economic or diplomatic objective.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 py-5">
                        <x-icon name="clock" class="mt-1 h-5 w-5 shrink-0 text-gold" />
                        <div>
                            <p class="font-serif text-base font-semibold text-cream">Diagnostic and roadmap</p>
                            <p class="mt-1 text-sm leading-relaxed text-body">We clarify the objective, map the institutions on both sides, and agree a roadmap for how a visit or a sequence of conversations would be structured.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 py-5">
                        <x-icon name="clock" class="mt-1 h-5 w-5 shrink-0 text-gold" />
                        <div>
                            <p class="font-serif text-base font-semibold text-cream">A prepared visit</p>
                            <p class="mt-1 text-sm leading-relaxed text-body">Meetings are scheduled with clear agendas and briefing notes, through the appropriate institutional channels on both sides — never around them.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 py-5">
                        <x-icon name="clock" class="mt-1 h-5 w-5 shrink-0 text-gold" />
                        <div>
                            <p class="font-serif text-base font-semibold text-cream">Follow-up and reporting</p>
                            <p class="mt-1 text-sm leading-relaxed text-body">A written debrief follows each meeting, with a status report at agreed intervals as any resulting cooperation is carried forward.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Related --}}
    @if ($relatedCapabilities !== [] || $relatedEngagementModels !== [] || $sector)
        <section class="border-b border-border bg-ink">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-12 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-20">
                @if ($relatedCapabilities !== [])
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Related Capabilities</h2>
                        <ul class="mt-5 flex flex-col divide-y divide-border border-y border-border">
                            @foreach ($relatedCapabilities as $capability)
                                <li>
                                    <a href="{{ route('capabilities.show', $capability->slug->value) }}" class="group flex items-center gap-4 py-4">
                                        <x-icon name="{{ $capability->icon }}" class="h-5 w-5 shrink-0 text-gold" />
                                        <span class="flex-1 text-sm font-semibold text-cream group-hover:text-gold">{{ $capability->title }}</span>
                                        <x-icon name="chevron-right" class="h-4 w-4 text-gold" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex flex-col gap-10">
                    @if ($sector)
                        <div>
                            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Sector</h2>
                            <a href="{{ route('sectors.show', $sector->slug->value) }}" class="group mt-5 flex items-center gap-4 border-y border-border py-4">
                                <x-icon name="{{ $sector->motif }}" class="h-5 w-5 shrink-0 text-gold" />
                                <span class="flex-1 text-sm font-semibold text-cream group-hover:text-gold">{{ $sector->name }}</span>
                                <x-icon name="chevron-right" class="h-4 w-4 text-gold" />
                            </a>
                        </div>
                    @endif

                    @if ($relatedEngagementModels !== [])
                        <div>
                            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">How You Can Engage Us</h2>
                            <ul class="mt-5 flex flex-col divide-y divide-border border-y border-border">
                                @foreach ($relatedEngagementModels as $model)
                                    <li>
                                        <a href="{{ route('engagement-models.show', $model->slug->value) }}" class="group flex items-center gap-4 py-4">
                                            <x-icon name="{{ $model->icon }}" class="h-5 w-5 shrink-0 text-gold" />
                                            <span class="flex-1 text-sm font-semibold text-cream group-hover:text-gold">{{ $model->name }}</span>
                                            <x-icon name="chevron-right" class="h-4 w-4 text-gold" />
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="bg-ink">
        <div class="mx-auto flex max-w-5xl flex-col items-start gap-6 px-4 py-16 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 lg:py-20">
            <div class="flex flex-col gap-2">
                <h2 class="font-serif text-2xl font-semibold text-cream sm:text-3xl">Considering a cooperation framework?</h2>
                <p class="max-w-xl text-sm leading-relaxed text-body">A partner reviews every inquiry personally. Nothing you share leaves the firm.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <x-button
                    variant="primary"
                    href="{{ \Illuminate\Support\Facades\Route::has('consultations.create') ? route('consultations.create') : route('inquiries.create') }}"
                >
                    Submit a Request
                    <x-icon name="lock" class="h-3.5 w-3.5" />
                </x-button>
                <x-button variant="secondary" href="{{ route('capabilities.index') }}">
                    All Capabilities
                    <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </x-button>
            </div>
        </div>
    </section>
</x-layout>
