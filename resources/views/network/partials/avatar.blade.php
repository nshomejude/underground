@php
    $name = $profile->display_name ?: $profile->user->name;
    $initials = collect(preg_split('/\s+/', trim($name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: 'M';
    $src = null;
    if (method_exists($profile, 'avatarUrl')) {
        $src = $profile->avatarUrl();
    } elseif ($profile->avatar_path) {
        $src = str_starts_with($profile->avatar_path, 'http') ? $profile->avatar_path : \Illuminate\Support\Facades\Storage::disk('public')->url($profile->avatar_path);
    }
@endphp
<span class="nw-avatar {{ $size ?? '' }}" aria-hidden="true">
    @if ($src)
        <img src="{{ $src }}" alt="" loading="lazy" width="96" height="96">
    @else
        {{ $initials }}
    @endif
</span>
