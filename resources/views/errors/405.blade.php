@include('errors._shell', [
    'code' => 405,
    'title' => 'That did not work here',
    'headline' => 'That action is not available here',
    'message' => 'This address cannot be used that way. Head back and try again from the page itself.',
    'actions' => [['Go home', '/', true], ['Contact us', '/contact']],
])
