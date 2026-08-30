@php
    $navGroups = [
        'Company' => [
            'About' => route('about'),
            'Team' => route('team'),
            'Careers' => route('careers'),
            'Partners' => route('partners'),
            'Contact' => route('contact'),
        ],
        'What We Do' => [
            'Capabilities' => url('/').'#capabilities',
            'Expertise' => url('/').'#sectors',
            'Global Reach' => url('/').'#reach',
            'Portfolio' => route('portfolio'),
            'Projects' => route('projects'),
        ],
        'Resources' => [
            'Insights' => route('insights.index'),
            'Events' => route('events'),
            'Terms' => route('terms'),
            'Privacy' => route('privacy'),
        ],
    ];
@endphp

<footer class="border-t border-border bg-ink pb-20 lg:pb-0">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-[minmax(0,1.4fr)_repeat(3,minmax(0,1fr))] lg:gap-12">
            <div class="col-span-2 flex flex-col items-start gap-4 lg:col-span-1">
                <x-brand-mark />
                <p class="max-w-xs text-sm leading-relaxed text-body">
                    A global network delivering discreet, high-conviction execution across sectors and borders.
                </p>
            </div>

            @foreach ($navGroups as $group => $links)
                <nav aria-label="{{ $group }}">
                    <h2 class="text-[10px] font-semibold uppercase tracking-[0.25em] text-muted">{{ $group }}</h2>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($links as $label => $href)
                            <li>
                                <a href="{{ $href }}" class="text-sm text-body transition-colors hover:text-gold">{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endforeach
        </div>

        <div class="mt-12 flex flex-col items-center gap-2 border-t border-border pt-6 text-center sm:flex-row sm:justify-between sm:text-left">
            <p class="text-[11px] uppercase tracking-widest text-muted">
                &copy; {{ now()->year }} Underground Network Inc. All rights reserved.
            </p>
            <p class="text-[11px] uppercase tracking-widest text-muted">
                Powered by <a href="https://opesware.com" class="transition-colors hover:text-gold" rel="noopener">Opesware Technologies</a>
            </p>
        </div>
    </div>
</footer>
