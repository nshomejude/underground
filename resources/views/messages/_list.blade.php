

<aside class="msg-list" aria-label="Conversations">
    <header class="msg-list-head">
        <h1>Messages</h1>
        <p>Private, between connected members.</p>
    </header>

    @if ($conversations->isEmpty())
        <div class="msg-empty-list">
            <x-icon name="message-square" />
            <p>No conversations yet.</p>
        </div>
    @else
        <ul class="msg-items">
            @foreach ($conversations as $c)
                @php
                    $name = \App\Services\ConversationService::nameOf($c->other);
                    $last = $c->lastMessage;
                @endphp
                <li>
                    <a href="{{ route('messages.show', $c) }}" class="msg-item @if ($activeId === $c->id) is-active @endif" @if ($activeId === $c->id) aria-current="page" @endif>
                        <span class="msg-avatar" aria-hidden="true">{{ \App\Services\ConversationService::initials($name) }}</span>
                        <span class="msg-item-body">
                            <span class="msg-item-top">
                                <b class="msg-item-name">{{ $name }}</b>
                                @if ($last)
                                    <time class="msg-item-time" datetime="{{ $last->created_at->toIso8601String() }}" data-msg-list-time>{{ $last->created_at->isToday() ? $last->created_at->format('H:i') : $last->created_at->format('j M') }}</time>
                                @endif
                            </span>
                            <span class="msg-item-bottom">
                                <span class="msg-item-preview @if ($c->unread) is-unread @endif">
                                    @if ($last)
                                        @if ($last->sender_id === auth()->id())<i>You:</i> @endif{{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $last->body), 70) }}
                                    @else
                                        Say hello. No messages yet.
                                    @endif
                                </span>
                                @if ($c->unread)
                                    <span class="msg-badge" aria-label="{{ $c->unread }} unread">{{ $c->unread > 99 ? '99+' : $c->unread }}</span>
                                @endif
                            </span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</aside>
