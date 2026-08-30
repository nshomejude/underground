<x-layout title="Events">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Invitation Only">
            Events
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            We convene a small number of closed forums each year, bringing principals together under
            the same discretion every mandate is run under. Attendance is by invitation only.
        </p>

        <div class="flex flex-col divide-y divide-border border-y border-border">
            @foreach ($events as $event)
                <div class="flex flex-col gap-4 py-8 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="font-serif text-lg font-semibold leading-snug text-cream">
                                {{ $event['name'] }}
                            </h3>
                            @if ($event['is_past'])
                                <x-status-badge label="Past" tone="neutral" />
                            @else
                                <x-status-badge label="Upcoming" tone="success" />
                            @endif
                        </div>
                        <p class="text-sm leading-relaxed text-body">{{ $event['description'] }}</p>
                    </div>

                    <div class="flex shrink-0 flex-col gap-2 text-xs font-semibold uppercase tracking-wide text-muted sm:text-right">
                        <span class="inline-flex items-center gap-2 sm:justify-end">
                            <x-icon name="clock" class="h-3.5 w-3.5 text-gold" />
                            {{ $event['date']->format('F j, Y') }}
                        </span>
                        <span class="inline-flex items-center gap-2 sm:justify-end">
                            <x-icon name="map-pin" class="h-3.5 w-3.5 text-gold" />
                            {{ $event['location'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layout>
