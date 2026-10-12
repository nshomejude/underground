@php
    $statusTones = [
        'submitted' => 'info',
        'under_review' => 'warning',
        'qualified' => 'success',
        'proposal_sent' => 'success',
        'declined' => 'danger',
    ];

    $transitionLabels = [
        'under_review' => 'Move to Under Review',
        'qualified' => 'Mark Qualified',
        'proposal_sent' => 'Mark Proposal Sent',
        'declined' => 'Decline',
    ];
@endphp

<x-admin.shell title="Consultation {{ $consultation->reference->value }}" eyebrow="Staff Review" max-width="max-w-3xl">
    <x-slot:actions>
        <a href="{{ route('admin.consultations.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">
            &larr; Back to Queue
        </a>
    </x-slot:actions>

    <div class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface p-6 shadow-adm-xs sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-hairline pb-6">
            <div class="flex flex-col gap-1">
                <span class="font-mono text-xs tracking-wider text-muted">{{ $consultation->reference->value }}</span>
                <h2 class="text-xl font-semibold text-cream">{{ $consultation->name }}</h2>
                @if ($consultation->organisation)
                    <span class="text-sm text-body">{{ $consultation->organisation }}</span>
                @endif
                @if ($consultation->role)
                    <span class="text-xs text-muted">{{ $consultation->role }}</span>
                @endif
            </div>
            <x-status-badge
                :label="$consultation->status()->label()"
                :tone="$statusTones[$consultation->status()->value] ?? 'neutral'"
            />
        </div>

        <dl class="grid grid-cols-1 gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Email</dt>
                <dd class="text-body">{{ $consultation->email->value }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Country</dt>
                <dd class="text-body">{{ $consultation->country ?? '—' }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Sector</dt>
                <dd class="text-body">{{ $consultation->sector->label() }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Maturity Stage</dt>
                <dd class="text-body">{{ $consultation->maturityStage->label() }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Indicative Budget</dt>
                <dd class="text-body">{{ $consultation->budgetRange->label() }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Supporting Documents</dt>
                <dd class="text-body">{{ $consultation->hasSupportingDocuments ? 'Available on request' : 'None indicated' }}</dd>
            </div>
            <div class="flex flex-col gap-1">
                <dt class="text-xs uppercase tracking-wide text-muted">Submitted</dt>
                <dd class="text-body">{{ $consultation->submittedAt->format('j M Y, g:ia') }}</dd>
            </div>
        </dl>

        <div class="flex flex-col gap-2 border-t border-hairline pt-6">
            <span class="text-xs uppercase tracking-wide text-muted">Project Description</span>
            <p class="whitespace-pre-line text-sm leading-relaxed text-body">{{ $consultation->projectDescription }}</p>
        </div>

        <div class="flex flex-col gap-2 border-t border-hairline pt-6">
            <span class="text-xs uppercase tracking-wide text-muted">Need Description</span>
            <p class="whitespace-pre-line text-sm leading-relaxed text-body">{{ $consultation->needDescription }}</p>
        </div>

        @if (! empty($consultation->status()->allowedTransitions()))
            <div class="flex flex-wrap gap-3 border-t border-hairline pt-6">
                @foreach ($consultation->status()->allowedTransitions() as $target)
                    <form method="POST" action="{{ route('admin.consultations.transition', $consultation->reference->value) }}">
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
