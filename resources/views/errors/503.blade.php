@php
    $reference = strtoupper(bin2hex(random_bytes(4)));
    $note = '';
    try {
        $note = isset($exception) ? trim((string) $exception->getMessage()) : '';
    } catch (\Throwable) {
    }
    if ($note === '') {
        $note = 'We are carrying out some quiet maintenance. Please check back in a few minutes.';
    }
@endphp
@include('errors.minimal', [
    'code' => 503,
    'title' => 'Briefly unavailable',
    'headline' => 'Briefly unavailable',
    'message' => $note,
    'hint' => 'This page checks again by itself every minute.',
    'actions' => [['Try again', '/', true]],
    'reference' => $reference,
    'refresh' => 60,
])
