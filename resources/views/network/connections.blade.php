<x-account.shell title="Connections" active="network">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Member Network</p>
            <h1>My connections</h1>
            <p class="ac-sub">Your network, pending requests and blocked members.</p>
        </div>
        <nav class="nw-top-links" aria-label="Network sections">
            <a class="ac-btn" href="{{ route('network.index') }}"><x-icon name="users" class="ac-bi" /> Directory</a>
        </nav>
    </header>

    @if (session('status'))
        <p class="ac-flash" role="status">{{ session('status') }}</p>
    @endif
    @if (session('network_error'))
        <p class="ac-flash ac-flash-warn" role="alert">{{ session('network_error') }}</p>
    @endif

    @php
        $tabs = [
            'connections' => ['Connected', $accepted],
            'received' => ['Received requests', $received],
            'sent' => ['Sent requests', $sent],
            'blocked' => ['Blocked', $blocked],
        ];
        $rows = $tabs[$tab][1];
        $empty = [
            'connections' => ['No connections yet', 'Browse the directory and send a request to start building your network.'],
            'received' => ['No requests waiting', 'When a member asks to connect or collaborate, it will appear here.'],
            'sent' => ['No sent requests', 'Requests you send, and any that were declined, are listed here.'],
            'blocked' => ['No blocked members', 'Members you block will be listed here.'],
        ][$tab];
    @endphp

    <nav class="nw-cx-tabs" aria-label="Connection lists">
        @foreach ($tabs as $key => [$label, $list])
            <a href="{{ route('network.connections', ['tab' => $key]) }}" class="nw-cx-tab {{ $tab === $key ? 'is-active' : '' }}" @if ($tab === $key) aria-current="page" @endif>
                {{ $label }} <span class="nw-count-pill">{{ $list->count() }}</span>
            </a>
        @endforeach
    </nav>

    @if ($rows->isEmpty())
        <div class="ac-panel nw-empty">
            <x-icon name="users" />
            <h2>{{ $empty[0] }}</h2>
            <p>{{ $empty[1] }}</p>
            <a href="{{ route('network.index') }}" class="ac-btn">Browse the directory</a>
        </div>
    @else
        <ul class="nw-cx-list">
            @foreach ($rows as $c)
                @php
                    $other = $c->otherParty($viewer);
                    $profile = $other->profile;
                    $name = $profile?->display_name ?: $other->name;
                    $tier = $tiers[$other->id] ?? null;
                @endphp
                <li class="ac-panel nw-cx-row">
                    <div class="nw-card-id">
                        @if ($profile)
                            @include('network.partials.avatar', ['profile' => $profile, 'size' => 'nw-avatar--sm'])
                        @endif
                        <div>
                            <strong>
                                @if ($profile && $tab !== 'blocked')
                                    <a href="{{ route('network.show', $profile->slug) }}">{{ $name }}</a>
                                @else
                                    {{ $name }}
                                @endif
                            </strong>
                            @if ($profile?->headline)
                                <p class="nw-muted">{{ $profile->headline }}</p>
                            @endif
                            <div class="nw-chips">
                                @include('network.partials.tier-chip', ['tier' => $tier])
                                @if ($c->isCollaboration())
                                    <span class="nw-badge nw-badge--co">Collaboration</span>
                                @endif
                                @if ($tab === 'sent')
                                    <span class="nw-badge">{{ $c->status === 'declined' ? 'Declined' : 'Pending' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($c->topic)
                        <p class="nw-muted"><strong>Topic:</strong> {{ $c->topic }}</p>
                    @endif
                    @if ($c->message && in_array($tab, ['received', 'sent'], true))
                        <p class="nw-prose">{{ $c->message }}</p>
                    @endif

                    <div class="nw-actions">
                        @if ($tab === 'connections')
                            @if ($c->conversation && \Illuminate\Support\Facades\Route::has('messages.show'))
                                <a class="ac-btn ac-btn-solid" href="{{ route('messages.show', $c->conversation) }}"><x-icon name="message-square" class="ac-bi" /> Message</a>
                            @endif
                            <form method="POST" action="{{ route('network.remove', $c) }}" onsubmit="return confirm('Remove this connection?')">
                                @csrf
                                <button type="submit" class="ac-btn">Remove</button>
                            </form>
                        @elseif ($tab === 'received')
                            <form method="POST" action="{{ route('network.respond', $c) }}">
                                @csrf
                                <input type="hidden" name="action" value="accept">
                                <button type="submit" class="ac-btn ac-btn-solid">Accept</button>
                            </form>
                            <form method="POST" action="{{ route('network.respond', $c) }}">
                                @csrf
                                <input type="hidden" name="action" value="decline">
                                <button type="submit" class="ac-btn">Decline</button>
                            </form>
                        @elseif ($tab === 'sent')
                            @if ($c->status === 'pending')
                                <form method="POST" action="{{ route('network.withdraw', $c) }}">
                                    @csrf
                                    <button type="submit" class="ac-btn">Withdraw</button>
                                </form>
                            @endif
                        @elseif ($tab === 'blocked' && $profile)
                            <form method="POST" action="{{ route('network.unblock', $profile->slug) }}">
                                @csrf
                                <button type="submit" class="ac-btn">Unblock</button>
                            </form>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</x-account.shell>
