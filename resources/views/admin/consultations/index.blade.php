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

<x-admin.shell title="Consultation Requests" eyebrow="Staff Review" description="Qualify free consultation requests before any paid consultation is offered.">
        @if (empty($consultations))
            <div class="flex flex-col items-center gap-3 rounded-adm border border-dashed border-hairline px-6 py-16 text-center">
                <x-icon name="scan-line" class="h-6 w-6 text-muted" />
                <p class="text-sm text-muted">No consultation requests have been submitted yet.</p>
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($consultations as $consultation)
                    <article class="flex flex-col gap-4 rounded-adm border border-hairline bg-surface px-5 py-5 shadow-adm-xs sm:px-6 sm:py-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <span class="font-mono text-xs tracking-wider text-muted">{{ $consultation->reference->value }}</span>
                                <a href="{{ route('admin.consultations.show', $consultation->reference->value) }}" class="text-base font-semibold text-cream hover:text-gold">{{ $consultation->name }}</a>
                                @if ($consultation->organisation)
                                    <span class="text-sm text-body">{{ $consultation->organisation }}</span>
                                @endif
                            </div>

                            <x-status-badge
                                :label="$consultation->status()->label()"
                                :tone="$statusTones[$consultation->status()->value] ?? 'neutral'"
                            />
                        </div>

                        <dl class="grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="shrink-0 text-muted">Sector</dt>
                                <dd class="min-w-0 break-words text-right text-body sm:text-left">{{ $consultation->sector->label() }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="shrink-0 text-muted">Maturity</dt>
                                <dd class="min-w-0 break-words text-right text-body sm:text-left">{{ $consultation->maturityStage->label() }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="shrink-0 text-muted">Email</dt>
                                <dd class="min-w-0 break-words text-right text-body sm:text-left">{{ $consultation->email->value }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="shrink-0 text-muted">Submitted</dt>
                                <dd class="min-w-0 break-words text-right text-body sm:text-left">{{ $consultation->submittedAt->format('j M Y') }}</dd>
                            </div>
                        </dl>

                        @if (! empty($consultation->status()->allowedTransitions()))
                            <div class="flex flex-wrap gap-3 border-t border-hairline pt-4">
                                @foreach ($consultation->status()->allowedTransitions() as $target)
                                    <form method="POST" action="{{ route('admin.consultations.transition', $consultation->reference->value) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $target->value }}">
                                        <x-button
                                            type="submit"
                                            variant="{{ $target->value === 'declined' ? 'secondary' : 'primary' }}"
                                            class="!px-4 !py-2 !text-[11px] {{ $target->value === 'declined' ? '!border-danger !text-danger hover:!bg-danger hover:!text-cream' : '' }}"
                                        >
                                            {{ $transitionLabels[$target->value] ?? $target->label() }}
                                        </x-button>
                                    </form>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
</x-admin.shell>
