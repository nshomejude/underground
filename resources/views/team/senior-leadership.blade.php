<x-layout title="Senior Leadership">
    <section class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Governance &amp; Leadership">
            Senior Leadership
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Governance and operational leadership are kept distinct. Executive Officers run the firm
            day to day; the Board and Advisory Board provide oversight and outside counsel rather than
            day-to-day delivery. See the
            <a href="{{ route('team') }}" class="font-semibold text-gold underline-offset-4 hover:underline">full team roster</a>
            for the Regional and Expertise principals who lead individual mandates.
        </p>

        {{-- Pure CSS tabs: three radio inputs drive which panel shows via the
             `peer-checked/<name>:` variant. The inputs, their labels and the
             panels are all kept as direct siblings of one another (no extra
             wrapping element around just the labels) because Tailwind's peer
             variant compiles to a plain CSS sibling selector, which only
             matches true siblings — see the inline comments below before
             changing this structure. No JavaScript. --}}
        <div role="tablist" aria-label="Senior Leadership categories">
            <input type="radio" name="leadership-tab" id="leadership-tab-executive" class="peer/executive sr-only" checked>
            <input type="radio" name="leadership-tab" id="leadership-tab-board" class="peer/board sr-only">
            <input type="radio" name="leadership-tab" id="leadership-tab-advisory" class="peer/advisory sr-only">

            <label
                for="leadership-tab-executive"
                role="tab"
                aria-controls="leadership-panel-executive"
                class="inline-flex cursor-pointer items-center gap-2 border-b-2 border-transparent px-4 py-3 text-xs font-semibold uppercase tracking-widest text-muted transition-colors hover:text-cream peer-checked/executive:border-gold peer-checked/executive:text-gold peer-focus-visible/executive:ring-2 peer-focus-visible/executive:ring-gold/40"
            >
                <x-icon name="shield-check" class="h-4 w-4" />
                Executive Officers
            </label>
            <label
                for="leadership-tab-board"
                role="tab"
                aria-controls="leadership-panel-board"
                class="inline-flex cursor-pointer items-center gap-2 border-b-2 border-transparent px-4 py-3 text-xs font-semibold uppercase tracking-widest text-muted transition-colors hover:text-cream peer-checked/board:border-gold peer-checked/board:text-gold peer-focus-visible/board:ring-2 peer-focus-visible/board:ring-gold/40"
            >
                <x-icon name="users" class="h-4 w-4" />
                Board Members
            </label>
            <label
                for="leadership-tab-advisory"
                role="tab"
                aria-controls="leadership-panel-advisory"
                class="inline-flex cursor-pointer items-center gap-2 border-b-2 border-transparent px-4 py-3 text-xs font-semibold uppercase tracking-widest text-muted transition-colors hover:text-cream peer-checked/advisory:border-gold peer-checked/advisory:text-gold peer-focus-visible/advisory:ring-2 peer-focus-visible/advisory:ring-gold/40"
            >
                <x-icon name="handshake" class="h-4 w-4" />
                Advisory Board
            </label>

            <div class="mb-8 border-b border-border"></div>

            <div id="leadership-panel-executive" role="tabpanel" aria-labelledby="leadership-tab-executive" class="hidden peer-checked/executive:block">
                @if (count($executives) > 0)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($executives as $leader)
                            <x-team.leader-card :leader="$leader" :founder-portrait-src="$founderPortraitSrc" />
                        @endforeach
                    </div>
                @else
                    <x-team.empty-leadership-state
                        icon="shield-check"
                        text="No Executive Officers are published yet."
                    />
                @endif
            </div>

            <div id="leadership-panel-board" role="tabpanel" aria-labelledby="leadership-tab-board" class="hidden peer-checked/board:block">
                @if (count($boardMembers) > 0)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($boardMembers as $leader)
                            <x-team.leader-card :leader="$leader" :founder-portrait-src="$founderPortraitSrc" />
                        @endforeach
                    </div>
                @else
                    <x-team.empty-leadership-state
                        icon="users"
                        text="No Board Members are publicly listed at this time."
                    />
                @endif
            </div>

            <div id="leadership-panel-advisory" role="tabpanel" aria-labelledby="leadership-tab-advisory" class="hidden peer-checked/advisory:block">
                @if (count($advisoryBoard) > 0)
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($advisoryBoard as $leader)
                            <x-team.leader-card :leader="$leader" :founder-portrait-src="$founderPortraitSrc" />
                        @endforeach
                    </div>
                @else
                    <x-team.empty-leadership-state
                        icon="handshake"
                        text="No Advisory Board members are publicly listed at this time."
                    />
                @endif
            </div>
        </div>
    </section>

    <x-cta-band tone="ink" heading="Looking for the full team?" text="Regional leadership and the delivery bench are listed on the main team roster, alongside gallery and case-study records.">
        <x-button variant="secondary" href="{{ route('team') }}">
            View Team Roster
            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
        </x-button>
    </x-cta-band>
</x-layout>
