<x-layout title="Case Studies">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Selected Engagements">
            Case Studies
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            A sample of closed mandates, anonymised as a matter of course. No client, government, or
            institution named here or elsewhere has ever consented to being identified &mdash; nor
            will they be. Each record below states our real role, what we delivered, and whether it
            has a publication agreement on file.
        </p>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @foreach ($engagements as $engagement)
                <article class="flex flex-col gap-4 border border-border bg-surface p-8">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :label="$engagement['sector']" tone="info" />
                        @if ($engagement['period'])
                            <x-status-badge :label="$engagement['period']" tone="neutral" />
                        @endif
                    </div>

                    <h3 class="font-serif text-xl font-semibold leading-snug text-cream">
                        {{ $engagement['title'] }}
                    </h3>

                    <div class="flex flex-col gap-3">
                        <p class="text-sm leading-relaxed text-body">{{ $engagement['summary'] }}</p>

                        @if ($engagement['real_role'])
                            <p class="text-sm leading-relaxed text-body">
                                <span class="font-semibold text-cream">Our role:</span>
                                {{ $engagement['real_role'] }}
                            </p>
                        @endif

                        @if (!empty($engagement['deliverables']))
                            <div class="flex flex-col gap-1.5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-muted">Deliverables</p>
                                <ul class="flex flex-col gap-1">
                                    @foreach ($engagement['deliverables'] as $deliverable)
                                        <li class="flex items-start gap-2 text-sm leading-relaxed text-body">
                                            <x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-gold" />
                                            {{ $deliverable }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="mt-auto flex items-start gap-3 border-t border-border pt-4">
                        <x-icon name="target" class="mt-0.5 h-4 w-4 shrink-0 text-gold" />
                        <p class="text-xs font-semibold uppercase tracking-wide text-muted">
                            {{ $engagement['outcome'] }}
                        </p>
                    </div>

                    @if ($engagement['publication_agreement_note'])
                        <p class="flex items-start gap-1.5 text-[11px] leading-relaxed text-muted">
                            <x-icon name="lock" class="mt-0.5 h-3 w-3 shrink-0" />
                            {{ $engagement['publication_agreement_note'] }}
                        </p>
                    @endif
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
