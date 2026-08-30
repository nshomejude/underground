<x-layout title="Careers">
    <section class="border-b border-border bg-ink">
        <div class="mx-auto flex max-w-4xl flex-col gap-6 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <x-section-heading eyebrow="Careers">
                Work That Never Makes the Headlines.
            </x-section-heading>

            <p class="max-w-2xl text-base leading-relaxed text-body">
                We do not run a public job board, and we are not going to start. The mandates we take
                on are confidential by definition, and so is how we build the team that carries them.
                If you are the kind of person we hire, the work will find you &mdash; but it helps to
                make yourself known first.
            </p>
        </div>
    </section>

    <section class="border-b border-border bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <x-section-heading eyebrow="Who We Hire" align="center" class="mx-auto max-w-2xl">
                People Who Have Already Done This Quietly
            </x-section-heading>

            <p class="mx-auto mt-6 max-w-2xl text-center text-base leading-relaxed text-body">
                Almost everyone at the firm arrived with a career already in motion elsewhere &mdash;
                inside a ministry, a sovereign fund, a newsroom, a boardroom. We look for a small set
                of traits more than any particular resume.
            </p>

            <div class="mt-12 grid grid-cols-1 gap-px bg-border sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($traits as $trait)
                    <div class="flex flex-col items-start gap-4 bg-ink p-8">
                        <span class="flex h-12 w-12 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $trait['icon'] }}" class="h-6 w-6" />
                        </span>
                        <h3 class="font-serif text-lg font-semibold leading-snug text-cream">
                            {{ $trait['title'] }}
                        </h3>
                        <p class="text-sm leading-relaxed text-body">{{ $trait['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-b border-border bg-ink">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <x-section-heading eyebrow="Where We Recruit">
                Practice Areas We Quietly Build Into
            </x-section-heading>

            <p class="mt-6 max-w-2xl text-base leading-relaxed text-body">
                We do not post openings against these &mdash; headcount is added only when a mandate
                or a person warrants it. Consider this a map of where the firm grows, not a set of
                postings to apply against.
            </p>

            <div class="mt-12 flex flex-col divide-y divide-border border-y border-border">
                @foreach ($practiceAreas as $area)
                    <div class="flex min-h-[44px] items-center gap-4 py-5">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center border border-gold text-gold">
                            <x-icon name="{{ $area['icon'] }}" class="h-5 w-5" />
                        </span>
                        <span class="flex flex-1 flex-col gap-1">
                            <span class="font-serif text-base font-semibold leading-snug text-cream">{{ $area['title'] }}</span>
                            <span class="text-sm leading-snug text-body">{{ $area['body'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-surface">
        <div class="mx-auto flex max-w-3xl flex-col items-center gap-6 px-4 py-16 text-center sm:px-6 lg:px-8 lg:py-24">
            <x-section-heading eyebrow="Reach Out" align="center">
                We Recruit Quietly, Directly
            </x-section-heading>

            <p class="max-w-2xl text-base leading-relaxed text-body">
                There is no application form to submit into a queue. If you believe you belong among
                the people described above, write to us directly and tell us why &mdash; in confidence,
                to a partner who will actually read it.
            </p>

            <div class="mt-4 flex flex-col items-center gap-4 sm:flex-row">
                <x-button variant="primary" href="mailto:{{ $careersEmail }}">
                    {{ $careersEmail }}
                    <x-icon name="mail" class="h-3.5 w-3.5" />
                </x-button>

                <x-button variant="secondary" href="{{ route('inquiries.create') }}">
                    Submit a Confidential Inquiry
                    <x-icon name="lock" class="h-3.5 w-3.5" />
                </x-button>
            </div>
        </div>
    </section>
</x-layout>
