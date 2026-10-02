@props(['title' => 'My Account', 'active' => 'overview'])

@php
    $siteSetting = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current();
    $user = auth()->user();
    $initials = collect(preg_split('/\s+/', trim((string) $user?->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: 'U';

    // The full member menu, identical on desktop (sidebar) and mobile (drawer).
    $nav = [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'home', 'href' => route('account.show')],
        ['key' => 'card', 'label' => 'Membership Card', 'icon' => 'credit-card', 'href' => route('account.show').'#card'],
        ['key' => 'certificate', 'label' => 'Certificate', 'icon' => 'award', 'href' => route('account.certificate')],
        ['key' => 'applications', 'label' => 'Applications', 'icon' => 'file-text', 'href' => route('account.applications')],
        ['key' => 'inquiries', 'label' => 'Inquiries', 'icon' => 'message-square', 'href' => route('inquiries.track')],
        ['key' => 'documents', 'label' => 'Documents', 'icon' => 'folder-open', 'href' => route('account.documents')],
        ['key' => 'security', 'label' => 'Security', 'icon' => 'shield-check', 'href' => route('account.security')],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'settings', 'href' => route('account.settings')],
    ];

    // Quick links for the mobile bottom bar (the drawer holds everything).
    $tabs = collect($nav)->whereIn('key', ['overview', 'card', 'certificate', 'inquiries'])->values();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

        <x-seo-head :title="$title" :site-setting="$siteSetting" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @fonts
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="ac-body">
        <a class="ac-skip" href="#main">Skip to main content</a>

        <div class="ac-shell">
            <div class="ac-backdrop" data-ac-close hidden></div>

            <aside class="ac-side" id="ac-side" aria-label="Member menu">
                <div class="ac-side-head">
                    <a href="{{ route('account.show') }}" class="ac-brand" aria-label="Underground member area">
                        <span class="ac-mono" aria-hidden="true">U</span>
                        <span><b>Underground</b><small>Member Network</small></span>
                    </a>
                    <button type="button" class="ac-iconbtn ac-close" data-ac-close aria-label="Close menu"><x-icon name="x" /></button>
                </div>

                <nav aria-label="Member area" class="ac-nav-wrap">
                    <ul class="ac-nav">
                        @foreach ($nav as $item)
                            <li>
                                <a href="{{ $item['href'] }}" @if ($active === $item['key']) aria-current="page" @endif>
                                    <x-icon :name="$item['icon']" />
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="ac-signout"><x-icon name="log-out" /><span>Sign out</span></button>
                            </form>
                        </li>
                    </ul>
                </nav>

                <div class="ac-foot">
                    <a href="{{ route('home') }}" class="ac-foot-link"><x-icon name="globe" /><span>Visit website</span></a>
                </div>
            </aside>

            <div class="ac-main">
                <header class="ac-mbar">
                    <button type="button" class="ac-iconbtn" data-ac-open aria-controls="ac-side" aria-expanded="false" aria-label="Open menu"><x-icon name="menu" /></button>
                    <span class="ac-mbar-title">{{ $title }}</span>
                    <span class="ac-avatar" role="img" aria-label="{{ $user?->name }}">{{ $initials }}</span>
                </header>

                <main id="main" tabindex="-1" class="ac-content">
                    {{ $slot }}
                </main>
            </div>

            <nav class="ac-tabbar" aria-label="Quick links">
                @foreach ($tabs as $item)
                    <a href="{{ $item['href'] }}" @if ($active === $item['key']) aria-current="page" @endif>
                        <x-icon :name="$item['icon']" />
                        <span>{{ $item['key'] === 'card' ? 'Card' : $item['label'] }}</span>
                    </a>
                @endforeach
                <button type="button" data-ac-open aria-controls="ac-side" aria-label="Open full menu">
                    <x-icon name="menu" />
                    <span>Menu</span>
                </button>
            </nav>
        </div>

        <div class="ac-toast" id="ac-toast" role="status" aria-live="polite"></div>

        <script>
            (function () {
                var side = document.getElementById('ac-side');
                var back = document.querySelector('.ac-backdrop');
                var openers = document.querySelectorAll('[data-ac-open]');
                function setOpen(on) {
                    side.classList.toggle('is-open', on);
                    back.hidden = !on;
                    document.body.classList.toggle('ac-lock', on);
                    openers.forEach(function (b) { b.setAttribute('aria-expanded', on ? 'true' : 'false'); });
                    if (on) { var first = side.querySelector('a, button'); if (first) first.focus(); }
                }
                openers.forEach(function (b) { b.addEventListener('click', function () { setOpen(true); }); });
                document.querySelectorAll('[data-ac-close]').forEach(function (b) { b.addEventListener('click', function () { setOpen(false); }); });
                side.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', function () { setOpen(false); }); });
                document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
            })();
        </script>

        {{ $scripts ?? '' }}
    </body>
</html>
