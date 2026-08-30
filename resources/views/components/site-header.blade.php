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

<header class="sticky top-0 z-40 border-b border-border bg-ink/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="shrink-0">
            <x-brand-mark />
        </a>

        <nav class="hidden items-center gap-8 lg:flex" aria-label="Primary">
            @foreach ($navLinks as $label => $href)
                <a href="{{ $href }}" class="text-xs font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="hidden lg:block">
            <x-button variant="secondary" href="{{ route('inquiries.create') }}">
                Confidential Inquiry
                <x-icon name="lock" class="h-3.5 w-3.5" />
            </x-button>
        </div>

        <button
            type="button"
            data-drawer-toggle="mobile-drawer"
            aria-label="Open menu"
            aria-expanded="false"
            aria-controls="mobile-drawer"
            class="flex h-10 w-10 shrink-0 items-center justify-center border border-gold text-gold lg:hidden"
        >
            <x-icon name="menu" class="h-5 w-5" />
        </button>
    </div>
</header>

<x-mobile-drawer />
