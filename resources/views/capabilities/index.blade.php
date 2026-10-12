<x-layout title="Capabilities">
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="What We Do">
            Our Capabilities
        </x-section-heading>

        <p class="mt-6 max-w-2xl text-base leading-relaxed text-body">
            Discreet, high-conviction execution across the disciplines that move institutions,
            capital and policy.
        </p>

        @if ($capabilities === [])
            <p class="mt-12 text-sm text-muted">No capabilities have been published yet.</p>
        @else
            <div class="mt-12 grid grid-cols-1 tile-grid sm:grid-cols-2 lg:grid-cols-4">
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

        @if (\Illuminate\Support\Facades\Route::has('government-advisory'))
            <a
                href="{{ route('government-advisory') }}"
                class="group mt-12 flex flex-col gap-4 border border-gold bg-surface p-7 transition-colors hover:bg-surface-raised sm:flex-row sm:items-center sm:justify-between lg:p-8"
            >
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center border border-gold text-gold">
                        <x-icon name="landmark" class="h-6 w-6" />
                    </span>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Featured</span>
                        <h2 class="font-serif text-lg font-semibold leading-snug text-cream group-hover:text-gold">
                            Government &amp; Head-of-State Advisory
                        </h2>
                        <p class="max-w-2xl text-sm leading-relaxed text-body">
                            A dedicated page for heads of state, ministers and cabinet offices: the initial
                            diagnostic, the cooperation roadmap, and how we prepare an engagement from first
                            contact through follow-up reporting.
                        </p>
                    </div>
                </div>
                <span class="inline-flex shrink-0 items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold">
                    Explore
                    <x-icon name="arrow-right" class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-1" />
                </span>
            </a>
        @endif
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="How We Work"
        heading="Principles Behind Every Capability"
        :items="[
            ['icon' => 'lock', 'title' => 'Discretion by default', 'text' => 'Every engagement is confidential. We never publish client names, and we never trade on what we learn.'],
            ['icon' => 'shield-check', 'title' => 'Lawful and principled', 'text' => 'We operate within the law and ethics rules of every jurisdiction, and we decline work that asks us to do otherwise.'],
            ['icon' => 'handshake', 'title' => 'Relationships over transactions', 'text' => 'We invest in trust that lasts beyond a single mandate, because durable influence is built slowly.'],
            ['icon' => 'globe', 'title' => 'Local insight, global network', 'text' => 'Offices and partners across Africa, Europe and North America give us context where decisions are made.'],
            ['icon' => 'users', 'title' => 'Senior attention', 'text' => 'Partners lead the work. You deal with the people who carry the relationships, not an intermediary.'],
            ['icon' => 'target', 'title' => 'Outcomes, not activity', 'text' => 'We measure ourselves by the decision you needed made, not the hours we billed.'],
        ]"
    />

    <x-cta-band heading="Not sure which capability fits?" text="Describe the outcome you need. We will tell you candidly whether we are the right firm, and which practice should lead.">
        <x-button variant="secondary" href="{{ route('engagement-models.index') }}">
            How to Engage Us
            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
        </x-button>
    </x-cta-band>
</x-layout>
