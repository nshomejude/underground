<x-layout title="Portfolio">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Selected Engagements">
            Portfolio
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            A sample of closed mandates, anonymised as a matter of course. No client, government, or
            institution named here or elsewhere has ever consented to being identified &mdash; nor
            will they be.
        </p>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @foreach ($engagements as $engagement)
                <article class="flex flex-col gap-4 border border-border bg-surface p-8">
                    <x-status-badge :label="$engagement['sector']" tone="info" />

                    <h3 class="font-serif text-xl font-semibold leading-snug text-cream">
                        {{ $engagement['title'] }}
                    </h3>

                    <p class="text-sm leading-relaxed text-body">{{ $engagement['summary'] }}</p>

                    <div class="mt-auto flex items-start gap-3 border-t border-border pt-4">
                        <x-icon name="target" class="mt-0.5 h-4 w-4 shrink-0 text-gold" />
                        <p class="text-xs font-semibold uppercase tracking-wide text-muted">
                            {{ $engagement['outcome'] }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="Reading the Record"
        heading="What Our Track Record Reflects"
        :items="[
            ['icon' => 'lock', 'title' => 'Anonymised by design', 'text' => 'No client, government or institution is named here. Details are generalized so that no engagement can be identified.'],
            ['icon' => 'target', 'title' => 'Outcomes that matter', 'text' => 'We record results in terms the client cares about: a policy retained, a deal closed, a crisis contained.'],
            ['icon' => 'globe', 'title' => 'Across sectors and regions', 'text' => 'The mandates shown span government, energy, infrastructure, security, technology and finance.'],
        ]"
    />

    <x-cta-band tone="ink" heading="Have a similar challenge?" text="Every engagement is different, but many begin with a problem much like those above. Tell us about yours.">
        <x-button variant="secondary" href="{{ route('capabilities.index') }}">
            Our Capabilities
            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
        </x-button>
    </x-cta-band>
</x-layout>
