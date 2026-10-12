<x-layout title="Business Events" description="How Underground membership tiers relate to Business Events: free events for every tier, discounts on paid events, and the Sovereign Partner guest policy.">
    <section class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Membership &amp; the Event Calendar">
            Business Events
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Membership affects how you can attend Underground's event program. Free events follow
            the firm's general admission policy at every tier; paid events carry a tier-based
            discount. See the full calendar on <a href="{{ route('events') }}" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Events</a>.
        </p>

        <div class="flex flex-col gap-3 border border-gold/30 bg-surface px-6 py-5 sm:flex-row sm:items-start sm:gap-4">
            <x-icon name="triangle-alert" class="h-5 w-5 shrink-0 text-gold" />
            <p class="text-sm leading-relaxed text-body">
                The figures below are proposed, indicative benefit terms and are not yet
                contractually final. Where a specific paid event has its own published fee, that
                fee is in USD and excludes applicable taxes.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            @foreach ($tiers as $tier)
                <div class="flex flex-col gap-5 border border-border bg-surface p-7">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center border border-gold/40 text-gold">
                            <x-icon name="{{ $tier['icon'] }}" class="h-5 w-5" />
                        </span>
                        <h3 class="font-serif text-xl font-semibold text-cream">{{ $tier['name'] }}</h3>
                    </div>

                    <div class="flex flex-col gap-4 text-sm leading-relaxed text-body">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-semibold uppercase tracking-widest text-muted">Free Underground events</span>
                            <p>Included, per the firm's general admission policy for the event in question.</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-semibold uppercase tracking-widest text-muted">Paid Underground events</span>
                            <p>
                                @if ($tier['benefit']['discount'] ?? null)
                                    A proposed <strong class="text-cream">{{ $tier['benefit']['discount'] }}</strong> flat discount off the published fee.
                                @else
                                    {{ $tier['benefit']['summary'] ?? 'Terms for paid events are confirmed per event.' }}
                                @endif
                            </p>
                        </div>

                        @if ($tier['slug'] === 'sovereign-partner')
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-semibold uppercase tracking-widest text-muted">Guests</span>
                                <p>
                                    Two guests per event under the standard policy. A specific event may be
                                    marked to raise this to five guests for Sovereign Partner members &mdash;
                                    five is the exception, not the default, and only applies where an event
                                    is marked that way.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-3 border border-border bg-ink px-6 py-6 sm:px-8">
            <h3 class="font-serif text-base font-semibold text-cream">Not automatically included</h3>
            <p class="max-w-3xl text-sm leading-relaxed text-body">
                Private client meetings and third-party events (events Underground did not convene
                or does not control) are not automatically included in these tier benefits, even for
                Sovereign Partner members. Admission and discounts for any specific event remain
                subject to that event's own rules and availability.
            </p>
        </div>

        <div class="flex flex-col gap-4 border-t border-border pt-8 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-body">Underground Plus bundles further perks into every tier &mdash; separate from event benefits.</p>
            <x-button variant="secondary" href="{{ route('membership.plus') }}" class="w-fit">
                See Underground Plus
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
            </x-button>
        </div>
    </section>
</x-layout>
