<x-layout title="Collaboration">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="How We Work Together">
            Collaboration
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            A mandate does not end at the proposal. Here is how we actually work once we are in the
            room &mdash; day to day, not just at the milestones.
        </p>

        <div class="grid grid-cols-1 tile-grid sm:grid-cols-2">
            @foreach ($modes as $mode)
                <div class="flex flex-col items-start gap-4 bg-surface p-8">
                    <span class="flex h-12 w-12 items-center justify-center border border-gold text-gold">
                        <x-icon name="{{ $mode['icon'] }}" class="h-6 w-6" />
                    </span>
                    <h3 class="font-serif text-lg font-semibold leading-snug text-cream">
                        {{ $mode['title'] }}
                    </h3>
                    <p class="text-sm leading-relaxed text-body">{{ $mode['body'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-6 border-t border-border pt-10">
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">What Stays Constant</h2>

            <ul class="flex flex-col gap-4">
                @foreach ($principles as $principle)
                    <li class="flex items-start gap-3">
                        <x-icon name="check-circle" class="mt-0.5 h-4 w-4 shrink-0 text-gold" />
                        <span class="text-sm leading-relaxed text-body">{{ $principle }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="The Lifecycle"
        heading="How a Collaboration Unfolds"
        :items="[
            ['icon' => 'handshake', 'title' => '1. Onboard', 'text' => 'We agree scope, governance and confidentiality terms, and establish the secure channels the mandate will run on.'],
            ['icon' => 'users', 'title' => '2. Mobilize', 'text' => 'The named team is assembled, briefed and introduced to your principals and any other parties involved.'],
            ['icon' => 'radar', 'title' => '3. Operate', 'text' => 'Weekly briefings, same-day escalation and milestone reviews keep everyone aligned and decisions moving.'],
            ['icon' => 'check-circle', 'title' => '4. Close', 'text' => 'We deliver a closing review, hand over relationships and materials, and wind down every working channel.'],
        ]"
        :columns="4"
    />

    <x-faq
        tone="ink"
        eyebrow="Working With Us"
        heading="Collaboration Questions"
        :items="[
            ['q' => 'Do we need to share sensitive information?', 'a' => 'Only what the mandate requires, and only through the secure channels we set up. Our team is bound by confidentiality, and nothing is shared beyond the working group without your consent.'],
            ['q' => 'Who makes the decisions?', 'a' => 'You do. We advise and recommend, and nothing material moves without the principal’s sign-off.'],
            ['q' => 'Can you work alongside our existing advisors?', 'a' => 'Yes. We regularly work with a client’s legal, financial and communications advisors, and are comfortable fitting into an established team.'],
            ['q' => 'What happens when the mandate ends?', 'a' => 'Every working group and channel is closed and archived. Relationships we have built on your behalf are handed over to you.'],
        ]"
    />

    <x-cta-band heading="Ready to work together?" text="Tell us about the outcome you are aiming for and we will propose how we would collaborate." />
</x-layout>
