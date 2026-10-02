@php
    $siteSetting = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current();

    $navGroups = [
        'Company' => [
            'About' => route('about'),
            'Team' => route('team'),
            'Careers' => route('careers'),
            'Partners' => route('partners'),
            'Contact' => route('contact'),
        ],
        'What We Do' => [
            'Capabilities' => route('capabilities.index'),
            'Expertise' => route('sectors.index'),
            'Global Reach' => route('global-reach'),
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

<footer class="border-t border-border bg-ink pb-[calc(5rem+env(safe-area-inset-bottom))] lg:pb-0">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-4 lg:gap-12">
            <div class="col-span-2 flex flex-col items-start gap-4 lg:col-span-1">
                <x-seal :size="84" />
                <x-brand-mark />
                <p class="max-w-xs text-sm leading-relaxed text-body">
                    A global network delivering discreet, high-conviction execution across sectors and borders.
                </p>
                <ul class="flex flex-col gap-1.5 text-sm text-body">
                    <li><a href="mailto:info@un-der.com" class="transition-colors hover:text-gold">info@un-der.com</a></li>
                    <li><a href="tel:+15715089170" class="transition-colors hover:text-gold">+1-571-508-9170</a></li>
                    <li class="text-muted">Washington, DC &middot; Douala &middot; Abidjan &middot; Lagos &middot; Paris</li>
                </ul>
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

        @if (! empty($siteSetting->socialLinks))
            <nav aria-label="Social" class="mt-10 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 border-t border-border pt-8 sm:justify-start">
                @foreach ($siteSetting->socialLinks as $social)
                    <a href="{{ $social['url'] }}" class="text-xs font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold" rel="noopener" target="_blank">
                        {{ $social['label'] }}
                    </a>
                @endforeach
            </nav>
        @endif

        <div class="mt-12 flex flex-col items-center gap-2 border-t border-border pt-6 text-center sm:flex-row sm:justify-between sm:text-left">
            <p class="text-[11px] uppercase tracking-widest text-muted">
                &copy; {{ now()->year }} {{ $siteSetting->siteName }} Inc. All rights reserved.
            </p>
            <div class="flex flex-col items-center gap-1 sm:items-end">
                @if ($siteSetting->footerNote)
                    <p class="text-[11px] uppercase tracking-widest text-muted">{{ $siteSetting->footerNote }}</p>
                @endif
                <p class="text-[11px] uppercase tracking-widest text-muted">
                    Powered by <a href="https://opesware.com" class="text-body transition-colors hover:text-gold" rel="noopener" target="_blank">opesware.com</a>
                </p>
            </div>
        </div>
    </div>
</footer>
