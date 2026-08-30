@props([
    'title' => null,
    'eyebrow' => 'Admin',
    'description' => null,
    'maxWidth' => 'max-w-7xl',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? $title . ' — Admin — ' . config('app.name', 'Underground Network') : 'Admin — ' . config('app.name', 'Underground Network') }}</title>

        {{-- Blocking, pre-paint: apply a previously-saved theme choice
             before first render so switching themes never flashes the
             other one. Admin-only — the public site has no such script
             and always renders its fixed dark brand palette. --}}
        <script>
            (function () {
                try {
                    if (localStorage.getItem('admin-theme') === 'light') {
                        document.documentElement.setAttribute('data-theme', 'light');
                    }
                } catch (e) {}
            })();
        </script>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @fonts
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-ink font-sans text-sm text-body antialiased">
        <div class="flex min-h-screen">
            {{-- Desktop sidebar --}}
            <aside class="sticky top-0 hidden h-screen w-60 shrink-0 flex-col overflow-y-auto bg-surface lg:flex">
                <a href="{{ route('admin.index') }}" class="flex items-center gap-2.5 px-4 py-4">
                    <x-brand-mark :compact="true" />
                </a>

                <div class="flex-1 overflow-y-auto px-2.5 py-2">
                    <x-admin.sidebar-nav />
                </div>

                <div class="flex flex-col gap-0.5 border-t border-hairline px-2.5 py-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 rounded-adm px-2.5 py-1.5 text-[13px] font-medium text-muted transition-colors hover:bg-surface-raised hover:text-cream">
                        <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0 rotate-180" />
                        View Site
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2.5 rounded-adm px-2.5 py-1.5 text-left text-[13px] font-medium text-muted transition-colors hover:bg-surface-raised hover:text-cream">
                            <x-icon name="x" class="h-3.5 w-3.5 shrink-0" />
                            Log Out
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Mobile off-canvas sidebar --}}
            <div id="admin-sidebar" data-drawer class="fixed inset-0 z-50 hidden lg:hidden" aria-hidden="true">
                <div data-drawer-close class="absolute inset-0 bg-ink/80"></div>

                <div class="absolute inset-y-0 left-0 flex w-full max-w-xs flex-col overflow-y-auto bg-surface shadow-adm-md">
                    <div class="flex items-center justify-between px-4 py-4">
                        <x-brand-mark :compact="true" />
                        <button type="button" data-drawer-close aria-label="Close menu" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-adm border border-hairline text-body hover:border-gold hover:text-gold">
                            <x-icon name="x" class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    <div class="flex-1 px-2.5 py-2">
                        <x-admin.sidebar-nav />
                    </div>

                    <div class="flex flex-col gap-0.5 border-t border-hairline px-2.5 py-3">
                        <a href="{{ url('/') }}" class="flex items-center gap-2.5 rounded-adm px-2.5 py-1.5 text-[13px] font-medium text-muted transition-colors hover:bg-surface-raised hover:text-cream">
                            <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0 rotate-180" />
                            View Site
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2.5 rounded-adm px-2.5 py-1.5 text-left text-[13px] font-medium text-muted transition-colors hover:bg-surface-raised hover:text-cream">
                                <x-icon name="x" class="h-3.5 w-3.5 shrink-0" />
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Main column --}}
            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-30 flex h-14 shrink-0 items-center gap-4 bg-ink/90 px-4 shadow-[0_1px_0_var(--color-hairline)] backdrop-blur sm:px-6 lg:px-8">
                    <button
                        type="button"
                        data-drawer-toggle="admin-sidebar"
                        aria-label="Open admin menu"
                        aria-expanded="false"
                        aria-controls="admin-sidebar"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-adm border border-hairline text-body lg:hidden"
                    >
                        <x-icon name="menu" class="h-4 w-4" />
                    </button>

                    <x-brand-mark :compact="true" class="lg:hidden" />

                    <nav aria-label="Breadcrumb" class="hidden items-center gap-1.5 text-[13px] text-muted lg:flex">
                        <span>{{ $eyebrow }}</span>
                        @if ($title)
                            <x-icon name="chevron-right" class="h-3 w-3 shrink-0" />
                            <span class="font-medium text-cream">{{ $title }}</span>
                        @endif
                    </nav>

                    <div class="ml-auto flex items-center gap-2">
                        <button
                            type="button"
                            data-theme-toggle
                            aria-label="Toggle light and dark mode"
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-adm text-muted transition-colors hover:bg-surface-raised hover:text-cream"
                        >
                            <x-icon name="sun" class="h-4 w-4" data-theme-icon="light" />
                            <x-icon name="moon" class="hidden h-4 w-4" data-theme-icon="dark" />
                        </button>

                        @auth
                            <span class="hidden items-center gap-2 border-l border-hairline pl-3 sm:flex">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gold/15 text-[11px] font-semibold uppercase text-gold">
                                    {{ Str::of(auth()->user()->name)->substr(0, 1) }}
                                </span>
                                <span class="flex flex-col leading-tight">
                                    <span class="text-[13px] font-medium text-cream">{{ auth()->user()->name }}</span>
                                    <span class="text-[11px] text-muted">Staff Admin</span>
                                </span>
                            </span>
                        @endauth
                    </div>
                </header>

                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    <div class="mx-auto flex w-full {{ $maxWidth }} flex-col gap-6">
                        @if ($title)
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                <div class="flex flex-col gap-1">
                                    <h1 class="text-xl font-semibold tracking-tight text-cream sm:text-2xl">{{ $title }}</h1>
                                    @if ($description)
                                        <p class="text-sm text-muted">{{ $description }}</p>
                                    @endif
                                </div>
                                @isset($actions)
                                    <div class="flex shrink-0 items-center gap-3">
                                        {{ $actions }}
                                    </div>
                                @endisset
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="flex items-center gap-3 rounded-adm border border-success/25 bg-success/10 px-4 py-3 text-sm text-success">
                                <x-icon name="check-circle" class="h-4 w-4 shrink-0" />
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="flex items-center gap-3 rounded-adm border border-danger/25 bg-danger/10 px-4 py-3 text-sm text-danger">
                                <x-icon name="flag" class="h-4 w-4 shrink-0" />
                                {{ session('error') }}
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
