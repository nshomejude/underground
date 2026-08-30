<x-layout title="About">
    <section class="border-b border-border bg-ink">
        <div class="mx-auto flex max-w-4xl flex-col gap-6 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <x-section-heading eyebrow="Who We Are">
                Power Beneath the Surface.
            </x-section-heading>

            <p class="max-w-2xl text-base leading-relaxed text-body">
                Underground is a global strategic advisory and influence firm operating at the
                intersection of politics, government, business, capital, and media. We move in the
                shadows so our clients can lead in the light.
            </p>
        </div>
    </section>

    <section class="border-b border-border bg-surface">
        <div class="mx-auto flex max-w-4xl flex-col gap-6 px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <p class="text-base leading-relaxed text-body">
                We were founded on a simple premise: the outcomes that matter most &mdash; a contested
                election, a stalled concession, a capital raise that hinges on a single relationship
                &mdash; are rarely won in public. They are won in the rooms before the announcement, by
                people who understand every party at the table and are trusted by all of them. That is
                the work we built the firm to do.
            </p>

            <p class="text-base leading-relaxed text-body">
                Two decades on, that premise has not changed, only the scale of what it is applied to.
                Our partners have sat inside ministries, sovereign funds, multinational boards, and
                multilateral institutions. What they carry with them is not a network of contacts but a
                working understanding of how each of those institutions actually decides &mdash; and
                the discipline to keep what they learn in one room out of every other.
            </p>

            <p class="text-base leading-relaxed text-body">
                We remain deliberately small. Every mandate is led by a named partner, staffed by people
                who have already spent years in the market it concerns, and closed out completely once
                the outcome is delivered. Growth, for us, has always meant taking on fewer engagements
                more seriously, not more of them at a distance.
            </p>
        </div>
    </section>

    <section class="bg-ink">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <x-section-heading eyebrow="How We Work" align="center" class="mx-auto max-w-2xl">
                The Principles Behind Every Mandate
            </x-section-heading>

            <div class="mt-12 grid grid-cols-1 gap-px bg-border sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($principles as $principle)
                    <div class="flex flex-col items-start gap-4 bg-surface p-8">
                        <span class="flex h-12 w-12 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $principle['icon'] }}" class="h-6 w-6" />
                        </span>
                        <h3 class="font-serif text-lg font-semibold leading-snug text-cream">
                            {{ $principle['title'] }}
                        </h3>
                        <p class="text-sm leading-relaxed text-body">{{ $principle['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layout>
