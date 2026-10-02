@php
    $editing = $motion->exists;
    $choices = old('options', $editing && ! $motion->isDecision() ? $motion->choiceList() : []);
    $choices = array_pad(array_values((array) $choices), 4, '');
    $dt = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('Y-m-d\TH:i') : '';
    $kind = old('kind', $motion->kind ?: 'decision');
    $tier = (int) old('min_tier_rank', $motion->min_tier_rank ?: 1);
@endphp

@if ($errors->any())
    <div class="vt-alert vt-alert-error" role="alert"><x-icon name="triangle-alert" /><span>{{ $errors->first() }}</span></div>
@endif

<form method="POST" action="{{ $action }}" class="vt-form" novalidate>
    @csrf
    @if ($editing) @method('PUT') @endif

    <fieldset class="vt-panel">
        <legend class="vt-legend">The motion</legend>
        <div class="vt-field">
            <label class="vt-label" for="mt-title">Title</label>
            <input class="vt-input" id="mt-title" name="title" type="text" required minlength="5" maxlength="160" value="{{ old('title', $motion->title) }}">
            @error('title')<p class="vt-err">{{ $message }}</p>@enderror
        </div>
        <div class="vt-field">
            <label class="vt-label" for="mt-summary">Summary <span class="vt-hint">(optional, up to 400 characters)</span></label>
            <textarea class="vt-textarea" id="mt-summary" name="summary" rows="2" maxlength="400" style="min-height: 80px">{{ old('summary', $motion->summary) }}</textarea>
            @error('summary')<p class="vt-err">{{ $message }}</p>@enderror
        </div>
        <div class="vt-field">
            <label class="vt-label" for="mt-body">Full text <span class="vt-hint">(optional)</span></label>
            <textarea class="vt-textarea" id="mt-body" name="body" rows="8" maxlength="8000">{{ old('body', $motion->body) }}</textarea>
            @error('body')<p class="vt-err">{{ $message }}</p>@enderror
        </div>
    </fieldset>

    <fieldset class="vt-panel">
        <legend class="vt-legend">Who votes and how</legend>
        <div class="vt-row">
            <div class="vt-field">
                <label class="vt-label" for="mt-kind">Kind</label>
                <select class="vt-select" id="mt-kind" name="kind">
                    @foreach (['decision' => 'Decision (For, Against, Abstain)', 'election' => 'Election (choose one option)', 'poll' => 'Poll (choose one option)'] as $k => $label)
                        <option value="{{ $k }}" @selected($kind === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('kind')<p class="vt-err">{{ $message }}</p>@enderror
            </div>
            <div class="vt-field">
                <label class="vt-label" for="mt-tier">Who can vote</label>
                <select class="vt-select" id="mt-tier" name="min_tier_rank" aria-describedby="mt-tier-hint">
                    @foreach (\App\Models\Motion::TIER_LABELS as $rank => $label)
                        <option value="{{ $rank }}" @selected($tier === $rank)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="vt-hint" id="mt-tier-hint">Principal Circle and above can vote on motions limited to that tier; "All members" means every verified member.</p>
                @error('min_tier_rank')<p class="vt-err">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="vt-field" id="mt-options-wrap">
            <span class="vt-label">Choices <span class="vt-hint">(elections and polls: 2 to 8, blanks ignored)</span></span>
            <div class="vt-options">
                @foreach ($choices as $i => $option)
                    <input class="vt-input" type="text" name="options[]" maxlength="80" value="{{ $option }}" aria-label="Choice {{ $i + 1 }}">
                @endforeach
            </div>
            @error('options')<p class="vt-err">{{ $message }}</p>@enderror
        </div>

        <div class="vt-row">
            <div class="vt-field">
                <label class="vt-label" for="mt-quorum">Quorum (voters needed)</label>
                <input class="vt-input" id="mt-quorum" name="quorum" type="number" min="1" max="10000" value="{{ old('quorum', $motion->quorum ?? 10) }}">
                @error('quorum')<p class="vt-err">{{ $message }}</p>@enderror
            </div>
            <div class="vt-field">
                <label class="vt-label" for="mt-threshold">Pass threshold % <span class="vt-hint">(decisions)</span></label>
                <input class="vt-input" id="mt-threshold" name="pass_threshold" type="number" min="50" max="100" step="any" value="{{ old('pass_threshold', $motion->pass_threshold ?? 50) }}">
                @error('pass_threshold')<p class="vt-err">{{ $message }}</p>@enderror
            </div>
            <div class="vt-field">
                <label class="vt-label" for="mt-results">Results visible</label>
                <select class="vt-select" id="mt-results" name="show_results">
                    @foreach (['after_close' => 'After voting closes', 'live' => 'Live while voting'] as $k => $label)
                        <option value="{{ $k }}" @selected(old('show_results', $motion->show_results ?: 'after_close') === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <label class="vt-check"><input type="checkbox" name="anonymous" value="1" @checked(old('anonymous', $motion->anonymous))><span>Anonymous vote<small>Nobody, including administrators, can see how an individual voted.</small></span></label>
        <label class="vt-check"><input type="checkbox" name="allow_change" value="1" @checked(old('allow_change', $motion->allow_change ?? true))><span>Members may change their vote until voting closes</span></label>
        <label class="vt-check"><input type="checkbox" name="weighted" value="1" @checked(old('weighted', $motion->weighted))><span>Weight votes by membership tier</span></label>
    </fieldset>

    <fieldset class="vt-panel">
        <legend class="vt-legend">Schedule</legend>
        <div class="vt-row">
            <div class="vt-field">
                <label class="vt-label" for="mt-opens">Opens <span class="vt-hint">(blank: when published)</span></label>
                <input class="vt-input" id="mt-opens" name="opens_at" type="datetime-local" value="{{ old('opens_at', $dt($motion->opens_at)) }}">
                @error('opens_at')<p class="vt-err">{{ $message }}</p>@enderror
            </div>
            <div class="vt-field">
                <label class="vt-label" for="mt-closes">Closes</label>
                <input class="vt-input" id="mt-closes" name="closes_at" type="datetime-local" required value="{{ old('closes_at', $dt($motion->closes_at)) }}">
                <p class="vt-hint">At least one hour after it opens, and at most 90 days.</p>
                @error('closes_at')<p class="vt-err">{{ $message }}</p>@enderror
            </div>
        </div>
    </fieldset>

    <div class="vt-actions">
        <button type="submit" name="intent" value="publish" class="vt-btn vt-btn-solid"><x-icon name="check" />Publish motion</button>
        <button type="submit" name="intent" value="draft" class="vt-btn">Save as draft</button>
        <a href="{{ $cancelUrl }}" class="vt-btn">Cancel</a>
    </div>
</form>
