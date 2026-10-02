<x-account.shell title="Messages" active="messages">
    <script>document.body.classList.add('msg-page');</script>

    <div class="msg-layout" data-view="list">
        @include('messages._list', ['conversations' => $conversations, 'activeId' => null])

        <section class="msg-pane msg-pane-empty" aria-label="Conversation">
            @if ($conversations->isEmpty())
                <div class="msg-empty">
                    <span class="msg-empty-icon"><x-icon name="message-square" /></span>
                    <h2>Messaging opens when a connection is accepted</h2>
                    <p>Private messages are only available between members who have connected. Send a connection request from the directory; once it is accepted, your conversation appears here.</p>
                    @if (\Illuminate\Support\Facades\Route::has('network.index'))
                        <a href="{{ route('network.index') }}" class="ac-btn ac-btn-solid">Browse the network <x-icon name="arrow-right" class="ac-bi" /></a>
                    @endif
                </div>
            @else
                <div class="msg-empty">
                    <span class="msg-empty-icon"><x-icon name="message-square" /></span>
                    <h2>Select a conversation</h2>
                    <p>Choose a member on the left to read and reply. Messages are text only for now: attachments are not supported in this version.</p>
                </div>
            @endif
        </section>
    </div>

    <x-slot:scripts>
        <script>
            (function () {
                var fmt = new Intl.DateTimeFormat(undefined, { hour: '2-digit', minute: '2-digit' });
                var dfmt = new Intl.DateTimeFormat(undefined, { day: 'numeric', month: 'short' });
                document.querySelectorAll('[data-msg-list-time]').forEach(function (t) {
                    var d = new Date(t.getAttribute('datetime'));
                    if (isNaN(d)) return;
                    t.textContent = d.toDateString() === new Date().toDateString() ? fmt.format(d) : dfmt.format(d);
                });
            })();
        </script>
    </x-slot:scripts>
</x-account.shell>
