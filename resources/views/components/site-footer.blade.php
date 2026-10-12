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
            'Case Studies' => route('team.case-studies'),
            'Projects' => route('projects'),
        ],
        'Resources' => [
            'Reports Hub' => route('reports-hub.index'),
            'Flex Media Group' => route('flex-media-group'),
            'Membership' => route('membership.index'),
            'Events' => route('events'),
            'Consultation Desk' => route('consultations.create'),
        ],
    ];

    // Membership Terms is a later-lot page; until membership.terms exists,
    // point at the general Terms page rather than invent one.
    $membershipTermsHref = \App\Support\OptionalRoute::url('membership.terms', [], route('terms'));
@endphp

<footer class="border-t border-border bg-ink pb-[calc(5rem+env(safe-area-inset-bottom))] lg:pb-0">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-4 lg:gap-12">
            <div class="col-span-2 flex flex-col items-start gap-4 lg:col-span-1">
                <x-brand-mark :lockup="true" class="!h-auto !w-full max-w-[15rem]" />
                <p class="max-w-xs text-sm leading-relaxed text-body">
                    A global network delivering discreet, high-conviction execution across sectors and borders.
                </p>
                <ul class="flex flex-col gap-1.5 text-sm text-body">
                    <li><a href="mailto:info@un-der.com" class="transition-colors hover:text-gold">info@un-der.com</a></li>
                    <li><a href="tel:+15715089170" class="transition-colors hover:text-gold">+1-571-508-9170</a></li>
                    <li class="text-muted">200 Massachusetts Ave NW, Washington, DC 20001 &middot; Douala &middot; Abidjan &middot; Lagos &middot; Paris</li>
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

        <div class="mt-10 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 border-t border-border pt-8 text-xs font-semibold uppercase tracking-widest sm:justify-start">
            <a href="{{ route('privacy') }}" class="text-body transition-colors hover:text-gold">Privacy</a>
            <a href="{{ route('terms') }}" class="text-body transition-colors hover:text-gold">Terms</a>
            <a href="{{ $membershipTermsHref }}" class="text-body transition-colors hover:text-gold">Membership Terms</a>
            <a href="mailto:info@un-der.com" class="text-body transition-colors hover:text-gold">Support</a>
        </div>

        <div class="mt-12 flex flex-col items-center gap-2 border-t border-border pt-6 text-center sm:flex-row sm:justify-between sm:text-left">
            <p class="flex items-center gap-3 text-[11px] uppercase tracking-widest text-muted">
                <x-seal :size="24" alt="" />
                &copy; {{ now()->year }} Underground USA, LLC. All rights reserved.
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
