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
            <p class="text-sm text-body">Curious what membership itself looks like once granted? You can also <a href="{{ route('plans.public') }}" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">compare the three plans side by side</a>.</p>
            <x-button variant="secondary" href="{{ route('membership.cards') }}" class="w-fit">
                View Sample Membership Cards
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
            </x-button>
        </div>

        <div class="flex flex-col gap-4 border-t border-border pt-8 sm:flex-row sm:items-center sm:gap-6">
            <p class="text-sm text-body">More on what's included:</p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('membership.plus') }}" class="inline-flex items-center gap-2 text-sm text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">
                    Underground Plus <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </a>
                <a href="{{ route('membership.events') }}" class="inline-flex items-center gap-2 text-sm text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">
                    Business Events <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </a>
                <a href="#professional-expertise" class="inline-flex items-center gap-2 text-sm text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">
                    Professional Expertise <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </a>
                <a href="#registered-agent" class="inline-flex items-center gap-2 text-sm text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">
                    Registered Agent <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </a>
            </div>
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

    <section id="professional-expertise" class="scroll-mt-24 border-t border-border bg-ink">
        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:gap-12 lg:px-8 lg:py-20">
            <div class="flex flex-col gap-6">
                <span class="flex h-11 w-11 items-center justify-center border border-gold/40 text-gold">
                    <x-icon name="briefcase" class="h-5 w-5" />
                </span>
                <div class="flex flex-col gap-3">
                    <h2 id="professional-expertise-heading" class="font-serif text-2xl font-semibold text-cream">Professional Expertise</h2>
                    <p class="text-sm leading-relaxed text-body">
                        Membership orients you toward identified professionals in Underground's network
                        &mdash; lawyers, tax advisors, and notaries &mdash; for matters that fall outside what
                        the firm itself does. This is an orientation and introduction, not a referral
                        guarantee or a service Underground performs directly.
                    </p>
                    <p class="text-sm leading-relaxed text-body">
                        The professional's mission (scope of work) and their fees are agreed directly
                        between you and that professional, and are separate from your membership fee
                        unless a specific engagement expressly includes them.
                    </p>
                </div>
            </div>

            <div id="registered-agent" class="scroll-mt-24 flex flex-col gap-6">
                <span class="flex h-11 w-11 items-center justify-center border border-gold/40 text-gold">
                    <x-icon name="landmark" class="h-5 w-5" />
                </span>
                <div class="flex flex-col gap-3">
                    <h2 id="registered-agent-heading" class="font-serif text-2xl font-semibold text-cream">Registered Agent</h2>
                    <p class="text-sm leading-relaxed text-body">
                        Where available, membership can connect you with a registered agent service
                        through a licensed third-party provider. Availability depends on your
                        jurisdiction and on which licensed provider covers it &mdash; it is not offered
                        everywhere.
                    </p>
                    <p class="text-sm leading-relaxed text-body">
                        Subscribing to a registered agent service does <strong class="text-cream">not</strong>,
                        by itself, constitute company domiciliation. Domiciliation, where you need it, is a
                        separate jurisdiction-specific step arranged directly with the relevant licensed
                        provider or counsel.
                    </p>
                </div>
            </div>
        </div>
    </section>

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
