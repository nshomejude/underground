@props(['title' => null, 'description' => null, 'type' => 'website', 'image' => null, 'schema' => []])

@php
    $siteSetting = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <x-seo-head :title="$title" :description="$description" :type="$type" :image="$image" :schema="$schema" :site-setting="$siteSetting" />

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" href="{{ asset('images/app-icon-192.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

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
        {{-- Mount point for the on-demand FR translator; empty and inert until a visitor picks FR. --}}
        <div id="google_translate_element" class="hidden" aria-hidden="true" translate="no"></div>
    </body>
</html>
