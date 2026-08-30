@props([
    'title' => null,
    'eyebrow' => 'Admin',
    'maxWidth' => 'max-w-5xl',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? $title . ' — Admin — ' . config('app.name', 'Underground Network') : 'Admin — ' . config('app.name', 'Underground Network') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @fonts
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-ink font-sans text-body antialiased">
        <div class="flex min-h-screen">
            {{-- Desktop sidebar --}}
            <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col overflow-y-auto border-r border-border bg-surface lg:flex">
                <a href="{{ route('admin.index') }}" class="border-b border-border px-5 py-5">
                    <x-brand-mark :compact="true" />
                </a>

                <div class="flex-1 overflow-y-auto px-3 py-6">
                    <x-admin.sidebar-nav />
                </div>

                <div class="flex flex-col gap-1 border-t border-border px-3 py-4">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-body transition-colors hover:bg-surface-raised hover:text-cream">
                        <x-icon name="arrow-right" class="h-4 w-4 shrink-0 rotate-180" />
                        View Site
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-sm font-medium text-muted transition-colors hover:bg-surface-raised hover:text-cream">
                            <x-icon name="x" class="h-4 w-4 shrink-0" />
                            Log Out
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Mobile off-canvas sidebar --}}
            <div id="admin-sidebar" data-drawer class="fixed inset-0 z-50 hidden lg:hidden" aria-hidden="true">
                <div data-drawer-close class="absolute inset-0 bg-ink/80"></div>

                <div class="absolute inset-y-0 left-0 flex w-full max-w-xs flex-col overflow-y-auto border-r border-border bg-surface">
                    <div class="flex items-center justify-between border-b border-border px-5 py-5">
                        <x-brand-mark :compact="true" />
                        <button type="button" data-drawer-close aria-label="Close menu" class="flex h-9 w-9 shrink-0 items-center justify-center border border-gold text-gold">
                            <x-icon name="x" class="h-4 w-4" />
                        </button>
                    </div>

                    <div class="flex-1 px-3 py-6">
                        <x-admin.sidebar-nav />
                    </div>

                    <div class="flex flex-col gap-1 border-t border-border px-3 py-4">
                        <a href="{{ url('/') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-body transition-colors hover:bg-surface-raised hover:text-cream">
                            <x-icon name="arrow-right" class="h-4 w-4 shrink-0 rotate-180" />
                            View Site
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-sm font-medium text-muted transition-colors hover:bg-surface-raised hover:text-cream">
                                <x-icon name="x" class="h-4 w-4 shrink-0" />
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Main column --}}
            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex items-center gap-4 border-b border-border bg-ink/95 px-4 py-4 backdrop-blur sm:px-6 lg:hidden">
                    <button
                        type="button"
                        data-drawer-toggle="admin-sidebar"
                        aria-label="Open admin menu"
                        aria-expanded="false"
                        aria-controls="admin-sidebar"
                        class="flex h-10 w-10 shrink-0 items-center justify-center border border-gold text-gold"
                    >
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>
                    <x-brand-mark :compact="true" />
                </header>

                <main class="flex-1 px-4 py-10 sm:px-6 lg:px-10 lg:py-12">
                    <div class="mx-auto flex w-full {{ $maxWidth }} flex-col gap-8">
                        @if ($title)
                            <x-section-heading :eyebrow="$eyebrow">{{ $title }}</x-section-heading>
                        @endif

                        @if (session('status'))
                            <div class="flex items-center gap-3 border border-success/40 bg-success/10 px-4 py-3 text-sm text-success">
                                <x-icon name="check-circle" class="h-5 w-5 shrink-0" />
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="flex items-center gap-3 border border-danger/40 bg-danger/10 px-4 py-3 text-sm text-danger">
                                <x-icon name="flag" class="h-5 w-5 shrink-0" />
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
