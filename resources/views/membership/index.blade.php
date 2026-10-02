<x-layout title="Membership">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="By Invitation and Application">
            Membership
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Underground extends three vetted tiers to governments, principals, and corporate
            institutions. There is no public checkout &mdash; every application is reviewed by a partner
            before a tier is granted.
        </p>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($tiers as $tier)
                <div class="flex flex-col gap-6 border border-border bg-surface p-8">
                    <span class="flex h-12 w-12 items-center justify-center border border-gold/40 text-gold">
                        <x-icon :name="$tier->icon" class="h-6 w-6" />
                    </span>

                    <div class="flex flex-col gap-2">
                        <h3 class="font-serif text-2xl font-semibold text-cream">{{ $tier->name }}</h3>
                        <p class="text-sm leading-relaxed text-body">{{ $tier->audience }}</p>
                    </div>

                    <x-button variant="secondary" href="{{ route('membership.apply', ['tier' => $tier->slug->value]) }}" class="mt-auto w-fit">
                        Apply
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                    </x-button>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-4 border-t border-border pt-8 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-body">Curious what membership itself looks like once granted?</p>
            <x-button variant="secondary" href="{{ route('membership.cards') }}" class="w-fit">
                View Sample Membership Cards
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
            </x-button>
        </div>
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="Membership Benefits"
        heading="What Membership Offers"
        :items="[
            ['icon' => 'handshake', 'title' => 'Access to the network', 'text' => 'Introductions to a trusted circle of principals, institutions and operators across our regions.'],
            ['icon' => 'radar', 'title' => 'Briefings and insight', 'text' => 'Regular strategic briefings and early access to our research and analysis.'],
            ['icon' => 'users', 'title' => 'Closed forums', 'text' => 'Priority consideration for invitation-only roundtables and dialogues.'],
            ['icon' => 'lock', 'title' => 'Private channel', 'text' => 'A confidential line to the firm for questions that cannot wait for a formal engagement.'],
        ]"
        :columns="4"
    />

    <x-faq
        tone="ink"
        eyebrow="Applying"
        heading="Membership Questions"
        :items="[
            ['q' => 'How does the application process work?', 'a' => 'Submit an application for the tier that fits. A partner reviews every application personally and will respond with a decision. You will receive a reference number to track your application.'],
            ['q' => 'Is there a fee?', 'a' => 'There is no public checkout. Terms are discussed directly with approved applicants.'],
            ['q' => 'How long does review take?', 'a' => 'Reviews are handled personally by partners, so timing varies. Use your reference number on the membership tracker to check progress at any time.'],
            ['q' => 'Can I apply for more than one tier?', 'a' => 'Please apply for the tier that best describes you or your organization. If circumstances change, we can revisit it.'],
        ]"
    />
</x-layout>
