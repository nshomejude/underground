@php
    $navLinks = [
        'About' => '#',
        'Capabilities' => '#',
        'Expertise' => '#',
        'Global Reach' => '#',
        'Insights' => '#',
        'Careers' => '#',
        'Contact' => '#',
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
        </nav>

        <x-button variant="primary" href="#" class="mt-auto">
            Confidential Inquiry
            <x-icon name="lock" class="h-3.5 w-3.5" />
        </x-button>
    </div>
</div>
