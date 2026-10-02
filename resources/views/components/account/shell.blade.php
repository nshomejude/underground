@props(['title' => 'My Account', 'active' => 'overview'])

@php
    $siteSetting = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current();
    $user = auth()->user();
    $initials = collect(preg_split('/\s+/', trim((string) $user?->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: 'U';

    $nav = [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'home', 'href' => route('account.show')],
        ['key' => 'card', 'label' => 'Card', 'full' => 'Membership Card', 'icon' => 'card', 'href' => route('account.show').'#card'],
    ];
    if (\Illuminate\Support\Facades\Route::has('account.certificate')) {
        $nav[] = ['key' => 'certificate', 'label' => 'Certificate', 'icon' => 'cert', 'href' => route('account.certificate')];
    }
    $nav[] = ['key' => 'inquiries', 'label' => 'Inquiries', 'icon' => 'msg', 'href' => route('inquiries.track')];
    $nav[] = ['key' => 'security', 'label' => 'Security', 'icon' => 'lock', 'href' => route('account.settings').'#security'];
    $nav[] = ['key' => 'settings', 'label' => 'Settings', 'icon' => 'gear', 'href' => route('account.settings')];
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

        <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
            <defs>
                <symbol id="ai-home" viewBox="0 0 24 24"><path d="M3 11l9-7 9 7v9H3z"/></symbol>
                <symbol id="ai-card" viewBox="0 0 24 24"><rect x="2.5" y="5" width="19" height="14"/><path d="M2.5 10h19"/></symbol>
                <symbol id="ai-cert" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="13"/><circle cx="12" cy="10.5" r="3"/><path d="M9 21l3-3 3 3"/></symbol>
                <symbol id="ai-msg" viewBox="0 0 24 24"><path d="M3 5h18v12H9l-5 4v-4H3z"/></symbol>
                <symbol id="ai-lock" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10"/><path d="M8 11V7a4 4 0 018 0v4"/></symbol>
                <symbol id="ai-gear" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/></symbol>
                <symbol id="ai-out" viewBox="0 0 24 24"><path d="M10 4H4v16h6M14 8l4 4-4 4M18 12H9"/></symbol>
                <symbol id="ai-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"/></symbol>
                <symbol id="ai-dl" viewBox="0 0 24 24"><path d="M12 3v12M7 11l5 5 5-5M4 20h16"/></symbol>
                <symbol id="ai-share" viewBox="0 0 24 24"><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="M8 11l8-4M8 13l8 4"/></symbol>
                <symbol id="ai-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
                <symbol id="ai-chk" viewBox="0 0 24 24"><path d="M4 12l5 5L20 6"/></symbol>
                <symbol id="ai-key" viewBox="0 0 24 24"><circle cx="8" cy="12" r="4"/><path d="M12 12h9M18 12v4"/></symbol>
                <symbol id="ai-star" viewBox="0 0 24 24"><path d="M12 3l2.7 6 6.3.6-4.8 4.3 1.5 6.3L12 17l-5.7 3.2 1.5-6.3L3 9.6 9.3 9z"/></symbol>
                <symbol id="ai-eye" viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></symbol>
            </defs>
        </svg>

        <div class="ac-shell">
            <aside class="ac-side">
                <a href="{{ route('account.show') }}" class="ac-brand" aria-label="Underground member area">
                    <span class="ac-mono" aria-hidden="true">U</span>
                    <span><b>Underground</b><small>Member Network</small></span>
                </a>

                <nav aria-label="Member area" class="ac-nav-wrap">
                    <ul class="ac-nav">
                        @foreach ($nav as $item)
                            <li @class(["ac-hide-m" => $item["key"] === "security"])>
                                <a href="{{ $item['href'] }}" @if ($active === $item['key']) aria-current="page" @endif @isset($item['full']) aria-label="{{ $item['full'] }}" @endisset>
                                    <svg aria-hidden="true"><use href="#ai-{{ $item['icon'] }}"/></svg>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <div class="ac-foot">
                    <a href="{{ route('home') }}" class="ac-foot-link"><svg aria-hidden="true"><use href="#ai-globe"/></svg>Visit website</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ac-foot-link"><svg aria-hidden="true"><use href="#ai-out"/></svg>Sign out</button>
                    </form>
                </div>
            </aside>

            <div class="ac-main">
                <header class="ac-mbar">
                    <span class="ac-mono ac-mono-sm" aria-hidden="true">U</span>
                    <span class="ac-mbar-title">{{ $title }}</span>
                    <span class="ac-avatar" role="img" aria-label="{{ $user?->name }}">{{ $initials }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="ac-mbar-out">
                        @csrf
                        <button type="submit" aria-label="Sign out"><svg aria-hidden="true"><use href="#ai-out"/></svg></button>
                    </form>
                </header>

                <main id="main" tabindex="-1" class="ac-content">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <div class="ac-toast" id="ac-toast" role="status" aria-live="polite"></div>
        {{ $scripts ?? '' }}
    </body>
</html>
