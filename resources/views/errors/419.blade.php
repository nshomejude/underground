@php
    $returnUrl = '/';
    try {
        $previous = url()->previous();
        if (is_string($previous) && $previous !== '' && $previous !== url()->full()) {
            $returnUrl = $previous;
        }
    } catch (\Throwable) {
    }
@endphp
@include('errors._shell', [
    'code' => 419,
    'title' => 'Your session expired',
    'headline' => 'Your session expired',
    'message' => 'For your security the page timed out before it was sent. Nothing was submitted and nothing was changed.',
    'hint' => 'Reload the page and fill in the form again. Anything you typed may need to be re-entered.',
    'actions' => [['Reload the page', $returnUrl, true], ['Go home', '/']],
])
