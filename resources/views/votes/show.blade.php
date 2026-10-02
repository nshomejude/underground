@php
    $quorum = max(1, (int) $motion->quorum);
    $pct = min(100, (int) round($motion->votes_count / $quorum * 100));
    $met = $motion->votes_count >= $quorum;
    $subs = ['For' => 'Vote in favour of the motion', 'Against' => 'Vote against the motion', 'Abstain' => 'Take part without taking a side'];
@endphp

<x-account.shell :title="$motion->title" active="votes">
    <div class="vt">
        <a href="{{ route('votes.index') }}" class="vt-back"><x-icon name="arrow-left" class="h-4 w-4" />All votes</a>

        @if (session('status'))
            <div class="vt-alert" role="status"><x-icon name="check-circle" /><span>{{ session('status') }}</span></div>
        @endif
        @if ($errors->any())
            <div class="vt-alert vt-alert-error" role="alert"><x-icon name="triangle-alert" /><span>{{ $errors->first() }}</span></div>
        @endif
        <p id="vt-live-region" class="vt-sr" aria-live="polite"></p>

        <div class="vt-show">
            <div>
                <section class="vt-panel">
                    <div class="vt-chips">
                        <span class="vt-chip vt-chip-gold">{{ $motion->kindLabel() }}</span>
                        <span class="vt-chip"><x-icon name="users" />{{ $motion->tierLabel() }}</span>
                        @if ($motion->anonymous)
                            <span class="vt-chip"><x-icon name="eye" />Anonymous vote</span>
                        @endif
                        @if ($motion->isDraft())
                            <span class="vt-chip"><x-icon name="file-text" />Draft</span>
                        @elseif ($motion->isCancelled())
                            <span class="vt-chip"><x-icon name="ban" />Cancelled</span>
                        @endif
                    </div>
                    <h1 class="vt-title" style="margin-top: 14px">{{ $motion->title }}</h1>
                    @if ($motion->summary)
                        <p class="vt-summary-text">{{ $motion->summary }}</p>
                    @endif

                    <dl class="vt-meta">
                        <div><dt>Opened by</dt><dd>{{ $motion->creator?->name ?? 'Member' }}</dd></div>
                        <div><dt>Opens</dt><dd>{{ $motion->opens_at?->format('j M Y, H:i') ?? 'On publishing' }}</dd></div>
                        <div>
                            <dt>Closes</dt>
                            <dd>{{ $motion->closes_at?->format('j M Y, H:i') }}</dd>
                        </div>
                        @if ($motion->isOpenNow())
                            <div>
                                <dt>Time left</dt>
                                <dd><time datetime="{{ $motion->closes_at->toIso8601String() }}" data-vt-countdown="{{ $motion->closes_at->toIso8601String() }}" data-vt-reload>{{ $motion->closes_at->diffForHumans(['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }} left</time></dd>
                            </div>
                        @endif
                    </dl>

                    <p class="vt-rule"><strong>How it is decided.</strong> {{ $rules }}
                        {{ $motion->allow_change ? 'You may change your vote until voting closes.' : 'Votes are final once cast.' }}
                        {{ $motion->anonymous ? 'This vote is anonymous: nobody can see who voted for what.' : 'Votes are visible to staff administrators and the person who opened the motion.' }}
                    </p>
                </section>

                @if ($body !== [])
                    <section class="vt-panel" aria-labelledby="vt-body-h">
                        <h2 id="vt-body-h">The motion</h2>
                        <div class="vt-prose">
                            @foreach ($body as $block)
                                @if ($block['type'] === 'heading')
                                    <h3>{{ $block['text'] }}</h3>
                                @elseif ($block['type'] === 'p')
                                    <p>{{ $block['text'] }}</p>
                                @else
                                    <{{ $block['type'] }}>
                                        @foreach ($block['items'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </{{ $block['type'] }}>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($voters->isNotEmpty())
                    <section class="vt-panel" aria-labelledby="vt-voters-h">
                        <h2 id="vt-voters-h">Who voted</h2>
                        <div class="vt-tablewrap">
                            <table class="vt-table">
                                <thead><tr><th scope="col">Member</th><th scope="col">Choice</th><th scope="col">Comment</th></tr></thead>
                                <tbody>
                                    @foreach ($voters as $v)
                                        <tr><td>{{ $v->user?->name }}</td><td>{{ $v->choice }}</td><td>{{ $v->comment }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            </div>

            <aside class="vt-aside">
                @if ($tally !== null)
                    @include('votes._result', ['tally' => $tally, 'final' => $motion->isDecided()])
                @endif

                @if ($motion->isCancelled())
                    <section class="vt-panel">
                        <h2>Cancelled</h2>
                        <p>This motion was withdrawn{{ $motion->cancel_reason ? ': '.$motion->cancel_reason : '.' }} No result was recorded.</p>
                    </section>
                @elseif ($motion->isDraft())
                    <section class="vt-panel">
                        <h2>Draft</h2>
                        <p>Only you and staff administrators can see this draft. Publish it when the wording is final.</p>
                    </section>
                @elseif ($motion->isScheduled())
                    <section class="vt-panel">
                        <h2>Not open yet</h2>
                        <p>Voting opens on {{ $motion->opens_at->format('j F Y \a\t H:i') }}.</p>
                    </section>
                @elseif ($motion->isOpenNow())
                    <section class="vt-panel" aria-labelledby="vt-cast-h">
                        <div class="vt-meter" style="margin-bottom: 18px">
                            <div class="vt-meter-bar" role="img" aria-label="{{ $motion->votes_count }} of {{ $quorum }} voters needed for quorum">
                                <div class="vt-meter-fill {{ $met ? 'is-met' : '' }}" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="vt-meter-text">
                                <span>{{ $motion->votes_count }} {{ \Illuminate\Support\Str::plural('member', $motion->votes_count) }} voted</span>
                                <span>{{ $met ? 'Quorum met' : 'Quorum: '.$quorum }}</span>
                            </div>
                        </div>

                        @if ($canVote && $myVote && ! $motion->allow_change)
                            <h2 id="vt-cast-h">Your vote is recorded</h2>
                            <p class="vt-status vt-status-ok"><x-icon name="check-circle" />Voted: {{ $myVote->choice }}</p>
                            <p>This motion does not allow votes to be changed.</p>
                        @elseif ($canVote)
                            <form method="POST" action="{{ route('votes.cast', $motion) }}" id="vt-vote-form" novalidate>
                                @csrf
                                <fieldset class="vt-fieldset" role="radiogroup" aria-labelledby="vt-cast-h">
                                    <legend class="vt-legend" id="vt-cast-h">{{ $myVote ? 'Change my vote' : 'Cast your vote' }}</legend>
                                    @if ($myVote)
                                        <p class="vt-status vt-status-ok" style="margin-bottom: 12px"><x-icon name="check-circle" />Voted: {{ $myVote->choice }}</p>
                                    @else
                                        <p class="vt-status vt-status-todo" style="margin-bottom: 12px"><x-icon name="triangle-alert" />Not voted yet</p>
                                    @endif
                                    <div class="vt-radios">
                                        @foreach ($motion->choiceList() as $i => $choice)
                                            <label class="vt-radio">
                                                <input type="radio" name="choice" value="{{ $choice }}" @checked(old('choice', $myVote?->choice) === $choice) required>
                                                <span class="vt-radio-body">
                                                    <span class="vt-radio-mark" aria-hidden="true"><x-icon name="check" /></span>
                                                    <span class="vt-radio-text">
                                                        <span class="vt-radio-label">{{ $choice }}</span>
                                                        @if ($motion->isDecision())
                                                            <span class="vt-radio-sub">{{ $subs[$choice] ?? '' }}</span>
                                                        @endif
                                                    </span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>

                                @if (! $motion->anonymous)
                                    <div class="vt-field" style="margin-top: 18px">
                                        <label class="vt-label" for="vt-comment">Comment <span class="vt-hint">(optional, up to 500 characters)</span></label>
                                        <textarea class="vt-textarea" id="vt-comment" name="comment" maxlength="500" rows="3">{{ old('comment', $myVote?->comment) }}</textarea>
                                    </div>
                                @endif

                                <div class="vt-actions" style="margin-top: 18px">
                                    <button type="submit" class="vt-btn vt-btn-solid" id="vt-submit"><x-icon name="check" />{{ $myVote ? 'Change my vote' : 'Cast my vote' }}</button>
                                </div>
                                <p class="vt-hint" style="margin-top: 10px">You will be asked to confirm before your vote is recorded.</p>
                            </form>

                            <dialog class="vt-dialog" id="vt-confirm" aria-labelledby="vt-confirm-h">
                                <h2 id="vt-confirm-h">Confirm your vote</h2>
                                <p>You are voting <strong style="color: var(--vt-cream)" id="vt-confirm-choice"></strong> on "{{ $motion->title }}".
                                    {{ $motion->allow_change ? 'You can change it until voting closes.' : 'This cannot be changed afterwards.' }}</p>
                                <div class="vt-actions">
                                    <button type="button" class="vt-btn vt-btn-solid" id="vt-confirm-yes">Confirm vote</button>
                                    <button type="button" class="vt-btn" id="vt-confirm-no">Go back</button>
                                </div>
                            </dialog>
                            <script>
                                (function () {
                                    var form = document.getElementById('vt-vote-form');
                                    var dlg = document.getElementById('vt-confirm');
                                    if (!form || !dlg || typeof dlg.showModal !== 'function') { return; }
                                    form.addEventListener('submit', function (e) {
                                        if (form.dataset.confirmed === '1') { return; }
                                        var picked = form.querySelector('input[name=choice]:checked');
                                        if (!picked) { return; }
                                        e.preventDefault();
                                        document.getElementById('vt-confirm-choice').textContent = picked.value;
                                        dlg.showModal();
                                    });
                                    document.getElementById('vt-confirm-yes').addEventListener('click', function () {
                                        form.dataset.confirmed = '1'; dlg.close(); form.submit();
                                    });
                                    document.getElementById('vt-confirm-no').addEventListener('click', function () { dlg.close(); });
                                })();
                            </script>
                        @else
                            <h2 id="vt-cast-h">Voting is open</h2>
                            <p>You are viewing this motion as its owner or an administrator. Voting is by eligible members in their dashboard.</p>
                        @endif
                    </section>
                @elseif (! $motion->isDecided())
                    <section class="vt-panel"><h2>Voting closed</h2><p>Voting on this motion has closed.</p></section>
                @endif

                @if ($tally === null && $motion->isOpenNow())
                    <section class="vt-panel">
                        <div class="vt-sealed">
                            <x-icon name="lock" />
                            <p>Results are sealed until voting closes, so early totals cannot sway the outcome.</p>
                        </div>
                    </section>
                @endif

                @if ($canEdit || $canCancel)
                    <section class="vt-panel">
                        <h2>Manage</h2>
                        <div class="vt-actions">
                            @if ($canEdit)
                                <a href="{{ route('votes.edit', $motion) }}" class="vt-btn">Edit motion</a>
                            @endif
                            @if ($motion->isDraft() && $isCreator)
                                <form method="POST" action="{{ route('votes.publish', $motion) }}">@csrf<button type="submit" class="vt-btn vt-btn-solid"><x-icon name="send" />Publish</button></form>
                            @endif
                        </div>
                        @if ($canCancel)
                            <details class="vt-more">
                                <summary>Cancel this motion</summary>
                                <form method="POST" action="{{ route('votes.cancel', $motion) }}" class="vt-field" style="margin-top: 8px">
                                    @csrf
                                    <label class="vt-label" for="vt-reason">Reason (optional)</label>
                                    <input class="vt-input" id="vt-reason" name="reason" maxlength="300">
                                    <button type="submit" class="vt-btn vt-btn-danger"><x-icon name="ban" />Cancel motion</button>
                                </form>
                            </details>
                        @endif
                        <p class="vt-hint" style="margin-top: 12px">Once anyone has voted a motion can no longer be edited, and only a staff administrator can cancel it.</p>
                    </section>
                @endif
            </aside>
        </div>
    </div>
    @include('votes._scripts')
</x-account.shell>
