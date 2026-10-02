@php
    $retry = null;
    try {
        $retry = isset($exception) ? ($exception->getHeaders()['Retry-After'] ?? null) : null;
    } catch (\Throwable) {
    }
    $seconds = is_numeric($retry) ? max(1, (int) $retry) : null;
    $hint = $seconds === null
        ? 'Please wait a minute, then try again.'
        : ($seconds >= 90
            ? 'Please try again in about '.ceil($seconds / 60).' minutes.'
            : 'Please try again in about '.$seconds.' '.($seconds === 1 ? 'second' : 'seconds').'.');
@endphp
@include('errors._shell', [
    'code' => 429,
    'title' => 'Too many requests',
    'headline' => 'Too many requests',
    'message' => 'You have sent a lot of requests in a short time. This is a gentle pause, not a block.',
    'hint' => $hint,
    'actions' => [['Go home', '/', true], ['Contact us', '/contact']],
])
