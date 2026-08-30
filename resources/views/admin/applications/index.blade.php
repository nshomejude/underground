@php
    $statusTones = [
        'submitted' => 'info',
        'under_review' => 'warning',
        'approved' => 'success',
        'declined' => 'danger',
    ];
@endphp

<x-layout title="Membership Applications">
    <section class="mx-auto flex max-w-5xl flex-col gap-8 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Staff Review">Membership Applications</x-section-heading>

        @if (session('status'))
            <div class="flex items-center gap-3 border border-success/40 bg-success/10 px-4 py-3 text-sm text-success">
                <x-icon name="check-circle" class="h-5 w-5 shrink-0" />
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 border border-danger/40 bg-danger/10 px-4 py-3 text-sm text-danger">
                <x-icon name="flag" class="h-5 w-5 shrink-0" />
                {{ session('error') }}
            </div>
        @endif

        @if (empty($applications))
            <p class="border border-border bg-surface px-6 py-8 text-sm text-muted">
                No membership applications have been submitted yet.
            </p>
        @else
            <div class="flex flex-col gap-4">
                @foreach ($applications as $application)
                    <article class="flex flex-col gap-4 border border-border bg-surface px-5 py-5 sm:px-6 sm:py-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <span class="font-mono text-xs tracking-wider text-muted">{{ $application->reference->value }}</span>
                                <h3 class="font-serif text-xl font-semibold text-cream">{{ $application->name }}</h3>
                                @if ($application->organisation)
                                    <span class="text-sm text-body">{{ $application->organisation }}</span>
                                @endif
                            </div>

                            <x-status-badge
                                :label="$application->status()->label()"
                                :tone="$statusTones[$application->status()->value] ?? 'neutral'"
                            />
                        </div>

                        <dl class="grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="shrink-0 text-muted">Tier</dt>
                                <dd class="min-w-0 break-words text-right text-body sm:text-left">{{ $application->tier->value }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="shrink-0 text-muted">Email</dt>
                                <dd class="min-w-0 break-words text-right text-body sm:text-left">{{ $application->email->value }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="shrink-0 text-muted">Submitted</dt>
                                <dd class="min-w-0 break-words text-right text-body sm:text-left">{{ $application->submittedAt->format('j M Y') }}</dd>
                            </div>
                            @if ($application->memberId())
                                <div class="flex justify-between gap-4 sm:justify-start">
                                    <dt class="shrink-0 text-muted">Member ID</dt>
                                    <dd class="min-w-0 break-words text-right font-mono text-gold-bright sm:text-left">{{ $application->memberId() }}</dd>
                                </div>
                            @endif
                        </dl>

                        @unless ($application->status()->isTerminal())
                            <div class="flex flex-wrap gap-3 border-t border-border pt-4">
                                <form method="POST" action="{{ route('admin.applications.approve', $application->reference->value) }}">
                                    @csrf
                                    <x-button type="submit" variant="primary" class="!px-4 !py-2 !text-[11px]">
                                        <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                        Approve
                                    </x-button>
                                </form>

                                <form method="POST" action="{{ route('admin.applications.decline', $application->reference->value) }}">
                                    @csrf
                                    <x-button type="submit" variant="secondary" class="!px-4 !py-2 !text-[11px] !border-danger !text-danger hover:!bg-danger hover:!text-cream">
                                        <x-icon name="x" class="h-3.5 w-3.5" />
                                        Decline
                                    </x-button>
                                </form>
                            </div>
                        @endunless
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layout>
