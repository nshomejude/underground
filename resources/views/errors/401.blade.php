@include('errors.minimal', [
    'code' => 401,
    'title' => 'Sign in required',
    'headline' => 'Please sign in to continue',
    'message' => 'This page is for signed-in members. Sign in and we will take you straight there.',
    'actions' => [['Sign in', '/login', true], ['Go home', '/']],
])
