@props(['user', 'size' => 'sm'])

@php
    $identity = $user && $user->isIdentityVerified();
    $company = $user && $user->isCompanyVerified();
    $cls = 'vf-badge'.($size === 'md' ? ' vf-badge--md' : '');
@endphp

@if ($identity || $company)
    <span class="vf-badges">
        @if ($identity)
            <span class="{{ $cls }}" title="Identity verified by manual document review">
                <x-icon name="shield-check" />
                Identity verified
            </span>
        @endif
        @if ($company)
            <span class="{{ $cls }}" title="Company documents verified by manual review">
                <x-icon name="badge-check" />
                Verified company
            </span>
        @endif
    </span>
@endif
