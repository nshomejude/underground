@php
    $statusTones = [
        'submitted' => 'info',
        'under_review' => 'warning',
        'approved' => 'success',
        'declined' => 'danger',
    ];
@endphp

<x-admin.shell title="Application {{ $application->reference->value }}" eyebrow="Staff Review" max-width="max-w-3xl">
    <x-slot:actions>
        <a href="{{ route('admin.applications.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">
            &larr; Back to Queue
        </a>
    </x-slot:actions>

    <div class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface p-6 shadow-adm-xs sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-hairline pb-6">
            <div class="flex flex-col gap-1">
                <span class="font-mono text-xs tracking-wider text-muted">{{ $application->reference->value }}</span>
                <h2 class="text-xl font-semibold text-cream">{{ $application->name }}</h2>
                @if ($application->organisation)
                    <span class="text-sm text-body">{{ $application->organisation }}</span>
                @endif
            </div>
            <x-status-badge
                :label="$application->status()->label()"
                :tone="$statusTones[$application->status()->value] ?? 'neutral'"
            />
        </div>

        <dl class="grid grid-cols-1 gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Tier</dt>
                <dd class="text-body">{{ $application->tier->value }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Email</dt>
                <dd class="text-body">{{ $application->email->value }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Phone</dt>
                <dd class="text-body">{{ $application->phone ?? '—' }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Country</dt>
                <dd class="text-body">{{ $application->country ?? '—' }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Submitted</dt>
                <dd class="text-body">{{ $application->submittedAt->format('j M Y, g:ia') }}</dd>
            </div>
            @if ($application->memberId())
                <div class="flex flex-col gap-1">
                    <dt class="text-xs uppercase tracking-wide text-muted">Member ID</dt>
                    <dd class="font-mono text-gold-bright">{{ $application->memberId() }}</dd>
                </div>
            @endif
        </dl>

        <div class="flex flex-col gap-2 border-t border-hairline pt-6">
            <span class="text-xs uppercase tracking-wide text-muted">Statement</span>
            <p class="whitespace-pre-line text-sm leading-relaxed text-body">{{ $application->statement }}</p>
        </div>

        @unless ($application->status()->isTerminal())
            <div class="flex flex-wrap gap-3 border-t border-hairline pt-6">
                <form method="POST" action="{{ route('admin.applications.approve', $application->reference->value) }}">
                    @csrf
                    <x-button type="submit" variant="primary">
                        <x-icon name="check-circle" class="h-3.5 w-3.5" />
                        Approve
                    </x-button>
                </form>

                <form method="POST" action="{{ route('admin.applications.decline', $application->reference->value) }}">
                    @csrf
                    <x-button type="submit" variant="secondary" class="!border-danger !text-danger hover:!bg-danger hover:!text-cream">
                        <x-icon name="x" class="h-3.5 w-3.5" />
                        Decline
                    </x-button>
                </form>
            </div>
        @endunless
    </div>
</x-admin.shell>
