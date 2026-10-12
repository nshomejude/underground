<x-layout title="Global Reach">
    <div class="relative isolate overflow-hidden">
        <x-network-grid />
        <div class="hero-veil pointer-events-none absolute inset-0" aria-hidden="true"></div>
    <section class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:gap-16">
            <div class="flex flex-col gap-6">
                <x-section-heading tag="h1" eyebrow="Global Reach">{{ $narrative->reachHeading }}</x-section-heading>

                <p class="max-w-md text-base leading-relaxed text-body">
                    {{ $narrative->reachBody }}
                </p>

                @unless (app(\Domain\Content\Repositories\SiteSettingRepository::class)->current()->globeAtlasEnabled)
                    <x-reach-map />
                @endunless
            </div>

            <div class="flex flex-col gap-px self-start border border-border bg-border">
                <div class="flex items-center justify-between gap-4 bg-ink px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">
                        {{ $narrative->engagementHeading }}
                    </p>
                    <a href="{{ route('engagement-models.index') }}" class="inline-flex shrink-0 items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
                        View All
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                    </a>
                </div>

                @foreach ($engagementModels as $model)
                    <a
                        href="{{ route('engagement-models.show', $model->slug->value) }}"
                        class="group flex min-h-[44px] items-center gap-4 bg-ink px-6 py-5 transition-colors hover:bg-surface-raised"
                    >
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $model->icon }}" class="h-4 w-4" />
                        </span>
                        <span class="flex-1 text-sm font-semibold uppercase tracking-wide text-cream group-hover:text-gold">
                            {{ $model->name }}
                        </span>
                        <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gold" />
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-16 border-t border-border pt-12">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Our Alliance Network</h2>
                    <p class="mt-3 text-sm leading-relaxed text-body">
                        Underground develops a network of alliances to facilitate access to local expertise in
                        institutional relations, public affairs, and strategic support. Services are coordinated
                        according to country, client needs, and confirmed partner capacity — never promised
                        before a partner's capacity is confirmed.
                    </p>
                </div>
                {{--
                    Route::has() guards: routes/lot1-global-reach.php is not
                    yet required from routes/web.php (see that file's own
                    header comment), so these links degrade to nothing
                    rather than fatal until it is wired in.
                --}}
                <div class="flex flex-wrap gap-4 sm:shrink-0">
                    @if (Route::has('global-alliance'))
                        <x-button variant="secondary" href="{{ route('global-alliance') }}">
                            How the Alliance Works
                            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                        </x-button>
                    @endif
                    @if (Route::has('locations'))
                        <x-button variant="secondary" href="{{ route('locations') }}">
                            Full Directory
                            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                        </x-button>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 tile-grid sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($offices as $office)
                    <div class="flex flex-col gap-2 bg-surface p-6">
                        <x-icon name="map-pin" class="h-4 w-4 text-gold" />
                        <h3 class="font-serif text-lg font-semibold text-cream">
                            {{ $office->name }}
                            @if ($office->isHeadquarters)
                                <span class="ml-1 text-[10px] font-semibold uppercase tracking-wider text-gold">HQ</span>
                            @endif
                        </h3>
                        <p class="text-xs uppercase tracking-wide text-muted">{{ $office->region }}</p>
                        <p class="text-xs uppercase tracking-wide text-gold">{{ $office->status->label() }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                <x-button variant="secondary" href="{{ route('contact') }}">
                    Contact Us
                    <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </x-button>
            </div>
        </div>
    </section>
    </div>

    <x-globe-atlas :offices="$offices" />

    <x-value-grid
        tone="surface"
        eyebrow="Where We Operate"
        heading="Regions We Cover"
        :items="[
            ['icon' => 'flag', 'title' => 'North America', 'text' => 'Washington, D.C. is our directly operated headquarters, serving policy, finance and institutional audiences across the United States. Coverage in Canada is proposed and not yet confirmed.'],
            ['icon' => 'handshake', 'title' => 'Central & West Africa', 'text' => 'Independent partners in Douala, Abidjan and Lagos extend coverage across Cameroon, Côte d’Ivoire, Nigeria and the wider CEMAC/ECOWAS communities.'],
            ['icon' => 'globe', 'title' => 'Southern & East Africa', 'text' => 'Regional partner coverage reaching South Africa, Lesotho, Eswatini, Kenya and the wider East African community, subject to confirmed partner capacity.'],
            ['icon' => 'building-2', 'title' => 'Gulf & Middle East', 'text' => 'Coordinated partner coverage across Bahrain, Kuwait, Oman, Qatar and the UAE.'],
            ['icon' => 'landmark', 'title' => 'Europe', 'text' => 'An independent partner in Paris covers France today. A broader European presence across the United Kingdom, Switzerland and Germany is proposed, not yet confirmed.'],
            ['icon' => 'map-pin', 'title' => 'Asia-Pacific', 'text' => 'Coverage under consideration. The scope and partner for this region have not yet been confirmed.'],
        ]"
        :columns="3"
    />

    <x-value-grid
        tone="ink"
        eyebrow="Our Model"
        heading="Global Network, Local Insight"
        :items="[
            ['icon' => 'users', 'title' => 'People on the ground', 'text' => 'Partners and associates who live and work in the markets we serve, and who know the people and customs that matter.'],
            ['icon' => 'handshake', 'title' => 'Trusted partners', 'text' => 'A vetted network of advisors, institutions and operators we introduce to one another when it serves the client.'],
            ['icon' => 'radar', 'title' => 'One coordinated team', 'text' => 'A single point of accountability, whichever combination of offices and specialists an engagement needs.'],
        ]"
    />

    <x-cta-band heading="Planning to operate in a new market?" text="Tell us where you are going. We will tell you who to meet, what to expect and how to begin." />
</x-layout>
