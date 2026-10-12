<x-layout title="Team">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Leadership">
            The Partners
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            A deliberately small group of principals, each with their own standing before they ever
            joined the firm. Every mandate is led by one of them, by name.
        </p>

        @if (Route::has('team.senior-leadership') || Route::has('team.gallery') || Route::has('team.case-studies'))
            <div class="flex flex-wrap gap-3">
                @if (Route::has('team.senior-leadership'))
                    <a href="{{ route('team.senior-leadership') }}" class="inline-flex items-center gap-2 border border-border px-4 py-2 text-xs font-semibold uppercase tracking-widest text-body transition-colors hover:border-gold hover:text-gold">
                        <x-icon name="shield-check" class="h-3.5 w-3.5" />
                        Senior Leadership
                    </a>
                @endif
                @if (Route::has('team.gallery'))
                    <a href="{{ route('team.gallery') }}" class="inline-flex items-center gap-2 border border-border px-4 py-2 text-xs font-semibold uppercase tracking-widest text-body transition-colors hover:border-gold hover:text-gold">
                        <x-icon name="camera" class="h-3.5 w-3.5" />
                        Gallery
                    </a>
                @endif
                @if (Route::has('team.case-studies'))
                    <a href="{{ route('team.case-studies') }}" class="inline-flex items-center gap-2 border border-border px-4 py-2 text-xs font-semibold uppercase tracking-widest text-body transition-colors hover:border-gold hover:text-gold">
                        <x-icon name="file-text" class="h-3.5 w-3.5" />
                        Case Studies
                    </a>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($leaders as $leader)
                <x-team.leader-card :leader="$leader" :founder-portrait-src="$founderPortraitSrc" />
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
