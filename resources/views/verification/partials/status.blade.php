@php
    $status = $row->status;
    if ($status === 'approved' && ! $row->isApproved()) { $status = 'expired'; }
    $until = $status === 'approved' ? $row->expires_at?->format('j M Y') : null;
    $labels = [
        'draft_started' => 'Draft started', 'consent_given' => 'Consent recorded', 'submitted' => 'Submitted for review',
        'in_review' => 'Review started', 'approved' => 'Approved', 'rejected' => 'Not approved',
        'info_requested' => 'More information requested', 'withdrawn' => 'Withdrawn', 'expired' => 'Expired',
    ];
    $events = $row->events->reject(fn ($e) => in_array($e->event, ['note_saved', 'files_purged', 'files_stripped'], true));
    $cta = ['identity' => 'Identity', 'company' => 'Company'][$kind];
@endphp
<div class="vf-wrap">
    @if (session('status'))<p class="ac-flash ac-flash-ok" role="status">{{ session('status') }}</p>@endif

    <section class="ac-panel ac-stack">
        <div class="ac-ph"><x-icon :name="$kind === 'identity' ? 'fingerprint' : 'building-2'" class="ac-pi" /><h2 class="ac-h3">{{ $cta }} verification</h2></div>
        <div><x-verification.chip :status="$status" :until="$until" /></div>

        @if ($status === 'submitted')
            <p>Thank you. Your submission is in the review queue. A person will look at it within {{ config('verification.review_window') }} and email you the outcome.</p>
        @elseif ($status === 'in_review')
            <p>A reviewer has started checking your documents. You will be emailed when there is a decision.</p>
        @elseif ($status === 'approved')
            <p>{{ $kind === 'identity' ? 'Your identity is verified. The network features that need it are now unlocked.' : 'Your company is verified and shows a badge beside your name.' }}@if ($until) It stays valid until {{ $until }}.@endif</p>
        @elseif ($status === 'expired')
            <p>This verification has expired. Start again from the verification page to renew it.</p>
        @endif

        @if ($row->status === 'rejected')
            <div class="vf-note vf-note--bad"><x-icon name="circle-x" /><div><strong>Not approved</strong>{{ $row->rejection_reason ?: 'Please check your documents and try again.' }}</div></div>
        @endif
        @if ($row->status === 'in_review' && $row->info_request)
            <div class="vf-note vf-note--warn"><x-icon name="message-square" /><div><strong>Our reviewer asked</strong>{{ $row->info_request }}</div></div>
        @endif
    </section>

    <section class="ac-panel ac-stack">
        <div class="ac-ph"><x-icon name="clipboard-list" class="ac-pi" /><h2 class="ac-h3">What you submitted</h2></div>
        <dl class="vf-dl">
            @foreach ($summary as $label => $value)
                <dt>{{ $label }}</dt><dd>{{ $value !== null && $value !== '' ? $value : 'Not provided' }}</dd>
            @endforeach
            @foreach ($files as $slot => $f)
                <dt>{{ $service->slotLabel($slot) }}</dt><dd>{{ $f['name'] ?? 'Uploaded' }}</dd>
            @endforeach
            @if ($row->submitted_at)<dt>Submitted</dt><dd>{{ $row->submitted_at->format('j M Y, H:i') }}</dd>@endif
            @if ($row->reviewed_at)<dt>Decision</dt><dd>{{ $row->reviewed_at->format('j M Y, H:i') }}</dd>@endif
        </dl>
    </section>

    @if ($events->isNotEmpty())
        <section class="ac-panel ac-stack">
            <div class="ac-ph"><x-icon name="history" class="ac-pi" /><h2 class="ac-h3">Timeline</h2></div>
            <ol class="vf-tl">
                @foreach ($events as $e)
                    <li class="is-done {{ $loop->last ? 'is-now' : '' }}"><strong>{{ $labels[$e->event] ?? ucfirst(str_replace('_', ' ', $e->event)) }}</strong>{{ $e->created_at?->format('j M Y, H:i') }}</li>
                @endforeach
            </ol>
        </section>
    @endif

    <div class="vf-actions">
        <a class="ac-btn" href="{{ route('verification.index') }}">Back to verification</a>
        @if ($row->isActive())
            <form method="POST" action="{{ route('verification.'.$kind.'.withdraw') }}" onsubmit="return confirm('Withdraw this submission? Your files will be deleted.')">
                @csrf @method('DELETE')
                <button class="ac-btn ac-btn-danger" type="submit"><x-icon name="trash-2" class="h-4 w-4" />Withdraw submission</button>
            </form>
        @endif
    </div>
</div>
