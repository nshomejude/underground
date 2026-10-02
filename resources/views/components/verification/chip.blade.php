@props(['status', 'until' => null])
@php
    $map = [
        'none' => ['Not started', 'info', 'clock'],
        'draft' => ['Draft', 'info', 'file-text'],
        'submitted' => ['Submitted', 'info', 'clock'],
        'in_review' => ['In review', 'warn', 'scan-line'],
        'approved' => ['Approved'.($until ? ', valid until '.$until : ''), 'ok', 'shield-check'],
        'rejected' => ['Rejected', 'bad', 'circle-x'],
        'expired' => ['Expired', 'bad', 'triangle-alert'],
        'withdrawn' => ['Withdrawn', 'info', 'x'],
    ];
    [$label, $tone, $icon] = $map[$status] ?? $map['none'];
@endphp
<span class="vf-chip vf-chip--{{ $tone }}"><x-icon :name="$icon" />{{ $label }}</span>
