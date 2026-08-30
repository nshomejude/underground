<x-layout title="Projects">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="In Flight">
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
</x-layout>
