@props(['title' => null])

@php
    $siteSetting = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? $title . ' — ' . config('app.name', 'Underground Network') : config('app.name', 'Underground Network') }}</title>
        <meta name="description" content="{{ $siteSetting->metaDescription }}">
        <meta property="og:title" content="{{ $title ? $title . ' — ' . $siteSetting->metaTitle : $siteSetting->metaTitle }}">
        <meta property="og:description" content="{{ $siteSetting->metaDescription }}">
        @if ($siteSetting->ogImageUrl)
            <meta property="og:image" content="{{ $siteSetting->ogImageUrl }}">
            <meta name="twitter:card" content="summary_large_image">
        @endif
        @if ($siteSetting->twitterHandle)
            <meta name="twitter:site" content="{{ $siteSetting->twitterHandle }}">
        @endif

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

        <main>
            {{ $slot }}
        </main>

        <x-site-footer />
        <x-mobile-tab-bar />
    </body>
</html>
