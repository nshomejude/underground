<x-layout title="Underground Plus" description="Underground Plus is the perks and benefits program bundled into every membership tier — not a separate paid tier. See what each category covers and its current status.">
    <section class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Included With Every Tier">
            Underground Plus
        </x-section-heading>

        <div class="flex max-w-2xl flex-col gap-4 text-base leading-relaxed text-body">
            <p>
                Underground Plus is the name for the perks and benefits bundled into every membership
                tier &mdash; Corporate Affiliate, Principal Circle, and Sovereign Partner alike. It is
                <strong class="text-cream">not a fourth, separately priced tier</strong>: there is no
                additional subscription to buy into Plus on its own.
            </p>
            <p>
                The categories below describe what each part of the program is intended to cover.
                Enrollment and activation for most categories depend on confirmed partner agreements
                that are not yet in place. Where a category is not yet active, it is marked
                &ldquo;Program in preparation&rdquo; rather than presented as something you can book or
                claim today.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($categories as $category)
                <div class="flex flex-col gap-4 border border-border bg-surface p-7">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-11 w-11 items-center justify-center border border-gold/40 text-gold">
                            <x-icon name="{{ $category['icon'] }}" class="h-5 w-5" />
                        </span>
                        <x-status-badge label="In preparation" tone="warning" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <h3 class="font-serif text-lg font-semibold leading-snug text-cream">{{ $category['title'] }}</h3>
                        <p class="text-sm leading-relaxed text-body">{{ $category['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-3 border border-border bg-ink px-6 py-6 sm:px-8">
            <div class="flex items-center gap-2">
                <x-icon name="triangle-alert" class="h-4 w-4 shrink-0 text-gold" />
                <h3 class="font-serif text-base font-semibold text-cream">Status of this program</h3>
            </div>
            <p class="max-w-3xl text-sm leading-relaxed text-body">
                Underground Plus is in preparation. Most categories above depend on confirmed
                partnerships &mdash; with insurers, travel and hospitality providers, a banking
                partner for the card program, and venue operators for the club network &mdash; that
                are not yet finalized. Nothing on this page should be read as an active, bookable
                benefit until Underground confirms a category is live for your tier and jurisdiction.
            </p>
        </div>

        <div class="flex flex-col gap-4 border-t border-border pt-8 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-body">Not yet a member? See the three tiers Plus is bundled into.</p>
            <x-button variant="secondary" href="{{ route('membership.index') }}" class="w-fit">
                View Membership Tiers
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
            </x-button>
        </div>
    </section>
</x-layout>
