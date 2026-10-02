<x-admin.shell title="Motion" max-width="max-w-5xl">
    <div class="vt">
        <a href="{{ route('admin.motions.index') }}" class="vt-back"><x-icon name="arrow-left" class="h-4 w-4" />All motions</a>

        @if (session('status'))
            <div class="vt-alert" role="status"><x-icon name="check-circle" /><span>{{ session('status') }}</span></div>
        @endif
        @if ($errors->any())
            <div class="vt-alert vt-alert-error" role="alert"><x-icon name="triangle-alert" /><span>{{ $errors->first() }}</span></div>
        @endif

        <section class="vt-panel">
            <div class="vt-chips">
                <span class="vt-chip vt-chip-gold">{{ $motion->kindLabel() }}</span>
                <span class="vt-chip">{{ ucfirst($motion->status) }}</span>
                <span class="vt-chip">{{ $motion->tierLabel() }}</span>
                @if ($motion->anonymous)<span class="vt-chip">Anonymous vote</span>@endif
            </div>
            <h2 class="vt-title">{{ $motion->title }}</h2>
            @if ($motion->summary)<p class="vt-summary-text">{{ $motion->summary }}</p>@endif
            @if ($motion->body)<div class="vt-prose">{!! nl2br(e($motion->body)) !!}</div>@endif
            <dl class="vt-facts">
                <div><dt>Created by</dt><dd>{{ $motion->creator?->name ?? 'Unknown' }}</dd></div>
                <div><dt>Opens</dt><dd>{{ $motion->opens_at?->format('j M Y, H:i') ?? 'On publish' }}</dd></div>
                <div><dt>Closes</dt><dd>{{ $motion->closes_at?->format('j M Y, H:i') }}</dd></div>
                <div><dt>Votes cast</dt><dd>{{ $motion->votes_count }}</dd></div>
                <div><dt>Rules</dt><dd>{{ $rules }}</dd></div>
            </dl>
        </section>

        @if ($tally)
            @include('votes._result', ['motion' => $motion, 'tally' => $tally, 'final' => true])
        @else
            <section class="vt-panel">
                <h2>Result</h2>
                <p class="vt-hint">No result yet. The tally is recorded when voting closes.</p>
            </section>
        @endif

        @if ($canClose || $canCancel)
            <section class="vt-panel">
                <h2>Actions</h2>
                @if ($canClose)
                    <form method="POST" action="{{ route('admin.motions.close', $motion) }}" class="vt-field">
                        @csrf
                        <label class="vt-label" for="close-reason">Close voting now (reason is recorded)</label>
                        <input class="vt-input" id="close-reason" name="reason" type="text" required minlength="3" maxlength="300">
                        <div class="vt-actions"><button class="vt-btn vt-btn-solid" type="submit">Close and record result</button></div>
                    </form>
                @endif
                @if ($canCancel)
                    <form method="POST" action="{{ route('admin.motions.cancel', $motion) }}" class="vt-field">
                        @csrf
                        <label class="vt-label" for="cancel-reason">Cancel this motion (reason is recorded)</label>
                        <input class="vt-input" id="cancel-reason" name="reason" type="text" required minlength="3" maxlength="300">
                        <div class="vt-actions"><button class="vt-btn vt-btn-danger" type="submit">Cancel motion</button></div>
                    </form>
                @endif
            </section>
        @endif

        @if ($motion->isDraft() && $motion->created_by === auth()->id())
            <section class="vt-panel">
                <h2>Draft</h2>
                <form method="POST" action="{{ route('votes.publish', $motion) }}">
                    @csrf
                    <button class="vt-btn vt-btn-solid" type="submit">Publish motion</button>
                    <a class="vt-btn" href="{{ route('votes.edit', $motion) }}">Edit draft</a>
                </form>
            </section>
        @endif

        @if ($voters->isNotEmpty())
            <section class="vt-panel">
                <h2>Voters</h2>
                <div class="vt-tablewrap"><table class="vt-table">
                    <thead><tr><th>Member</th><th>Choice</th><th>Cast</th></tr></thead>
                    <tbody>
                        @foreach ($voters as $v)
                            <tr><td>{{ $v->user?->name }}</td><td>{{ $v->choice }}</td><td>{{ $v->updated_at?->format('j M Y, H:i') }}</td></tr>
                        @endforeach
                    </tbody>
                </table></div>
            </section>
        @endif

        <section class="vt-panel">
            <h2>Audit trail</h2>
            <div class="vt-tablewrap"><table class="vt-table">
                <thead><tr><th>When</th><th>Event</th><th>By</th><th>Details</th></tr></thead>
                <tbody>
                    @forelse ($events as $e)
                        <tr>
                            <td>{{ $e->created_at?->format('j M Y, H:i') }}</td>
                            <td>{{ str_replace('_', ' ', $e->event) }}</td>
                            <td>{{ $e->user?->name ?? 'System' }}</td>
                            <td><code>{{ $e->data ? json_encode($e->data) : '' }}</code></td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No events.</td></tr>
                    @endforelse
                </tbody>
            </table></div>
        </section>
    </div>
</x-admin.shell>
