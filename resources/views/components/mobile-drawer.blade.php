@php
    $navLinks = [
        'About' => route('about'),
        'Capabilities' => url('/').'#capabilities',
        'Expertise' => '#',
        'Global Reach' => url('/').'#reach',
        'Insights' => route('insights.index'),
        'Careers' => '#',
        'Contact' => route('contact'),
    ];
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

        <nav class="flex flex-col gap-6">
            @foreach ($navLinks as $label => $href)
                <a href="{{ $href }}" class="text-sm font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold">
                    {{ $label }}
                </a>
            @endforeach

            @auth
                @if (auth()->user()->is_admin)
                    <a href="{{ route('admin.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold">
                        <x-icon name="shield-check" class="h-4 w-4" />
                        Admin
                    </a>
                @endif
                <a href="{{ route('account.show') }}" class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold">
                    <x-icon name="user" class="h-4 w-4" />
                    Account
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold">
                    Login
                </a>
                <a href="{{ route('register') }}" class="text-sm font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold">
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

            <x-button variant="primary" href="{{ route('inquiries.create') }}" class="w-full">
                Confidential Inquiry
                <x-icon name="lock" class="h-3.5 w-3.5" />
            </x-button>
        </div>
    </div>
</div>
