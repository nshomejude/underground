@props(['profile', 'compact' => false])

@php
    $p = app(\App\Services\ProfileService::class)->publicPayload($profile);
    $place = collect([$p['city'], $p['country']])->filter()->implode(', ');
    $shownSectors = array_slice($p['sector_labels'], 0, $compact ? 2 : 4);
    $moreSectors = count($p['sector_labels']) - count($shownSectors);
@endphp

<article {{ $attributes->class(['pc', 'pc-compact' => $compact]) }}>
    <div class="pc-head">
        @if ($p['avatar_url'])
            <img class="pc-avatar" src="{{ $p['avatar_url'] }}" alt="" width="64" height="64" loading="lazy">
        @else
            <span class="pc-avatar pc-initials" aria-hidden="true">{{ $p['initials'] }}</span>
        @endif
        <div class="pc-who">
            <h3 class="pc-name">{{ $p['name'] }}</h3>
            @if ($p['headline'])
                <p class="pc-headline">{{ $p['headline'] }}</p>
            @endif
        </div>
    </div>

    <div class="pc-badges">
        @if ($p['tier_name'])
            <span class="pc-tier"><x-icon name="gem" />{{ $p['tier_name'] }}</span>
        @endif
        @if (\Illuminate\Support\Facades\View::exists('components.verified-badge'))
            <x-dynamic-component component="verified-badge" :user="$profile->user" />
        @endif
        @if ($p['open_to_collaboration'])
            <span class="pc-open"><x-icon name="handshake" />Open to collaborate</span>
        @endif
    </div>

    @if ($place)
        <p class="pc-place"><x-icon name="map-pin" /><span>{{ $place }}</span></p>
    @endif

    @if ($shownSectors)
        <ul class="pc-chips" aria-label="Sectors">
            @foreach ($shownSectors as $label)
                <li>{{ $label }}</li>
            @endforeach
            @if ($moreSectors > 0)
                <li class="pc-more">+{{ $moreSectors }} more</li>
            @endif
        </ul>
    @endif

    @unless ($compact)
        @if ($p['bio'])
            <p class="pc-bio">{{ \Illuminate\Support\Str::limit($p['bio'], 220) }}</p>
        @endif
    @endunless
</article>
