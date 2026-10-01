<x-layout title="Team">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Leadership">
            The Partners
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            A deliberately small group of principals, each with their own standing before they ever
            joined the firm. Every mandate is led by one of them, by name.
        </p>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($leaders as $leader)
                <div class="flex flex-col items-start gap-5 border border-border bg-surface p-8">
                    @if (!empty($leader['portrait']))
                        <div class="h-20 w-20 overflow-hidden rounded-full border border-gold">
                            <img
                                src="{{ $founderPortraitSrc }}"
                                alt="Portrait of {{ $leader['name'] }}"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                        </div>
                    @else
                        <span class="flex h-20 w-20 items-center justify-center rounded-full border border-gold bg-surface">
                            <x-icon name="{{ $leader['icon'] }}" class="h-8 w-8 text-gold" />
                        </span>
                    @endif

                    <div class="flex flex-col gap-2">
                        <h3 class="font-serif text-xl font-semibold leading-snug text-cream">
                            {{ $leader['name'] }}
                        </h3>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gold">
                            {{ $leader['title'] }}
                        </p>
                        <p class="text-sm leading-relaxed text-body">{{ $leader['background'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="What Partners Commit To"
        heading="The Standard Every Partner Holds"
        :items="[
            ['icon' => 'users', 'title' => 'Personal accountability', 'text' => 'Each mandate is led by a named partner who answers to the client directly, from first conversation to closing review.'],
            ['icon' => 'lock', 'title' => 'Absolute discretion', 'text' => 'What is learned in one room stays out of every other. Confidentiality is a condition of partnership, not a policy.'],
            ['icon' => 'handshake', 'title' => 'Independent judgment', 'text' => 'Partners advise candidly, including when the answer is not what a client hoped to hear.'],
            ['icon' => 'globe', 'title' => 'Earned relationships', 'text' => 'Every partner brings standing of their own, built over years inside the institutions they now advise.'],
            ['icon' => 'scan-line', 'title' => 'Depth over breadth', 'text' => 'We take on fewer mandates, so that each partner can give every one the attention it deserves.'],
            ['icon' => 'shield-check', 'title' => 'Principled conduct', 'text' => 'We act within the law and decline work that would compromise our integrity or that of our clients.'],
        ]"
    />

    <x-cta-band tone="ink" heading="Interested in joining the team?" text="We do not run a public job board. The people who join us tend to be introduced, or to introduce themselves thoughtfully.">
        <x-button variant="secondary" href="{{ route('careers') }}">
            About Careers
            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
        </x-button>
    </x-cta-band>
</x-layout>
