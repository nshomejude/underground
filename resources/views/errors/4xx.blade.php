@include('errors.minimal', [
    'code' => isset($exception) ? $exception->getStatusCode() : 400,
    'title' => 'Request not completed',
    'headline' => 'We could not complete that request',
    'message' => 'Something about that request did not work. Head back and try again.',
    'actions' => [['Go home', '/', true], ['Contact us', '/contact']],
])
