<x-layout title="Sectors">
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Where We Operate">
            Sectors We Serve
        </x-section-heading>

        <p class="mt-6 max-w-2xl text-base leading-relaxed text-body">
            Six verticals where discretion, relationships, and strategic patience
            compound into outcomes.
        </p>

        @if ($sectors === [])
            <p class="mt-12 text-sm text-muted">No sectors have been published yet.</p>
        @else
            <div class="mt-12 grid grid-cols-2 gap-px bg-border sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($sectors as $sector)
                    <a
                        href="{{ route('sectors.show', $sector->slug->value) }}"
                        class="group flex aspect-square flex-col justify-between bg-gradient-to-b from-surface to-ink p-4 transition-colors hover:from-surface-raised"
                    >
                        <span class="flex h-9 w-9 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $sector->motif }}" class="h-4 w-4" />
                        </span>
                        <p class="text-xs font-semibold uppercase leading-snug tracking-wide text-cream group-hover:text-gold">
                            @foreach ($sector->nameLines() as $line)
                                {{ $line }}@if (!$loop->last)<br>@endif
                            @endforeach
                        </p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</x-layout>
