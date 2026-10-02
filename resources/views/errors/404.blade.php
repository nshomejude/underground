@include('errors._shell', [
    'code' => 404,
    'title' => 'Page not found',
    'headline' => 'This page has moved in the shadows',
    'message' => 'The page you are looking for is not here any more, or never was. Let us get you back on solid ground.',
    'actions' => [['Go home', '/', true], ['Contact us', '/contact']],
    'withAccount' => true,
])
