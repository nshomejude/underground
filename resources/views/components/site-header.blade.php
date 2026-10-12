@php
    // Primary IA. Items with a 'children' array render as a hover/click
    // disclosure on desktop and a <details> group in the mobile drawer.
    // Route names not yet merged from parallel workstreams resolve through
    // OptionalRoute so this header (rendered on every page) never 500s —
    // see app/Support/OptionalRoute.php.
    $navItems = [
        [
            'label' => 'Home',
            'href' => route('home'),
        ],
        [
            'label' => 'About',
            'href' => route('about'),
        ],
        [
            'label' => 'Capabilities',
            'href' => route('capabilities.index'),
        ],
        [
            'label' => 'Global Reach',
            'href' => route('global-reach'),
            'children' => [
                'Overview' => route('global-reach'),
                'Global Alliance' => \App\Support\OptionalRoute::url('global-alliance', [], '/global-alliance'),
                'Locations' => \App\Support\OptionalRoute::url('locations', [], '/locations'),
            ],
        ],
        [
            'label' => 'Reports Hub',
            'href' => \App\Support\OptionalRoute::url('reports-hub.index', [], '/reports-hub'),
            'children' => [
                'Overview' => \App\Support\OptionalRoute::url('reports-hub.index', [], '/reports-hub'),
                'Strategic Notes' => \App\Support\OptionalRoute::url('reports-hub.strategic-notes', [], '/reports-hub/strategic-notes'),
            ],
        ],
        [
            'label' => 'Team',
            'href' => route('team'),
            'children' => [
                'Overview' => route('team'),
                'Senior Leadership' => \App\Support\OptionalRoute::url('team.senior-leadership', [], '/team/senior-leadership'),
                'Gallery' => \App\Support\OptionalRoute::url('team.gallery', [], '/team/gallery'),
                'Case Studies' => \App\Support\OptionalRoute::url('team.case-studies', [], '/team/case-studies'),
            ],
        ],
        [
            'label' => 'Flex Media Group',
            'href' => \App\Support\OptionalRoute::url('flex-media-group', [], '/flex-media-group'),
        ],
        [
            'label' => 'Membership',
            'href' => route('membership.index'),
            'children' => [
                'Overview' => route('membership.index'),
                'Membership Plus' => \App\Support\OptionalRoute::url('membership.plus', [], '/membership/plus'),
                'Events' => \App\Support\OptionalRoute::url('membership.events', [], '/membership/events'),
                'Apply' => route('membership.apply', ['tier' => 'principal-circle']),
            ],
        ],
    ];

    $navLinkClass = 'inline-flex shrink-0 items-center gap-1 whitespace-nowrap text-xs font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold';
@endphp

<header class="sticky top-0 z-40 border-b border-border bg-ink/95 backdrop-blur">
    {{-- Utility bar: language, account area, and the briefing CTA get their
         own thin row so the primary nav below never has to compete with
         them for width (keeps the 7-item IA comfortably clear of overflow
         at common desktop widths, 1280–1440px included). --}}
    <div class="hidden border-b border-border/60 xl:block">
        <div class="mx-auto flex max-w-7xl items-center justify-end gap-4 px-4 py-2 sm:px-6 lg:px-8">
            <nav aria-label="Language" translate="no" data-lang-switch class="flex shrink-0 items-center gap-1.5 text-xs font-semibold uppercase tracking-widest">
                {{-- EN is the authored language; FR machine-translates the page on click (see resources/js/app.js). --}}
                <a href="{{ url()->current() }}" data-lang="en" class="text-gold" aria-current="true">EN</a>
                <span class="text-muted" aria-hidden="true">/</span>
                <a href="{{ url()->current() }}" data-lang="fr" hreflang="fr" class="text-muted transition-colors hover:text-gold">FR</a>
            </nav>

            @auth
                @if (auth()->user()->is_admin)
                    <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold">
                        <x-icon name="shield-check" class="h-3.5 w-3.5" />
                        Admin
                    </a>
                @endif
                <a
                    href="{{ route('account.show') }}"
                    aria-label="Your account"
                    title="Your account"
                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center border border-border text-body transition-colors hover:border-gold hover:text-gold"
                >
                    <x-icon name="user" class="h-3.5 w-3.5" />
                    <span class="sr-only">Account</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold uppercase tracking-widest text-muted transition-colors hover:text-gold">
                        Log Out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-xs font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold">
                    Login
                </a>
            @endauth

            <x-button variant="primary" href="{{ \App\Support\OptionalRoute::url('consultations.create', [], '/consultations') }}" class="!px-4 !py-2">
                Request an Executive Briefing
            </x-button>
        </div>
    </div>

    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="shrink-0">
            <x-brand-mark :lockup="true" />
        </a>

        <nav class="hidden shrink-0 items-center gap-4 xl:flex" aria-label="Primary">
            @foreach ($navItems as $item)
                @if (! empty($item['children']))
                    <div class="group relative shrink-0" data-nav-dropdown>
                        <button
                            type="button"
                            data-nav-dropdown-toggle
                            aria-expanded="false"
                            class="{{ $navLinkClass }}"
                        >
                            {{ $item['label'] }}
                            <x-icon name="chevron-down" class="h-3 w-3 shrink-0 transition-transform group-data-[nav-open]:rotate-180" />
                        </button>

                        <div
                            data-nav-dropdown-panel
                            class="invisible absolute left-0 top-full z-10 mt-2 w-56 border border-border bg-surface opacity-0 shadow-xl transition-opacity duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100 group-data-[nav-open]:visible group-data-[nav-open]:opacity-100"
                        >
                            <ul class="flex flex-col py-2">
                                @foreach ($item['children'] as $label => $href)
                                    <li>
                                        <a href="{{ $href }}" class="block px-4 py-2 text-xs font-semibold uppercase tracking-wide text-body transition-colors hover:text-gold">
                                            {{ $label }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <a href="{{ $item['href'] }}" class="{{ $navLinkClass }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <button
            type="button"
            data-drawer-toggle="mobile-drawer"
            aria-label="Open menu"
            aria-expanded="false"
            aria-controls="mobile-drawer"
            class="flex h-10 w-10 shrink-0 items-center justify-center border border-gold text-gold xl:hidden"
        >
            <x-icon name="menu" class="h-5 w-5" />
        </button>
    </div>
</header>

<x-mobile-drawer />
