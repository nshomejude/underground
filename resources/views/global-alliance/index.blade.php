<x-layout
    title="The Alliance Model"
    description="How Underground's network of independent partners works: status and relationship types explained, plainly."
>
    <div class="relative isolate overflow-hidden">
        <x-network-grid />
        <div class="hero-veil pointer-events-none absolute inset-0" aria-hidden="true"></div>
        <section class="relative z-10 mx-auto flex max-w-4xl flex-col gap-6 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <x-section-heading tag="h1" eyebrow="The Alliance Model">
                A network of independent partners, not a chain of branches.
            </x-section-heading>

            <p class="max-w-2xl text-base leading-relaxed text-body">
                Underground develops a network of alliances to facilitate access to local expertise in
                institutional relations, public affairs, and strategic support. Coverage is coordinated
                according to country, client needs, and confirmed partner capacity — we extend a mandate
                into a market only once a partner there has actually confirmed it can take it on.
            </p>

            <div class="flex flex-col gap-2 border-l-2 border-gold bg-surface p-6 sm:p-8">
                <p class="text-sm font-semibold uppercase tracking-wide text-gold">The one rule that never bends</p>
                <p class="max-w-2xl text-sm leading-relaxed text-body">
                    A partner is an independent firm or principal we work alongside — never an Underground
                    employee, and never a subsidiary. The only location Underground itself directly
                    operates and staffs is our Washington, D.C. headquarters. Everywhere else, the status
                    badge below tells you exactly what kind of relationship it actually is.
                </p>
            </div>
        </section>
    </div>

    <section class="border-t border-border bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <x-section-heading eyebrow="Status, Explained">
                What each status does — and does not — claim
            </x-section-heading>

            <div class="mt-10 grid grid-cols-1 tile-grid sm:grid-cols-2">
                @foreach ($statuses as $status)
                    <div class="flex flex-col gap-2 bg-ink p-6 lg:p-8">
                        <span class="inline-flex w-fit items-center border border-gold px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider text-gold">
                            {{ $status->label() }}
                        </span>
                        <p class="text-sm leading-relaxed text-body">{{ $status->description() }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-t border-border bg-ink">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <x-section-heading eyebrow="Relationship Type, Explained">
                Who the partner actually is to Underground
            </x-section-heading>

            <div class="mt-10 grid grid-cols-1 tile-grid sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($relationshipTypes as $type)
                    <div class="flex flex-col gap-2 bg-surface p-6 lg:p-8">
                        <span class="inline-flex w-fit items-center border border-gold px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider text-gold">
                            {{ $type->label() }}
                        </span>
                        <p class="text-sm leading-relaxed text-body">{{ $type->description() }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="How Coverage Works"
        heading="Three things decide whether we take a mandate into a market"
        :items="[
            ['icon' => 'flag', 'title' => 'Country', 'text' => 'Whether Underground or an independent partner already has standing, relationships or a confirmed presence in the country a mandate touches.'],
            ['icon' => 'target', 'title' => 'Client Needs', 'text' => 'What the mandate actually requires on the ground — government access, logistics, local counsel — rather than what a map implies we can offer.'],
            ['icon' => 'handshake', 'title' => 'Confirmed Partner Capacity', 'text' => 'We only commit to a market once a named partner there has confirmed they have the bandwidth and standing to take it on.'],
        ]"
        :columns="3"
    />

    <x-faq
        tone="ink"
        eyebrow="Working With the Network"
        heading="Alliance Questions"
        :items="[
            ['q' => 'Are your partners Underground employees?', 'a' => 'No. Every partner is an independent firm or principal, retained under one of the relationship types explained above. Only our Washington, D.C. headquarters is directly operated and staffed by Underground.'],
            ['q' => 'What does \"Planned\" or \"Partner Coverage\" actually mean for my mandate?', 'a' => 'It means exactly what the label says — see the status explanations above. We will tell you plainly whether a market is covered by a confirmed partner today, or whether it is still under consideration, before we take on a mandate there.'],
            ['q' => 'Can I see the full list of offices and coverage?', 'a' => 'Yes — the Locations directory lists every office and regional coverage entry with its status and relationship type labelled.'],
        ]"
    />

    <x-cta-band
        heading="Want the full, unfiltered directory?"
        text="Every office and regional coverage entry, with its status and relationship type labelled plainly."
        primary-label="View the Locations Directory"
        :primary-href="route('locations')"
    >
        <x-button variant="secondary" href="{{ route('global-reach') }}">
            Back to Global Reach
        </x-button>
    </x-cta-band>
</x-layout>
