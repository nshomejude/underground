@php
    $statusTones = [
        'submitted' => 'info',
        'under_review' => 'warning',
    ];
@endphp

<x-layout title="My Account">
    <section class="mx-auto flex max-w-3xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <x-section-heading eyebrow="Member Account">
                @if ($state === 'approved')
                    Your Membership Card
                @elseif ($state === 'pending')
                    Application Under Review
                @else
                    My Account
                @endif
            </x-section-heading>

            <a href="{{ route('account.settings') }}" class="inline-flex items-center gap-2 border border-border px-4 py-2 text-xs font-semibold uppercase tracking-widest text-body hover:border-gold hover:text-gold">
                <x-icon name="lock" class="h-3.5 w-3.5" />
                Settings
            </a>
        </div>

        @if ($state === 'approved')
            <p class="max-w-2xl text-base leading-relaxed text-body">
                This is your permanent Underground membership card &mdash; carried, never advertised.
                Select "View Back" to see its verification face.
            </p>

            <x-membership-card
                :variant="$variant"
                :name="$name"
                :representative="$representative"
                :representative-title="$representativeTitle"
                :tier="$tier"
                :member-id="$memberId"
                :issued-on="$issuedOn"
                :valid-through="$validThrough"
            />

            <p class="max-w-2xl text-xs leading-relaxed text-muted">
                Application reference {{ $application->reference->value }} &middot; approved
                {{ $issuedOn->format('j F Y') }}.
            </p>
        @elseif ($state === 'pending')
            <div class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
                <div class="flex items-center gap-3">
                    <x-icon name="clock" class="h-6 w-6 shrink-0 text-gold" />
                    <h3 class="font-serif text-2xl font-semibold text-cream">Still With the Review Committee</h3>
                </div>

                <p class="text-sm leading-relaxed text-body">
                    Your application is {{ strtolower($application->status()->label()) }}. Every application is
                    reviewed by a partner before a tier is granted &mdash; keep the reference below for your
                    records, and this page will reflect your membership card the moment it clears review.
                </p>

                <div class="flex flex-wrap items-center gap-3">
                    <x-status-badge :label="$application->status()->label()" :tone="$statusTones[$application->status()->value] ?? 'neutral'" />
                </div>

                <p class="inline-flex w-fit items-center gap-2 border border-border bg-ink px-4 py-2 font-mono text-sm tracking-wider text-gold-bright">
                    {{ $application->reference->value }}
                </p>
            </div>
        @else
            <div class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
                <div class="flex items-center gap-3">
                    <x-icon name="gem" class="h-6 w-6 shrink-0 text-gold" />
                    <h3 class="font-serif text-2xl font-semibold text-cream">You're Not Yet a Member</h3>
                </div>

                <p class="text-sm leading-relaxed text-body">
                    Underground extends three vetted tiers to governments, principals, and corporate
                    institutions. There is no public checkout &mdash; every application is reviewed by a
                    partner before a tier is granted. Once approved, your permanent membership card will
                    appear here.
                </p>

                <x-button variant="primary" href="{{ route('membership.index') }}" class="w-fit">
                    Explore Membership
                    <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </x-button>
            </div>
        @endif
    </section>
</x-layout>
