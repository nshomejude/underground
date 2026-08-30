@php
    $statusTones = [
        'received' => 'info',
        'under_review' => 'warning',
        'engaged' => 'success',
        'declined' => 'danger',
        'archived' => 'neutral',
    ];

    $transitionLabels = [
        'under_review' => 'Move to Under Review',
        'engaged' => 'Mark Engaged',
        'declined' => 'Decline',
        'archived' => 'Archive',
    ];
@endphp

<x-admin.shell title="Inquiry {{ $inquiry->reference->value }}" eyebrow="Staff Review" max-width="max-w-3xl">
    <x-slot:actions>
        <a href="{{ route('admin.inquiries.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">
            &larr; Back to Queue
        </a>
    </x-slot:actions>

    <div class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface p-6 shadow-adm-xs sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-hairline pb-6">
            <div class="flex flex-col gap-1">
                <span class="font-mono text-xs tracking-wider text-muted">{{ $inquiry->reference->value }}</span>
                <h2 class="text-xl font-semibold text-cream">{{ $inquiry->name }}</h2>
                @if ($inquiry->organisation)
                    <span class="text-sm text-body">{{ $inquiry->organisation }}</span>
                @endif
            </div>
            <x-status-badge
                :label="$inquiry->status()->label()"
                :tone="$statusTones[$inquiry->status()->value] ?? 'neutral'"
            />
        </div>

        <dl class="grid grid-cols-1 gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Interest</dt>
                <dd class="text-body">{{ $inquiry->interest->label() }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Email</dt>
                <dd class="text-body">{{ $inquiry->email->value }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Phone</dt>
                <dd class="text-body">{{ $inquiry->phone ?? '—' }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Country</dt>
                <dd class="text-body">{{ $inquiry->country ?? '—' }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Submitted</dt>
                <dd class="text-body">{{ $inquiry->submittedAt->format('j M Y, g:ia') }}</dd>
            </div>
            @if ($inquiry->needsPartnerTriage())
                <div class="flex flex-col gap-1">
                    <dt class="text-xs uppercase tracking-wide text-muted">Triage</dt>
                    <dd class="text-warning">Partner triage required</dd>
                </div>
            @endif
        </dl>

        <div class="flex flex-col gap-2 border-t border-hairline pt-6">
            <span class="text-xs uppercase tracking-wide text-muted">Brief</span>
            <p class="whitespace-pre-line text-sm leading-relaxed text-body">{{ $inquiry->brief }}</p>
        </div>

        @if (! empty($inquiry->status()->allowedTransitions()))
            <div class="flex flex-wrap gap-3 border-t border-hairline pt-6">
                @foreach ($inquiry->status()->allowedTransitions() as $target)
                    <form method="POST" action="{{ route('admin.inquiries.transition', $inquiry->reference->value) }}">
                        @csrf
                        <input type="hidden" name="status" value="{{ $target->value }}">
                        <x-button
                            type="submit"
                            variant="{{ $target->value === 'declined' ? 'secondary' : 'primary' }}"
                            class="{{ $target->value === 'declined' ? '!border-danger !text-danger hover:!bg-danger hover:!text-cream' : '' }}"
                        >
                            {{ $transitionLabels[$target->value] ?? $target->label() }}
                        </x-button>
                    </form>
                @endforeach
            </div>
        @endif
    </div>
</x-admin.shell>
