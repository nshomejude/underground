{{-- Standalone error document. Depends on nothing: no database, session, auth, settings
     repository, Vite or components. Needs: $code, $headline, $message; optional $title,
     $actions, $hint, $reference, $refresh (seconds, auto-reload). --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b0b0c">
    <title>{{ $title ?? $headline }} | Underground</title>
    @if (! empty($refresh))
        <meta http-equiv="refresh" content="{{ (int) $refresh }}">
    @endif
    @include('errors._styles')
</head>
<body class="er-page">
    <main>
        @include('errors._panel', ['standalone' => true])
    </main>
</body>
</html>
