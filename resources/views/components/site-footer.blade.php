@php
    $navLinks = [
        'About' => route('about'),
        'Capabilities' => '#',
        'Expertise' => '#',
        'Global Reach' => '#',
        'Insights' => '#',
        'Careers' => '#',
        'Contact' => route('contact'),
        'Team' => route('team'),
        'Partners' => route('partners'),
        'Portfolio' => route('portfolio'),
        'Projects' => route('projects'),
        'Events' => route('events'),
        'Terms' => route('terms'),
        'Privacy' => route('privacy'),
    ];
@endphp

<footer class="border-t border-border bg-ink">
    <div class="mx-auto flex max-w-7xl flex-col items-center gap-6 px-4 py-10 text-center sm:px-6 lg:flex-row lg:justify-between lg:px-8 lg:text-left">
        <x-brand-mark />

        <nav class="flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-xs font-semibold uppercase tracking-widest text-body" aria-label="Footer">
            @foreach ($navLinks as $label => $href)
                <a href="{{ $href }}" class="transition-colors hover:text-gold">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex flex-col items-center gap-1 lg:items-end">
            <p class="text-xs uppercase tracking-widest text-muted">
                &copy; {{ now()->year }} Underground Network Inc. All rights reserved.
            </p>
            <p class="text-xs uppercase tracking-widest text-muted">
                Powered by <a href="https://opesware.com" class="transition-colors hover:text-gold" rel="noopener">Opesware Technologies</a>
            </p>
        </div>
    </div>
</footer>
