<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0b0b0c">
    <title>{{ $title ?? $headline }} | Underground</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @include('errors._styles')
</head>
<body class="min-h-screen bg-ink font-sans text-body antialiased">
    <x-site-header />

    <main>
        @include('errors._panel', ['standalone' => false])
    </main>

    <x-site-footer />
    <x-mobile-tab-bar />
</body>
</html>
