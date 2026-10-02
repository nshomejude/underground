<x-layout title="Contact">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Reach Us">
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
            Already submitted an inquiry or a membership application? Track its status via the
            <a href="{{ route('inquiries.track') }}" class="text-gold underline hover:text-gold-bright">inquiry tracker</a>
            or the
            <a href="{{ route('membership.track') }}" class="text-gold underline hover:text-gold-bright">membership tracker</a>
            &mdash; no account required.
        </p>

        <div class="grid grid-cols-1 gap-6 border-t border-border pt-10 sm:grid-cols-2">
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

            <div class="flex items-center gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center border border-gold text-gold">
                    <x-icon name="phone" class="h-5 w-5" />
                </span>
                <div class="flex flex-col">
                    <p class="text-xs font-semibold uppercase tracking-widest text-muted">Telephone</p>
                    <a href="tel:{{ preg_replace('/[^+\d]/', '', $generalPhone) }}" class="text-sm font-semibold text-cream hover:text-gold">
                        {{ $generalPhone }}
                    </a>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Departments</h2>

            <div class="mt-6 grid grid-cols-1 tile-grid sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($departments as $label => $mailbox)
                    <a href="mailto:{{ $mailbox }}" class="group flex flex-col gap-1 bg-surface p-6 transition-colors hover:bg-surface-raised">
                        <span class="text-xs font-semibold uppercase tracking-widest text-muted">{{ $label }}</span>
                        <span class="text-sm font-semibold text-cream group-hover:text-gold">{{ $mailbox }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Our Offices</h2>

            <div class="mt-6 grid grid-cols-1 tile-grid sm:grid-cols-2">
                @foreach ($offices as $office)
                    <div class="flex flex-col gap-3 bg-surface p-6">
                        <div class="flex items-center gap-3">
                            <x-icon name="map-pin" class="h-4 w-4 shrink-0 text-gold" />
                            <h3 class="font-serif text-lg font-semibold text-cream">
                                {{ $office['city'] }}, {{ $office['region'] }}
                            </h3>
                        </div>
                        @if ($office['address'])
                            <p class="text-sm leading-relaxed text-body">{!! nl2br(e($office['address'])) !!}</p>
                        @endif
                        @if ($office['phone'])
                            <p class="text-sm text-body">
                                <a href="tel:{{ preg_replace('/[^+\d]/', '', $office['phone']) }}" class="hover:text-gold">{{ $office['phone'] }}</a>
                            </p>
                        @endif
                        @if ($office['email'])
                            <p class="text-sm text-body">
                                <a href="mailto:{{ $office['email'] }}" class="hover:text-gold">{{ $office['email'] }}</a>
                            </p>
                        @endif
                        <p class="text-xs uppercase tracking-wide text-muted">{{ $office['note'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="Choosing a Channel"
        heading="Who to Contact"
        :items="[
            ['icon' => 'lock', 'title' => 'A new mandate', 'text' => 'Use the confidential inquiry form. It reaches a partner directly and is the most secure way to describe a matter.'],
            ['icon' => 'mail', 'title' => 'General questions', 'text' => 'Write to info@un-der.com for anything that is not a new engagement, and the right person will reply.'],
            ['icon' => 'handshake', 'title' => 'Partnerships and proposals', 'text' => 'Use the departmental mailboxes above so your message reaches the team that handles it.'],
            ['icon' => 'newspaper', 'title' => 'Media and press', 'text' => 'Contact media@un-der.com for interview, comment and publication requests.'],
        ]"
        :columns="4"
    />

    <x-faq
        tone="ink"
        eyebrow="Before You Write"
        heading="Contact Questions"
        :items="[
            ['q' => 'How quickly will I receive a reply?', 'a' => 'We read every message personally. Time-sensitive matters are prioritized, but we do not promise a fixed response time.'],
            ['q' => 'Is email secure enough for sensitive matters?', 'a' => 'Ordinary email is not. For anything sensitive, please start with the confidential inquiry form and we will agree a secure way to continue.'],
            ['q' => 'Can I visit an office?', 'a' => 'Our offices are not open to walk-in visitors. Please write first and a partner will arrange a meeting where appropriate.'],
            ['q' => 'Do you accept unsolicited proposals?', 'a' => 'Yes. Send a short summary to proposals@un-der.com. We review each one and reply where there is a fit.'],
        ]"
    />
</x-layout>
