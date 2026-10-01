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
                        <span class="flex h-14 w-14 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $sector->motif }}" class="h-7 w-7" />
                        </span>
                        <p class="text-xs font-semibold uppercase leading-snug tracking-wide text-cream group-hover:text-gold">
                            @foreach ($sector->nameLines() as $line)
                                {{ $line }}@if (!$loop->last)<br>@endif
                            @endforeach
                        </p>
                    </a>
                @endforeach
            </div>

            <div class="mt-12 grid grid-cols-1 gap-px bg-border md:grid-cols-2">
                @foreach ($sectors as $sector)
                    <a href="{{ route('sectors.show', $sector->slug->value) }}" class="group flex items-start gap-4 bg-surface p-6 transition-colors hover:bg-surface-raised">
                        <x-icon name="{{ $sector->motif }}" class="mt-1 h-5 w-5 shrink-0 text-gold" />
                        <span class="flex flex-1 flex-col gap-1">
                            <span class="font-serif text-lg font-semibold text-cream group-hover:text-gold">{{ $sector->name }}</span>
                            <span class="text-sm leading-relaxed text-body">{{ $sector->summary }}</span>
                        </span>
                        <x-icon name="chevron-right" class="mt-1 h-4 w-4 shrink-0 text-gold" />
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="Our Edge"
        heading="What Sets Our Sector Work Apart"
        :items="[
            ['icon' => 'landmark', 'title' => 'Institutional fluency', 'text' => 'We understand how ministries, regulators, boards and investment committees actually make decisions.'],
            ['icon' => 'handshake', 'title' => 'Cross-sector connections', 'text' => 'A project in energy often needs government, finance and infrastructure at the same table. We bring them together.'],
            ['icon' => 'radar', 'title' => 'Context before action', 'text' => 'We read the political, regulatory and security environment first, so that engagement is timely and well judged.'],
        ]"
        :columns="3"
    />

    <x-cta-band tone="ink" heading="Operating in more than one sector?" text="Many of our engagements cross sectors. Tell us what you are working on and we will assemble the right team.">
        <x-button variant="secondary" href="{{ route('capabilities.index') }}">
            Our Capabilities
            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
        </x-button>
    </x-cta-band>
</x-layout>
