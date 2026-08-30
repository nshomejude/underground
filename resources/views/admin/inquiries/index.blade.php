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

<x-admin.shell title="Confidential Inquiries" eyebrow="Staff Review" description="Work confidential inquiries through the review pipeline.">
        @if (empty($inquiries))
            <div class="flex flex-col items-center gap-3 rounded-adm border border-dashed border-hairline px-6 py-16 text-center">
                <x-icon name="lock" class="h-6 w-6 text-muted" />
                <p class="text-sm text-muted">No confidential inquiries have been submitted yet.</p>
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($inquiries as $inquiry)
                    <article class="flex flex-col gap-4 rounded-adm border border-hairline bg-surface px-5 py-5 shadow-adm-xs sm:px-6 sm:py-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <span class="font-mono text-xs tracking-wider text-muted">{{ $inquiry->reference->value }}</span>
                                <h3 class="text-base font-semibold text-cream">{{ $inquiry->name }}</h3>
                                @if ($inquiry->organisation)
                                    <span class="text-sm text-body">{{ $inquiry->organisation }}</span>
                                @endif
                            </div>

                            <x-status-badge
                                :label="$inquiry->status()->label()"
                                :tone="$statusTones[$inquiry->status()->value] ?? 'neutral'"
                            />
                        </div>

                        <dl class="grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="shrink-0 text-muted">Interest</dt>
                                <dd class="min-w-0 break-words text-right text-body sm:text-left">{{ $inquiry->interest->label() }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="shrink-0 text-muted">Email</dt>
                                <dd class="min-w-0 break-words text-right text-body sm:text-left">{{ $inquiry->email->value }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="shrink-0 text-muted">Submitted</dt>
                                <dd class="min-w-0 break-words text-right text-body sm:text-left">{{ $inquiry->submittedAt->format('j M Y') }}</dd>
                            </div>
                            @if ($inquiry->needsPartnerTriage())
                                <div class="flex justify-between gap-4 sm:justify-start">
                                    <dt class="shrink-0 text-muted">Triage</dt>
                                    <dd class="min-w-0 break-words text-right text-warning sm:text-left">Partner triage required</dd>
                                </div>
                            @endif
                        </dl>

                        @if (! empty($inquiry->status()->allowedTransitions()))
                            <div class="flex flex-wrap gap-3 border-t border-hairline pt-4">
                                @foreach ($inquiry->status()->allowedTransitions() as $target)
                                    <form method="POST" action="{{ route('admin.inquiries.transition', $inquiry->reference->value) }}">
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
