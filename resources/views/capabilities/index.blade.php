<x-layout title="Capabilities">
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="What We Do">
            Our Capabilities
        </x-section-heading>

        <p class="mt-6 max-w-2xl text-base leading-relaxed text-body">
            Discreet, high-conviction execution across the disciplines that move institutions,
            capital and policy.
        </p>

        @if ($capabilities === [])
            <p class="mt-12 text-sm text-muted">No capabilities have been published yet.</p>
        @else
            <div class="mt-12 grid grid-cols-1 gap-px bg-border sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($capabilities as $capability)
                    <a
                        href="{{ route('capabilities.show', $capability->slug->value) }}"
                        class="group flex flex-col items-start gap-4 bg-surface p-8 transition-colors hover:bg-surface-raised"
                    >
                        <span class="flex h-12 w-12 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $capability->icon }}" class="h-6 w-6" />
                        </span>
                        <h2 class="font-serif text-lg font-semibold leading-snug text-cream group-hover:text-gold">
                            {{ $capability->title }}
                        </h2>
                        <p class="text-sm leading-relaxed text-body">{{ $capability->summary }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</x-layout>
