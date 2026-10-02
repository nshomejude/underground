<x-layout title="Projects">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="In Flight">
            Projects
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Standing initiatives currently underway across our practice areas &mdash; distinct from
            the closed mandates on our Portfolio, these are still live.
        </p>

        <div class="flex flex-col divide-y divide-border border-y border-border">
            @foreach ($projects as $project)
                <div class="flex flex-col gap-4 py-8 sm:flex-row sm:items-start sm:gap-6">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center border border-gold text-gold">
                        <x-icon name="{{ $project['icon'] }}" class="h-6 w-6" />
                    </span>

                    <div class="flex flex-1 flex-col gap-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="font-serif text-lg font-semibold leading-snug text-cream">
                                {{ $project['title'] }}
                            </h3>
                            <x-status-badge label="Ongoing" tone="success" />
                        </div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gold">{{ $project['sector'] }}</p>
                        <p class="text-sm leading-relaxed text-body">{{ $project['body'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="Why Standing Initiatives"
        heading="Work That Outlasts a Single Mandate"
        :items="[
            ['icon' => 'users', 'title' => 'Multi-party by design', 'text' => 'These initiatives bring several institutions to one table, which is where progress on shared problems is made.'],
            ['icon' => 'clock', 'title' => 'Long-horizon', 'text' => 'Policy, financing and governance do not change overnight. Our standing programs are built to run for years.'],
            ['icon' => 'radar', 'title' => 'Feeding our advice', 'text' => 'What we learn from these programs sharpens the counsel we give every client, within the bounds of confidentiality.'],
        ]"
    />

    <x-cta-band tone="ink" heading="Interested in collaborating on a project?" text="We welcome conversations with institutions working on shared challenges in these areas." primary-label="Write to Our Projects Team" primary-href="mailto:projects@un-der.com" />
</x-layout>
