@if (\Illuminate\Support\Facades\View::exists('components.verified-badge'))
    <x-verified-badge :user="$profile->user" />
@else
    <span class="nw-badge"><x-icon name="badge-check" /> Identity verified</span>
    @if (! empty($companyVerified))
        <span class="nw-badge nw-badge--co"><x-icon name="building-2" /> Verified company</span>
    @endif
@endif
