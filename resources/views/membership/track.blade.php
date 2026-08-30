@php
    $statusTones = [
        'submitted' => 'info',
        'under_review' => 'warning',
        'approved' => 'success',
        'declined' => 'danger',
    ];
@endphp

<x-layout title="Track Your Application">
    <section class="mx-auto flex max-w-3xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Membership Application">
            Track Your Application
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Enter the reference you were given when you applied for membership to see its
            current status. No account or login is required.
        </p>

        <form method="GET" action="{{ route('membership.track') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex flex-1 flex-col gap-2">
                <label for="reference" class="text-xs font-semibold uppercase tracking-widest text-body">
                    Reference
                </label>
                <input
                    type="text"
                    id="reference"
                    name="reference"
                    value="{{ $reference }}"
                    placeholder="UGM-2026-XXXXXX"
                    autocapitalize="characters"
                    autocomplete="off"
                    class="border border-border bg-surface px-4 py-3 font-mono text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
            </div>

            <x-button variant="primary" type="submit" class="w-fit">
                Check Status
                <x-icon name="scan-line" class="h-3.5 w-3.5" />
            </x-button>
        </form>

        @if ($searched)
            @if ($application)
                <div class="flex flex-col gap-6 border border-gold/40 bg-surface px-6 py-8 sm:px-10 sm:py-10" role="status">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <p class="inline-flex w-fit items-center gap-2 border border-border bg-ink px-4 py-2 font-mono text-sm tracking-wider text-gold-bright">
                            {{ $application->reference->value }}
                        </p>

                        <x-status-badge
                            :label="$application->status()->label()"
                            :tone="$statusTones[$application->status()->value] ?? 'neutral'"
                        />
                    </div>

                    <dl class="grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                        <div class="flex flex-col gap-1">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-muted">Tier</dt>
                            <dd class="text-body">{{ $tier?->name ?? $application->tier->value }}</dd>
                        </div>
                        <div class="flex flex-col gap-1">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-muted">Submitted</dt>
                            <dd class="text-body">{{ $application->submittedAt->format('j M Y') }}</dd>
                        </div>
                    </dl>

                    @if ($isApproved)
                        <div class="flex flex-col gap-4 border-t border-border pt-6">
                            <p class="text-sm leading-relaxed text-body">
                                Approved &mdash; register or log in to view your membership card.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <x-button variant="primary" href="{{ route('register') }}" class="w-fit">
                                    Register
                                </x-button>
                                <x-button variant="secondary" href="{{ route('login') }}" class="w-fit">
                                    Log In
                                </x-button>
                            </div>
                        </div>
                    @else
                        <p class="text-xs leading-relaxed text-muted">
                            For privacy, only status is shown here. A partner will contact you directly at
                            the email address you provided with any updates.
                        </p>
                    @endif
                </div>
            @else
                <div class="flex items-center gap-3 border border-danger/40 bg-danger/10 px-4 py-3 text-sm text-danger" role="alert">
                    <x-icon name="flag" class="h-5 w-5 shrink-0" />
                    We couldn&rsquo;t find an application with that reference. Double-check it and try again.
                </div>
            @endif
        @endif
    </section>
</x-layout>
