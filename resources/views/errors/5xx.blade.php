@php
    $reference = strtoupper(bin2hex(random_bytes(4)));
@endphp
@include('errors.minimal', [
    'code' => isset($exception) ? $exception->getStatusCode() : 500,
    'title' => 'Something went wrong',
    'headline' => 'Something went wrong on our side',
    'message' => 'This is not your doing. Please try again shortly.',
    'hint' => 'If it keeps happening, quote the reference below when you write to us.',
    'actions' => [['Go home', '/', true], ['Contact us', '/contact']],
    'reference' => $reference,
])
