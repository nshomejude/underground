{{-- Gentle prompt when the viewer is not yet listed. Expects $viewer = [profile, verified, listed, hidden]. --}}
@php($has = fn (string $r) => \Illuminate\Support\Facades\Route::has($r))
@if (! $viewer['listed'])
    <section class="ac-panel nw-prompt" aria-labelledby="nw-prompt-h">
        <x-icon name="shield-check" class="nw-prompt-icon" />
        <div>
            <h2 id="nw-prompt-h">You are not yet visible to other members</h2>
            <p>
                @if ($viewer['hidden'])
                    Your profile is hidden from the network, so you cannot be found and cannot send requests until you make it visible again.
                @else
                    You can browse the network now. To appear in the directory and send connection requests, complete these steps:
                @endif
            </p>
            <ol class="nw-steps">
                <li class="{{ $viewer['profile']?->display_name ? 'is-done' : '' }}">
                    Complete your profile
                    @if ($has('account.profile'))<a href="{{ route('account.profile') }}">Open profile</a>@endif
                </li>
                <li class="{{ $viewer['verified'] ? 'is-done' : '' }}">
                    Verify your identity
                    @if ($has('verification.index'))<a href="{{ route('verification.index') }}">Start verification</a>@endif
                </li>
                <li class="{{ ! $viewer['hidden'] && $viewer['profile'] ? 'is-done' : '' }}">Keep your profile visible to members</li>
            </ol>
        </div>
    </section>
@endif
