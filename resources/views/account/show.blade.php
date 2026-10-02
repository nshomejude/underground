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

        @if (session('status'))
            <p class="flex items-center gap-2 border border-success/40 bg-success/10 px-4 py-3 text-sm text-success" role="status">
                <x-icon name="check-circle" class="h-4 w-4 shrink-0" />
                {{ session('status') === 'verification-link-sent' ? 'A new verification link has been sent to '.auth()->user()->email.'.' : session('status') }}
            </p>
        @endif

        @unless (auth()->user()->hasVerifiedEmail())
            <div class="flex flex-col gap-4 border border-warning/40 bg-warning/10 px-6 py-5 sm:flex-row sm:items-center sm:justify-between" role="alert">
                <div class="flex items-start gap-3">
                    <x-icon name="mail" class="mt-0.5 h-5 w-5 shrink-0 text-warning" />
                    <p class="text-sm leading-relaxed text-body">
                        <strong class="text-cream">Please verify your email.</strong>
                        We sent a link to {{ auth()->user()->email }}. Check your spam folder if you cannot find it.
                    </p>
                </div>
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-button variant="secondary" type="submit">Resend Link</x-button>
                </form>
            </div>
        @endunless

        @php
            $steps = [
                ['done' => true, 'label' => 'Create your account', 'href' => null],
                ['done' => auth()->user()->hasVerifiedEmail(), 'label' => 'Verify your email address', 'href' => route('verification.notice')],
                ['done' => $state !== 'none', 'label' => 'Apply for a membership tier', 'href' => route('membership.index')],
                ['done' => $state === 'approved', 'label' => 'Receive your membership card', 'href' => $state === 'pending' ? route('membership.track') : null],
            ];
            $remaining = collect($steps)->where('done', false)->count();
        @endphp

        @if ($remaining > 0)
            <div class="flex flex-col gap-5 border border-border bg-surface px-6 py-6 sm:px-8">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="font-serif text-xl font-semibold text-cream">Getting started</h3>
                    <span class="text-xs font-semibold uppercase tracking-widest text-muted">{{ count($steps) - $remaining }} of {{ count($steps) }} complete</span>
                </div>
                <ol class="flex flex-col divide-y divide-border">
                    @foreach ($steps as $step)
                        <li class="flex items-center gap-4 py-3">
                            @if ($step['done'])
                                <x-icon name="check-circle" class="h-5 w-5 shrink-0 text-success" />
                                <span class="text-sm text-muted line-through">{{ $step['label'] }}</span>
                            @else
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-gold text-[10px] font-semibold text-gold">{{ $loop->iteration }}</span>
                                @if ($step['href'])
                                    <a href="{{ $step['href'] }}" class="text-sm font-semibold text-cream hover:text-gold">{{ $step['label'] }}</a>
                                @else
                                    <span class="text-sm text-cream">{{ $step['label'] }}</span>
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif

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

        <div class="grid grid-cols-1 gap-px bg-border sm:grid-cols-3">
            <a href="{{ route('inquiries.create') }}" class="group flex flex-col gap-2 bg-surface p-5 transition-colors hover:bg-surface-raised">
                <x-icon name="lock" class="h-5 w-5 text-gold" />
                <span class="text-sm font-semibold text-cream group-hover:text-gold">Confidential inquiry</span>
                <span class="text-xs leading-relaxed text-muted">Start a conversation about a mandate.</span>
            </a>
            <a href="{{ route('insights.index') }}" class="group flex flex-col gap-2 bg-surface p-5 transition-colors hover:bg-surface-raised">
                <x-icon name="newspaper" class="h-5 w-5 text-gold" />
                <span class="text-sm font-semibold text-cream group-hover:text-gold">Read our insights</span>
                <span class="text-xs leading-relaxed text-muted">Analysis from the practice.</span>
            </a>
            <a href="{{ route('contact') }}" class="group flex flex-col gap-2 bg-surface p-5 transition-colors hover:bg-surface-raised">
                <x-icon name="mail" class="h-5 w-5 text-gold" />
                <span class="text-sm font-semibold text-cream group-hover:text-gold">Contact us</span>
                <span class="text-xs leading-relaxed text-muted">Offices and departmental mailboxes.</span>
            </a>
        </div>
    </section>
</x-layout>
