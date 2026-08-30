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

<x-layout title="Confidential Inquiries">
    <section class="mx-auto flex max-w-5xl flex-col gap-8 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Staff Review">Confidential Inquiries</x-section-heading>

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

        @if (empty($inquiries))
            <p class="border border-border bg-surface px-6 py-8 text-sm text-muted">
                No confidential inquiries have been submitted yet.
            </p>
        @else
            <div class="flex flex-col gap-4">
                @foreach ($inquiries as $inquiry)
                    <article class="flex flex-col gap-4 border border-border bg-surface px-5 py-5 sm:px-6 sm:py-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <span class="font-mono text-xs tracking-wider text-muted">{{ $inquiry->reference->value }}</span>
                                <h3 class="font-serif text-xl font-semibold text-cream">{{ $inquiry->name }}</h3>
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
                                <dt class="text-muted">Interest</dt>
                                <dd class="text-body">{{ $inquiry->interest->label() }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="text-muted">Email</dt>
                                <dd class="text-body">{{ $inquiry->email->value }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 sm:justify-start">
                                <dt class="text-muted">Submitted</dt>
                                <dd class="text-body">{{ $inquiry->submittedAt->format('j M Y') }}</dd>
                            </div>
                            @if ($inquiry->needsPartnerTriage())
                                <div class="flex justify-between gap-4 sm:justify-start">
                                    <dt class="text-muted">Triage</dt>
                                    <dd class="text-warning">Partner triage required</dd>
                                </div>
                            @endif
                        </dl>

                        @if (! empty($inquiry->status()->allowedTransitions()))
                            <div class="flex flex-wrap gap-3 border-t border-border pt-4">
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
    </section>
</x-layout>
