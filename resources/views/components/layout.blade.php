@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? $title . ' — ' . config('app.name', 'Underground Network') : config('app.name', 'Underground Network') }}</title>

        {{-- Guard against a missing production build (e.g. before `npm run build`
             has been run, or in the test environment) so the shell still renders
             rather than throwing a Vite manifest exception. --}}
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @fonts
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-ink font-sans text-body antialiased">
        <x-site-header />

        <main class="pb-20 lg:pb-0">
            {{ $slot }}
        </main>

        <x-site-footer />
        <x-mobile-tab-bar />
    </body>
</html>
