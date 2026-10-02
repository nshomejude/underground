<x-layout title="Engagement Models">
    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Engagement Models">
            How Clients Retain the Firm
        </x-section-heading>

        <p class="mt-6 max-w-2xl text-base leading-relaxed text-body">
            Four ways to bring the practice to bear &mdash; matched to the scope, duration,
            and discretion the situation demands.
        </p>

        @if ($engagementModels === [])
            <p class="mt-12 text-sm text-muted">No engagement models have been published yet.</p>
        @else
            <div class="mt-12 flex flex-col gap-px border border-border bg-border">
                @foreach ($engagementModels as $model)
                    <a
                        href="{{ route('engagement-models.show', $model->slug->value) }}"
                        class="group flex min-h-[44px] items-center gap-4 bg-ink px-6 py-5 transition-colors hover:bg-surface-raised"
                    >
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $model->icon }}" class="h-4 w-4" />
                        </span>
                        <span class="flex-1 flex flex-col gap-1">
                            <span class="text-sm font-semibold uppercase tracking-wide text-cream group-hover:text-gold">
                                {{ $model->name }}
                            </span>
                            <span class="line-clamp-2 text-sm leading-relaxed text-body">
                                {{ $model->summary }}
                            </span>
                        </span>
                        <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gold" />
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="Every Engagement"
        heading="What You Can Expect, Whichever Model You Choose"
        :items="[
            ['icon' => 'lock', 'title' => 'Confidentiality', 'text' => 'A signed confidentiality agreement at the outset. Client identities and matters are never disclosed.'],
            ['icon' => 'users', 'title' => 'Senior-led teams', 'text' => 'A named partner is accountable for every engagement and remains involved from start to finish.'],
            ['icon' => 'check-circle', 'title' => 'Clear terms', 'text' => 'Scope, fees and responsibilities are agreed in writing before work begins. No surprises.'],
            ['icon' => 'scan-line', 'title' => 'Transparent reporting', 'text' => 'Regular, plain-language updates on progress, risks and next steps.'],
            ['icon' => 'shield-check', 'title' => 'Compliance first', 'text' => 'We follow the law and the ethics rules of every jurisdiction, and we will tell you when something should not be done.'],
            ['icon' => 'handshake', 'title' => 'A relationship, not a file', 'text' => 'We aim to be the firm you call again. Our best work comes from clients who stay.'],
        ]"
    />

    <x-cta-band tone="ink" heading="Let us find the right structure." text="Tell us about your situation. We will recommend the model that fits, and be candid if another approach would serve you better." />
</x-layout>
