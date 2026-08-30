<x-layout title="Contact">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Reach Us">
            Contact
        </x-section-heading>

        <div class="flex flex-col gap-6 border border-gold/40 bg-surface px-6 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-10 sm:py-10">
            <div class="flex flex-col gap-2">
                <h2 class="font-serif text-2xl font-semibold text-cream">Have a Mandate for Us?</h2>
                <p class="max-w-xl text-sm leading-relaxed text-body">
                    New engagements are handled through a single, confidential channel &mdash; not this
                    page. A partner reviews every inquiry personally.
                </p>
            </div>

            <x-button variant="primary" href="{{ route('inquiries.create') }}" class="w-fit shrink-0">
                Start a Confidential Conversation
                <x-icon name="lock" class="h-3.5 w-3.5" />
            </x-button>
        </div>

        <p class="text-xs leading-relaxed text-muted">
            Already submitted an inquiry or a membership application? Track its status at
            <a href="{{ route('inquiries.track') }}" class="text-gold underline hover:text-gold-bright">{{ route('inquiries.track') }}</a>
            or
            <a href="{{ route('membership.track') }}" class="text-gold underline hover:text-gold-bright">{{ route('membership.track') }}</a>
            &mdash; no account required.
        </p>

        <div class="flex flex-col gap-4 border-t border-border pt-10 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center border border-gold text-gold">
                    <x-icon name="mail" class="h-5 w-5" />
                </span>
                <div class="flex flex-col">
                    <p class="text-xs font-semibold uppercase tracking-widest text-muted">General Correspondence</p>
                    <a href="mailto:{{ $generalEmail }}" class="text-sm font-semibold text-cream hover:text-gold">
                        {{ $generalEmail }}
                    </a>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Our Offices</h2>

            <div class="mt-6 grid grid-cols-1 gap-px bg-border sm:grid-cols-2">
                @foreach ($offices as $office)
                    <div class="flex flex-col gap-3 bg-surface p-6">
                        <div class="flex items-center gap-3">
                            <x-icon name="map-pin" class="h-4 w-4 shrink-0 text-gold" />
                            <h3 class="font-serif text-lg font-semibold text-cream">
                                {{ $office['city'] }}, {{ $office['region'] }}
                            </h3>
                        </div>
                        <p class="text-sm leading-relaxed text-body">{{ $office['address'] }}</p>
                        <p class="text-xs uppercase tracking-wide text-muted">{{ $office['note'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layout>
