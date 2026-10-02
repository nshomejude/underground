{{-- Directory / match card. Expects $card = [profile, score, reasons, tier], $tierNames, $company; optional $showReasons. --}}
@php
    $p = $card['profile'];
    $name = $p->display_name ?: $p->user->name;
    $sectorNames = config('network.sectors');
    $roleNames = config('network.supply_chain_roles');
    $place = collect([$p->city, $p->country])->filter()->implode(', ');
    $sectors = collect((array) $p->sectors)->map(fn ($s) => $sectorNames[$s] ?? null)->filter()->values();
    $roles = collect((array) $p->supply_chain_roles)->map(fn ($r) => $roleNames[$r] ?? null)->filter()->values();
@endphp
<article class="nw-card">
    <a class="nw-card-link" href="{{ route('network.show', $p->slug) }}" aria-label="View {{ $name }}"></a>
    <header class="nw-card-head">
        @include('network.partials.avatar', ['profile' => $p])
        <div class="nw-card-id">
            <h3>{{ $name }}</h3>
            @if ($p->headline)<p class="nw-headline">{{ $p->headline }}</p>@endif
        </div>
        @include('network.partials.match', ['score' => $card['score'] ?? null])
    </header>

    <div class="nw-chips">
        @include('network.partials.tier-chip', ['tier' => $card['tier'] ?? null])
        @include('network.partials.badges', ['profile' => $p, 'companyVerified' => isset($company[$p->user_id])])
        @if ($p->open_to_collaboration)
            <span class="nw-badge nw-badge--open"><x-icon name="handshake" /> Open to collaboration</span>
        @endif
    </div>

    @if ($sectors->isNotEmpty())
        <ul class="nw-tags" aria-label="Sectors">
            @foreach ($sectors->take(3) as $s)<li>{{ $s }}</li>@endforeach
            @if ($sectors->count() > 3)<li class="nw-more">+{{ $sectors->count() - 3 }}</li>@endif
        </ul>
    @endif

    <dl class="nw-facts">
        @if ($roles->isNotEmpty())
            <div><dt>Role</dt><dd>{{ $roles->take(2)->implode(' · ') }}</dd></div>
        @endif
        @if ($place !== '')
            <div><dt>Based in</dt><dd><x-icon name="map-pin" /> {{ $place }}</dd></div>
        @endif
        @if ($p->organisation_name)
            <div><dt>Organisation</dt><dd>{{ $p->organisation_name }}</dd></div>
        @endif
    </dl>

    @if (! empty($showReasons) && ! empty($card['reasons']))
        <ul class="nw-reasons" aria-label="Why this match">
            @foreach (array_slice($card['reasons'], 0, 3) as $reason)
                <li><x-icon name="sparkles" /> {{ $reason }}</li>
            @endforeach
        </ul>
    @endif
</article>
