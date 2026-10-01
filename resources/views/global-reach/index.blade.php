<x-layout title="Global Reach">
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:gap-16">
            <div class="flex flex-col gap-6">
                <x-section-heading eyebrow="Global Reach">{{ $narrative->reachHeading }}</x-section-heading>

                <p class="max-w-md text-base leading-relaxed text-body">
                    {{ $narrative->reachBody }}
                </p>

                <x-reach-map />
            </div>

            <div class="flex flex-col gap-px border border-border bg-border">
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
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Our Offices</h2>
            <div class="mt-6 grid grid-cols-1 gap-px bg-border sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($offices as $office)
                    <div class="flex flex-col gap-2 bg-surface p-6">
                        <x-icon name="map-pin" class="h-4 w-4 text-gold" />
                        <h3 class="font-serif text-lg font-semibold text-cream">{{ $office['city'] }}</h3>
                        <p class="text-xs uppercase tracking-wide text-muted">{{ $office['region'] }}</p>
                        <p class="text-xs uppercase tracking-wide text-gold">{{ $office['note'] }}</p>
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
</x-layout>
