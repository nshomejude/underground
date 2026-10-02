<x-layout title="Partners">
    <div class="relative isolate overflow-hidden">
        <x-network-grid />
        <div class="hero-veil pointer-events-none absolute inset-0" aria-hidden="true"></div>
    <section class="relative z-10 mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Who We Work Alongside">
            Partners
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            No mandate is run alone. We work alongside a vetted circle of firms and institutions
            &mdash; identified here by category, never by name, consistent with the same discretion
            we extend to our clients.
        </p>

        <div class="grid grid-cols-1 tile-grid sm:grid-cols-2">
            @foreach ($categories as $category)
                <div class="flex flex-col items-start gap-4 bg-surface p-8">
                    <span class="flex h-12 w-12 items-center justify-center border border-gold text-gold">
                        <x-icon name="{{ $category['icon'] }}" class="h-6 w-6" />
                    </span>
                    <h3 class="font-serif text-lg font-semibold leading-snug text-cream">
                        {{ $category['title'] }}
                    </h3>
                    <p class="text-sm leading-relaxed text-body">{{ $category['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
    </div>

    <x-value-grid
        tone="surface"
        eyebrow="How Partnership Works"
        heading="A Vetted Circle, Engaged With Care"
        :items="[
            ['icon' => 'scan-line', 'title' => 'Vetted before introduced', 'text' => 'Every firm or institution is assessed on reputation, discretion and conduct before it joins a mandate.'],
            ['icon' => 'lock', 'title' => 'Bound by confidentiality', 'text' => 'Partners sign the same confidentiality terms we do, and see only what they need for their part of the work.'],
            ['icon' => 'handshake', 'title' => 'Aligned on outcomes', 'text' => 'We bring partners in only where they add something we cannot supply ourselves, and only with the client’s agreement.'],
        ]"
    />

    <x-faq
        tone="ink"
        eyebrow="Working Together"
        heading="Partner Questions"
        :items="[
            ['q' => 'Who can become a partner?', 'a' => 'Advisory firms, financial institutions, multilateral bodies and technology providers with a strong reputation for discretion and a clear complement to our work.'],
            ['q' => 'Why are partners not named?', 'a' => 'Naming a partner would often reveal the existence or nature of a mandate. We identify partners by category so that no client or relationship is exposed.'],
            ['q' => 'How do we start a conversation?', 'a' => 'Write to partnerships@un-der.com with a short description of your organization and how you see us working together. A partner will reply personally.'],
        ]"
    />

    <x-cta-band heading="Propose a partnership." text="If your organization complements our work, we would like to hear from you." primary-label="Email Our Partnerships Team" primary-href="mailto:partnerships@un-der.com" />
</x-layout>
