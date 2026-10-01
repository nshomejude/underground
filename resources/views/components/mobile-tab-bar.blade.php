@php
    $tabs = [
        ['label' => 'Home', 'icon' => 'home', 'href' => url('/')],
        ['label' => 'Capabilities', 'icon' => 'landmark', 'href' => route('capabilities.index')],
        ['label' => 'Reach', 'icon' => 'globe', 'href' => route('global-reach')],
        ['label' => 'Insights', 'icon' => 'newspaper', 'href' => route('insights.index')],
        ['label' => 'Contact', 'icon' => 'mail', 'href' => route('inquiries.create')],
    ];

    $currentUrl = url()->current();
@endphp

<nav
    class="fixed inset-x-0 bottom-0 z-40 flex items-stretch border-t border-border bg-surface pb-[env(safe-area-inset-bottom)] lg:hidden"
    aria-label="Primary"
>
    @foreach ($tabs as $tab)
        @php($isActive = ! str_contains($tab['href'], '#') && rtrim($tab['href'], '/') === rtrim($currentUrl, '/'))

        <a
            href="{{ $tab['href'] }}"
            class="flex min-w-0 flex-1 flex-col items-center justify-center gap-1 py-2.5 whitespace-nowrap text-[10px] font-semibold uppercase tracking-wide transition-colors {{ $isActive ? 'text-gold' : 'text-muted hover:text-gold' }}"
        >
            <x-icon :name="$tab['icon']" class="h-5 w-5" />
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
