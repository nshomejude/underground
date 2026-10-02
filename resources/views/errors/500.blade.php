@php
    $reference = strtoupper(bin2hex(random_bytes(4)));
    try {
        logger()->error('Error page 500 shown', ['reference' => $reference, 'path' => request()->path()]);
    } catch (\Throwable) {
    }
@endphp
@include('errors.minimal', [
    'code' => 500,
    'title' => 'Something went wrong',
    'headline' => 'Something went wrong on our side',
    'message' => 'This is not your doing. Our team has been told, and it is usually fixed quickly.',
    'hint' => 'Please try again shortly. If it keeps happening, quote the reference below when you write to us.',
    'actions' => [['Go home', '/', true], ['Contact us', '/contact']],
    'reference' => $reference,
])
