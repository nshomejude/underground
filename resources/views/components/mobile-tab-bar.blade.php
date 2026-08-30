@php
    $tabs = [
        ['label' => 'Home', 'icon' => 'home', 'href' => url('/')],
        ['label' => 'Capabilities', 'icon' => 'landmark', 'href' => url('/').'#capabilities'],
        ['label' => 'Global Reach', 'icon' => 'globe', 'href' => url('/').'#reach'],
        ['label' => 'Insights', 'icon' => 'newspaper', 'href' => route('insights.index')],
        ['label' => 'Contact', 'icon' => 'mail', 'href' => route('inquiries.create')],
    ];

    $currentUrl = url()->current();
@endphp

<nav
    class="fixed inset-x-0 bottom-0 z-40 flex items-stretch border-t border-border bg-surface lg:hidden"
    aria-label="Primary"
>
    @foreach ($tabs as $tab)
        @php($isActive = $tab['href'] === $currentUrl)

        <a
            href="{{ $tab['href'] }}"
            class="flex flex-1 flex-col items-center justify-center gap-1 py-2.5 text-[10px] font-semibold uppercase tracking-wide transition-colors {{ $isActive ? 'text-gold' : 'text-muted hover:text-gold' }}"
        >
            <x-icon :name="$tab['icon']" class="h-5 w-5" />
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
