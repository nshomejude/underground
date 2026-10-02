@include('errors._shell', [
    'code' => 403,
    'title' => 'This room is private',
    'headline' => 'This room is private',
    'message' => 'You do not have access to this page. If you think that is a mistake, tell us and we will put it right.',
    'actions' => [['Go home', '/', true], ['Contact us', '/contact']],
    'withAccount' => true,
])
