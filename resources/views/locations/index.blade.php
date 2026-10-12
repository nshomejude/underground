<x-layout
    title="Locations"
    description="The full Underground alliance directory: every office and regional coverage entry, with its status and relationship type labelled plainly."
>
    @php
        $statusTone = fn ($status) => match ($status->value) {
            'active_office' => 'success',
            'representative_office' => 'info',
            'partner_coverage' => 'info',
            'planned' => 'warning',
            default => 'neutral',
        };
    @endphp

    <section class="mx-auto flex max-w-6xl flex-col gap-8 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="The Full Directory">
            Locations
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Every office and regional coverage entry in Underground's alliance network, with its status
            and relationship type labelled plainly — no location is implied to be more than this says it
            is. See <a href="{{ route('global-alliance') }}" class="text-gold hover:text-gold-bright">the
            Alliance Model</a> for what each status and relationship type actually means.
        </p>

        <div class="flex flex-col gap-6">
            @foreach ($offices as $office)
                <article class="flex flex-col gap-4 border border-border bg-surface p-6 lg:flex-row lg:items-start lg:justify-between lg:gap-8 lg:p-8">
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="font-serif text-xl font-semibold text-cream">{{ $office->name }}</h2>
                            @if ($office->isHeadquarters)
                                <x-status-badge label="Headquarters" tone="success" />
                            @endif
                        </div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $office->region }}</p>

                        <p class="max-w-2xl text-sm leading-relaxed text-body">{{ $office->blurb }}</p>

                        @if (! empty($office->countries))
                            <div class="flex flex-wrap gap-2">
                                @foreach ($office->countries as $country)
                                    <span class="inline-flex items-center border border-border px-2.5 py-1 text-[11px] uppercase tracking-wide text-muted">
                                        {{ $country }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if ($office->address || $office->phone || $office->email)
                            <div class="flex flex-col gap-1 border-t border-border pt-3 text-sm text-body">
                                @if ($office->address)
                                    <p class="leading-relaxed">{!! nl2br(e($office->address)) !!}</p>
                                @endif
                                @if ($office->phone)
                                    <a href="tel:{{ preg_replace('/[^+\d]/', '', $office->phone) }}" class="hover:text-gold">{{ $office->phone }}</a>
                                @endif
                                @if ($office->email)
                                    <a href="mailto:{{ $office->email }}" class="hover:text-gold">{{ $office->email }}</a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex shrink-0 flex-row flex-wrap gap-2 lg:flex-col lg:items-end">
                        <x-status-badge :label="$office->status->label()" :tone="$statusTone($office->status)" />
                        <x-status-badge :label="$office->relationshipType->label()" tone="neutral" />
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <x-faq
        tone="surface"
        eyebrow="Reading This Page"
        heading="Directory Questions"
        :items="[
            ['q' => 'Does a \"Planned\" entry mean you already have a partner there?', 'a' => 'No. Planned means the location is under consideration only — no local presence or partner has been confirmed yet. We will never represent it as more than that.'],
            ['q' => 'Are regional offices the same as Underground branches?', 'a' => 'No, unless labelled Active Office. A regional office entry describes coordinated partner coverage across the countries listed, through an independent firm or principal — not Underground staff.'],
            ['q' => 'Why does one country appear under more than one entry?', 'a' => 'Some coverage areas were defined by the client brief as proposed, separately from an existing partner relationship in the same country. Where that overlap exists, both entries are shown rather than silently merged.'],
        ]"
    />

    <x-cta-band
        heading="Looking to work in one of these markets?"
        text="Tell us the country and what you need. We will tell you plainly whether coverage there is confirmed today."
    >
        <x-button variant="secondary" href="{{ route('global-reach') }}">
            Back to Global Reach
        </x-button>
    </x-cta-band>
</x-layout>
