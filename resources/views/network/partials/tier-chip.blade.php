@if (! empty($tier))
    <span class="nw-tier nw-tier--{{ $tier }}">
        <x-icon name="{{ $tier === 'sovereign-partner' ? 'gem' : ($tier === 'principal-circle' ? 'star' : 'building-2') }}" />
        {{ $tierNames[$tier] ?? \Illuminate\Support\Str::headline($tier) }}
    </span>
@endif
