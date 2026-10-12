@php
    // Primary IA — kept in sync with site-header.blade.php's $navItems.
    // Items with a 'children' array render as a <details>/<summary>
    // disclosure group here instead of the desktop hover/click dropdown.
    $navItems = [
        [
            'label' => 'About',
            'href' => route('about'),
            'icon' => 'building-2',
        ],
        [
            'label' => 'Capabilities',
            'href' => route('capabilities.index'),
            'icon' => 'landmark',
        ],
        [
            'label' => 'Global Reach',
            'href' => route('global-reach'),
            'icon' => 'globe',
            'children' => [
                'Overview' => route('global-reach'),
                'Global Alliance' => \App\Support\OptionalRoute::url('global-alliance', [], '/global-alliance'),
                'Locations' => \App\Support\OptionalRoute::url('locations', [], '/locations'),
            ],
        ],
        [
            'label' => 'Reports Hub',
            'href' => \App\Support\OptionalRoute::url('reports-hub.index', [], '/reports-hub'),
            'icon' => 'newspaper',
            'children' => [
                'Overview' => \App\Support\OptionalRoute::url('reports-hub.index', [], '/reports-hub'),
                'Strategic Notes' => \App\Support\OptionalRoute::url('reports-hub.strategic-notes', [], '/reports-hub/strategic-notes'),
            ],
        ],
        [
            'label' => 'Team',
            'href' => route('team'),
            'icon' => 'users',
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
            'icon' => 'megaphone',
        ],
        [
            'label' => 'Membership',
            'href' => route('membership.index'),
            'icon' => 'award',
            'children' => [
                'Overview' => route('membership.index'),
                'Membership Plus' => \App\Support\OptionalRoute::url('membership.plus', [], '/membership/plus'),
                'Events' => \App\Support\OptionalRoute::url('membership.events', [], '/membership/events'),
                'Apply' => route('membership.apply', ['tier' => 'principal-circle']),
            ],
        ],
    ];

    $linkClass = 'inline-flex items-center gap-3 text-sm font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold';
@endphp

<div
    id="mobile-drawer"
    data-drawer
    class="fixed inset-0 z-50 hidden"
    aria-hidden="true"
>
    <div data-drawer-close class="absolute inset-0 bg-ink/80"></div>

    <div class="absolute inset-y-0 right-0 flex w-full max-w-xs flex-col gap-8 border-l border-border bg-surface px-6 py-6 overflow-y-auto">
        <div class="flex items-center justify-between">
            <x-brand-mark :compact="true" />

            <button
                type="button"
                data-drawer-close
                aria-label="Close menu"
                class="flex h-10 w-10 shrink-0 items-center justify-center border border-gold text-gold"
            >
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <nav class="flex flex-col gap-5">
            @foreach ($navItems as $item)
                @if (! empty($item['children']))
                    <details class="group">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 marker:hidden {{ $linkClass }}">
                            <span class="inline-flex items-center gap-3">
                                <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0 text-gold" />
                                {{ $item['label'] }}
                            </span>
                            <x-icon name="chevron-down" class="h-3.5 w-3.5 shrink-0 text-muted transition-transform group-open:rotate-180" />
                        </summary>

                        <div class="mt-3 flex flex-col gap-3 border-l border-border pl-4">
                            @foreach ($item['children'] as $label => $href)
                                <a href="{{ $href }}" class="text-xs font-semibold uppercase tracking-wide text-body transition-colors hover:text-gold">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </details>
                @else
                    <a href="{{ $item['href'] }}" class="{{ $linkClass }}">
                        <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0 text-gold" />
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach

            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest">
                <a href="{{ url()->current() }}" class="text-gold" aria-current="true">EN</a>
                <span class="text-muted" aria-hidden="true">/</span>
                {{-- French content is a later lot; link kept inert until that workstream ships. --}}
                <a href="#" class="text-muted transition-colors hover:text-gold">FR</a>
            </div>

            @auth
                @if (auth()->user()->is_admin)
                    <a href="{{ route('admin.index') }}" class="{{ $linkClass }}">
                        <x-icon name="shield-check" class="h-4 w-4 shrink-0 text-gold" />
                        Admin
                    </a>
                @endif
                <a href="{{ route('account.show') }}" class="{{ $linkClass }}" aria-label="Your account" title="Your account">
                    <x-icon name="user" class="h-4 w-4 shrink-0 text-gold" />
                    Account
                </a>
            @else
                <a href="{{ route('login') }}" class="{{ $linkClass }}">
                    <x-icon name="lock" class="h-4 w-4 shrink-0 text-gold" />
                    Login
                </a>
                <a href="{{ route('register') }}" class="{{ $linkClass }}">
                    <x-icon name="users" class="h-4 w-4 shrink-0 text-gold" />
                    Register
                </a>
            @endauth
        </nav>

        <div class="mt-auto flex flex-col gap-3">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button variant="secondary" type="submit" class="w-full">
                        Log Out
                    </x-button>
                </form>
            @endauth

            <x-button variant="primary" href="{{ \App\Support\OptionalRoute::url('consultations.create', [], '/consultations') }}" class="w-full">
                Request an Executive Briefing
            </x-button>

            <x-button variant="secondary" href="{{ route('inquiries.create') }}" class="w-full">
                Confidential Inquiry
                <x-icon name="lock" class="h-3.5 w-3.5" />
            </x-button>
        </div>
    </div>
</div>
