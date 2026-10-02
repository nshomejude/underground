@php
    $lastId = collect($thread)->max('id') ?? 0;
    $lastMine = collect($thread)->where('mine', true)->last();
    $reportTpl = route('messages.report', [$conversation, 987654321]);
    $hasBadge = \Illuminate\Support\Facades\View::exists('components.verified-badge');
    $profileUrl = ($profileSlug && \Illuminate\Support\Facades\Route::has('network.show')) ? route('network.show', $profileSlug) : null;
@endphp

<x-account.shell :title="$otherName" active="messages">
    <script>document.body.classList.add('msg-page', 'msg-thread-page');</script>

    <div class="msg-layout" data-view="thread" id="msg-root"
         data-poll-url="{{ route('messages.poll', $conversation) }}"
         data-last-id="{{ $lastId }}"
         data-seen-id="{{ $seenId }}"
         data-report-tpl="{{ $reportTpl }}">

        @include('messages._list', ['conversations' => $conversations, 'activeId' => $conversation->id])

        <section class="msg-pane msg-thread" aria-label="Conversation with {{ $otherName }}">
            <header class="msg-thread-head">
                <a href="{{ route('messages.index') }}" class="msg-back" aria-label="Back to all conversations"><x-icon name="arrow-left" /></a>
                <span class="msg-avatar msg-avatar-lg" aria-hidden="true">{{ \App\Services\ConversationService::initials($otherName) }}</span>
                <div class="msg-who">
                    <h2>
                        @if ($profileUrl)<a href="{{ $profileUrl }}">{{ $otherName }}</a>@else{{ $otherName }}@endif
                    </h2>
                    <p class="msg-who-meta">
                        @if ($tierName)<span class="msg-chip">{{ $tierName }}</span>@endif
                        @if ($hasBadge)<x-verified-badge :user="$other" />@endif
                    </p>
                </div>
                @if ($profileUrl)
                    <a href="{{ $profileUrl }}" class="msg-profile-link">View profile <x-icon name="chevron-right" /></a>
                @endif
            </header>

            @if (session('messaging_status'))
                <p class="msg-notice" role="status">{{ session('messaging_status') }}</p>
            @endif
            <p class="msg-notice" id="msg-notice" role="status" hidden></p>

            <div class="msg-scroll" id="msg-scroll" tabindex="-1">
                <p class="msg-intro">Private conversation with {{ $otherName }}. Text only: attachments are not supported in this version.</p>
                <ol class="msg-stream" id="msg-stream">
                    @php($prevDay = null)
                    @foreach ($thread as $m)
                        @if ($m['day'] !== $prevDay)
                            <li class="msg-day" aria-hidden="true"><span>{{ $m['day'] }}</span></li>
                            @php($prevDay = $m['day'])
                        @endif
                        <li class="msg {{ $m['mine'] ? 'is-mine' : 'is-theirs' }}" data-id="{{ $m['id'] }}" data-at="{{ $m['at'] }}" data-mine="{{ $m['mine'] ? 1 : 0 }}">
                            <div class="msg-bubble">
                                <p class="msg-text">{!! $m['html'] !!}</p>
                                <span class="msg-meta">
                                    <time datetime="{{ $m['at'] }}">{{ $m['time'] }}</time>
                                    @unless ($m['mine'])
                                        <button type="button" class="msg-flag" data-report="{{ route('messages.report', [$conversation, $m['id']]) }}" aria-label="Report this message" hidden><x-icon name="flag" /></button>
                                    @endunless
                                </span>
                            </div>
                        </li>
                        @if ($lastMine && $m['id'] === $lastMine['id'] && $seenId >= $m['id'])
                            <li class="msg-seen" id="msg-seen"><x-icon name="check-check" /> Seen</li>
                        @endif
                    @endforeach
                </ol>
                @if (empty($thread))
                    <p class="msg-hello" id="msg-hello">No messages yet. Introduce yourself and say what you would like to explore together.</p>
                @endif
            </div>

            <button type="button" class="msg-newpill" id="msg-newpill" hidden>New messages <x-icon name="chevron-down" /></button>

            <form class="msg-composer" id="composer" method="POST" action="{{ route('messages.store', $conversation) }}">
                @csrf
                <p class="msg-err" id="msg-err" role="alert" @if (! $errors->has('body')) hidden @endif>{{ $errors->first('body') }}</p>
                <div class="msg-composer-row">
                    <label for="msg-body" class="sr-only">Message to {{ $otherName }}</label>
                    <textarea id="msg-body" name="body" rows="1" maxlength="{{ \App\Services\MessageService::MAX_LENGTH }}" placeholder="Write a message" autocomplete="off" required>{{ old('body') }}</textarea>
                    <button type="submit" class="msg-send" id="msg-send" aria-label="Send message"><x-icon name="send" /><span>Send</span></button>
                </div>
                <p class="msg-hint"><span class="msg-hint-keys">Enter to send, Shift+Enter for a new line. </span>Text only, no attachments. <span class="msg-count" id="msg-count">0 / {{ \App\Services\MessageService::MAX_LENGTH }}</span></p>
            </form>
        </section>
    </div>

    <dialog class="msg-dialog" id="msg-report-dialog" aria-labelledby="msg-report-title">
        <form method="POST" action="" id="msg-report-form">
            @csrf
            <h3 id="msg-report-title">Report this message</h3>
            <p>Tell our team what is wrong. They review reports in confidence and may contact you.</p>
            <label for="msg-report-reason" class="sr-only">Reason</label>
            <textarea id="msg-report-reason" name="reason" rows="4" maxlength="500" placeholder="Optional: what happened? (up to 500 characters)"></textarea>
            <div class="msg-dialog-actions">
                <button type="button" class="ac-btn" id="msg-report-cancel">Cancel</button>
                <button type="submit" class="ac-btn ac-btn-solid">Send report</button>
            </div>
        </form>
    </dialog>

    <x-slot:scripts>
        <script>
            (function () {
                var root = document.getElementById('msg-root');
                var scroller = document.getElementById('msg-scroll');
                var stream = document.getElementById('msg-stream');
                var form = document.getElementById('composer');
                var box = document.getElementById('msg-body');
                var sendBtn = document.getElementById('msg-send');
                var errEl = document.getElementById('msg-err');
                var countEl = document.getElementById('msg-count');
                var pill = document.getElementById('msg-newpill');
                var notice = document.getElementById('msg-notice');
                var dialog = document.getElementById('msg-report-dialog');
                var reportForm = document.getElementById('msg-report-form');
                var MAX = {{ \App\Services\MessageService::MAX_LENGTH }};
                var pollUrl = root.dataset.pollUrl;
                var reportTpl = root.dataset.reportTpl;
                var lastId = parseInt(root.dataset.lastId, 10) || 0;
                var seenId = parseInt(root.dataset.seenId, 10) || 0;
                var lastActivity = Date.now();
                var polling = false;
                var timer = null;

                var timeFmt = new Intl.DateTimeFormat(undefined, { hour: '2-digit', minute: '2-digit' });
                var dayFmt = new Intl.DateTimeFormat(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

                function dayLabel(d) {
                    var now = new Date();
                    var y = new Date(); y.setDate(now.getDate() - 1);
                    if (d.toDateString() === now.toDateString()) return 'Today';
                    if (d.toDateString() === y.toDateString()) return 'Yesterday';
                    return dayFmt.format(d);
                }

                function relayout() {
                    stream.querySelectorAll('.msg-day').forEach(function (n) { n.remove(); });
                    var prev = null;
                    stream.querySelectorAll('.msg').forEach(function (li) {
                        var d = new Date(li.dataset.at);
                        var t = li.querySelector('time');
                        if (!isNaN(d)) { t.textContent = timeFmt.format(d); }
                        var key = isNaN(d) ? li.dataset.at : d.toDateString();
                        if (key !== prev) {
                            var sep = document.createElement('li');
                            sep.className = 'msg-day';
                            sep.setAttribute('aria-hidden', 'true');
                            sep.innerHTML = '<span></span>';
                            sep.firstChild.textContent = isNaN(d) ? '' : dayLabel(d);
                            li.parentNode.insertBefore(sep, li);
                            prev = key;
                        }
                    });
                    updateSeen();
                }

                function updateSeen() {
                    var old = document.getElementById('msg-seen');
                    if (old) old.remove();
                    var mine = stream.querySelectorAll('.msg.is-mine');
                    var last = mine[mine.length - 1];
                    if (last && seenId >= parseInt(last.dataset.id, 10)) {
                        var li = document.createElement('li');
                        li.className = 'msg-seen';
                        li.id = 'msg-seen';
                        li.innerHTML = '<svg class="lucide" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg> Seen';
                        last.insertAdjacentElement('afterend', li);
                    }
                }

                function atBottom() { return scroller.scrollHeight - scroller.scrollTop - scroller.clientHeight < 80; }
                function toBottom() { scroller.scrollTop = scroller.scrollHeight; pill.hidden = true; }

                function build(m) {
                    var li = document.createElement('li');
                    li.className = 'msg ' + (m.mine ? 'is-mine' : 'is-theirs');
                    li.dataset.id = m.id; li.dataset.at = m.at; li.dataset.mine = m.mine ? 1 : 0;
                    var flag = m.mine ? '' : '<button type="button" class="msg-flag" data-report="' + reportTpl.replace('987654321', m.id) + '" aria-label="Report this message"><svg class="lucide" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22V4a1 1 0 0 1 .4-.8A6 6 0 0 1 8 2c3 0 5 2 7.333 2q2 0 3.067-.8A1 1 0 0 1 20 4v10a1 1 0 0 1-.4.8A6 6 0 0 1 16 16c-3 0-5-2-8-2a6 6 0 0 0-4 1.528"/></svg></button>';
                    li.innerHTML = '<div class="msg-bubble"><p class="msg-text">' + m.html + '</p><span class="msg-meta"><time datetime="' + m.at + '"></time>' + flag + '</span></div>';
                    return li;
                }

                function append(list) {
                    var fresh = list.filter(function (m) { return !stream.querySelector('.msg[data-id="' + m.id + '"]'); });
                    if (!fresh.length) return false;
                    var stick = atBottom() || fresh.some(function (m) { return m.mine; });
                    fresh.forEach(function (m) { stream.appendChild(build(m)); lastId = Math.max(lastId, m.id); });
                    var hello = document.getElementById('msg-hello'); if (hello) hello.remove();
                    relayout();
                    if (stick) { toBottom(); } else { pill.hidden = false; }
                    lastActivity = Date.now();
                    return true;
                }

                function showError(text) {
                    errEl.textContent = text || '';
                    errEl.hidden = !text;
                }

                function updateCount() {
                    countEl.textContent = box.value.length + ' / ' + MAX;
                    countEl.classList.toggle('is-near', box.value.length > MAX - 100);
                    box.style.height = 'auto';
                    box.style.height = Math.min(box.scrollHeight, 140) + 'px';
                }

                /* ---- polling ---- */
                function nextDelay() { return Date.now() - lastActivity > 120000 ? 15000 : 5000; }
                function schedule() {
                    clearTimeout(timer);
                    if (document.hidden) return;
                    timer = setTimeout(tick, nextDelay());
                }
                function tick() {
                    if (polling || document.hidden) { schedule(); return; }
                    polling = true;
                    fetch(pollUrl + '?after=' + lastId, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                        .then(function (r) { return r.ok ? r.json() : null; })
                        .then(function (data) {
                            if (!data) return;
                            if (data.seen_id && data.seen_id !== seenId) { seenId = data.seen_id; updateSeen(); }
                            append(data.messages || []);
                        })
                        .catch(function () {})
                        .then(function () { polling = false; schedule(); });
                }
                document.addEventListener('visibilitychange', function () { if (!document.hidden) { lastActivity = Date.now(); tick(); } else { clearTimeout(timer); } });

                /* ---- sending ---- */
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var text = box.value.trim();
                    if (!text) { showError('Write a message before sending.'); return; }
                    if (text.length > MAX) { showError('Messages can be up to ' + MAX + ' characters.'); return; }
                    showError('');
                    sendBtn.disabled = true;
                    fetch(form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (r) { return r.json().then(function (j) { return { status: r.status, body: j }; }); })
                        .then(function (res) {
                            if (res.status === 201) {
                                box.value = ''; updateCount();
                                append([res.body.message]); toBottom();
                            } else if (res.status === 422) {
                                showError((res.body.errors && res.body.errors.body && res.body.errors.body[0]) || 'That message could not be sent.');
                            } else if (res.status === 429) {
                                showError('You are sending messages too quickly. Please wait a moment.');
                            } else {
                                showError('This conversation is no longer available.');
                            }
                        })
                        .catch(function () { showError('Could not send. Check your connection and try again.'); })
                        .then(function () { sendBtn.disabled = false; box.focus({ preventScroll: true }); });
                });

                var finePointer = window.matchMedia('(pointer: fine)').matches;
                if (!finePointer) { var keys = form.querySelector('.msg-hint-keys'); if (keys) keys.remove(); }
                box.addEventListener('keydown', function (e) {
                    if (finePointer && e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); form.requestSubmit(); }
                });
                box.addEventListener('input', updateCount);
                box.addEventListener('focus', function () { setTimeout(toBottom, 250); });

                /* ---- report ---- */
                stream.querySelectorAll('.msg-flag').forEach(function (b) { b.hidden = false; });
                stream.addEventListener('click', function (e) {
                    var b = e.target.closest('.msg-flag');
                    if (!b) return;
                    reportForm.action = b.dataset.report;
                    reportForm.reason.value = '';
                    dialog.showModal();
                });
                document.getElementById('msg-report-cancel').addEventListener('click', function () { dialog.close(); });
                reportForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    fetch(reportForm.action, { method: 'POST', body: new FormData(reportForm), credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (r) { return r.json(); })
                        .then(function (j) { notice.textContent = j.message || 'Report sent.'; notice.hidden = false; })
                        .catch(function () { notice.textContent = 'Could not send the report. Please try again.'; notice.hidden = false; })
                        .then(function () { dialog.close(); });
                });

                /* ---- layout ---- */
                scroller.addEventListener('scroll', function () { if (atBottom()) pill.hidden = true; }, { passive: true });
                pill.addEventListener('click', toBottom);

                // keep the composer above the on-screen keyboard
                if (window.visualViewport) {
                    var setVh = function () { document.documentElement.style.setProperty('--msg-vh', window.visualViewport.height + 'px'); if (document.activeElement === box) { toBottom(); } };
                    window.visualViewport.addEventListener('resize', setVh);
                    setVh();
                }

                relayout(); updateCount(); toBottom();
                if (location.hash === '#composer') { box.focus({ preventScroll: true }); }
                schedule();
            })();
        </script>
    </x-slot:scripts>
</x-account.shell>
