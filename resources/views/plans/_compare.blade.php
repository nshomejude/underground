{{-- Shared plan comparison: three plan cards (each with the real membership card
     preview) plus a feature matrix. $mode is 'member' (request form) or 'public'. --}}
@php
    $mode = $mode ?? 'public';
    $rank = $rank ?? 0;
    $canRequest = $canRequest ?? false;
    $pending = $pending ?? null;
    $current = $current ?? null;
@endphp

<div class="pl-grid">
    @foreach ($plans as $plan)
        @php
            $relation = $mode === 'member' ? $service->relation($plan, $rank) : 'none';
            $card = $service->sampleCard($plan->tier_slug);
        @endphp
        <article class="pl-plan {{ $plan->is_featured ? 'pl-plan-featured' : '' }} {{ $relation === 'current' ? 'pl-plan-current' : '' }}" aria-labelledby="plan-{{ $plan->id }}">
            <div class="pl-flags">
                @if ($relation === 'current')
                    <span class="pl-flag pl-flag-solid">Your plan</span>
                @elseif ($plan->is_featured)
                    <span class="pl-flag">Most chosen</span>
                @endif
            </div>

            @if ($card)
                <div class="pl-card" aria-hidden="false">
                    <x-membership-card
                        :variant="$card['variant']"
                        :name="$card['name']"
                        :representative="$card['representative']"
                        :representative-title="$card['representativeTitle']"
                        :tier="$card['tier']"
                        :member-id="$card['memberId']"
                        :issued-on="$card['issuedOn']"
                        :valid-through="$card['validThrough']"
                    />
                </div>
                <p class="pl-sample">Illustrative card.</p>
            @endif

            <h2 id="plan-{{ $plan->id }}" class="pl-name">{{ $plan->name }}</h2>
            @if ($plan->tagline)
                <p class="pl-tagline">{{ $plan->tagline }}</p>
            @endif

            <p class="pl-price">
                <span class="pl-amount" data-price="{{ $plan->hasPrice() ? 'set' : 'application' }}">{{ $plan->rangeLabel() }}</span>
                @if ($plan->hasPrice())
                    <span class="pl-interval">{{ $plan->intervalLabel() }}</span>
                @else
                    <span class="pl-interval">Terms are agreed with approved members</span>
                @endif
            </p>

            <ul class="pl-features">
                @foreach ($plan->features ?? [] as $feature)
                    <li><x-icon name="check" />{{ $feature }}</li>
                @endforeach
            </ul>

            <div class="pl-cta">
                @if ($mode === 'public')
                    <a href="{{ route('membership.apply', $plan->tier_slug) }}" class="pl-btn pl-btn-solid">{{ $plan->cta_label ?: 'Apply for membership' }} <x-icon name="arrow-right" /></a>
                @elseif ($rank === 0)
                    <a href="{{ route('membership.index') }}" class="pl-btn pl-btn-solid">Apply for membership <x-icon name="arrow-right" /></a>
                @elseif ($relation === 'current')
                    <span class="pl-note"><x-icon name="badge-check" /> Your plan</span>
                @elseif ($relation === 'downgrade')
                    <p class="pl-note">Downgrades are handled by our team. <a href="{{ route('contact') }}">Contact us</a>.</p>
                @elseif (! $canRequest)
                    <p class="pl-note">Verify your email address to request a change.</p>
                @elseif ($pending)
                    <p class="pl-note">
                        @if ($pending->to_plan_id === $plan->id)
                            Your request for this plan is awaiting review.
                        @else
                            You have a request awaiting review.
                        @endif
                        <a href="{{ route('plans.requests') }}">View requests</a>
                    </p>
                @else
                    <details class="pl-req" @if ($errors->any() && (int) old('plan_id') === $plan->id) open @endif>
                        <summary class="pl-btn pl-btn-solid">Request upgrade</summary>
                        <form method="POST" action="{{ route('plans.request') }}" class="pl-form">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            @if ($plan->hasPrice())
                                <label for="amount-{{ $plan->id }}" class="pl-label">Annual fee ({{ $plan->currency }}) <span>{{ number_format($plan->price_cents / 100) }} to {{ number_format(($plan->maxCents() ?? $plan->price_cents) / 100) }}</span></label>
                                <input id="amount-{{ $plan->id }}" name="amount" type="number" inputmode="numeric" min="{{ $plan->price_cents / 100 }}" max="{{ ($plan->maxCents() ?? $plan->price_cents) / 100 }}" step="1" class="pl-input" value="{{ (int) old('plan_id') === $plan->id ? old('amount') : $plan->price_cents / 100 }}">
                                @if ((int) old('plan_id') === $plan->id) @error('amount') <p class="pl-err" role="alert">{{ $message }}</p> @enderror @endif
                            @endif
                            <label for="note-{{ $plan->id }}" class="pl-label">Why do you want {{ $plan->name }}? <span>Optional</span></label>
                            <textarea id="note-{{ $plan->id }}" name="note" rows="4" maxlength="600" class="pl-input" placeholder="A sentence or two helps our team review quickly.">{{ (int) old('plan_id') === $plan->id ? old('note') : '' }}</textarea>
                            @if ((int) old('plan_id') === $plan->id)
                                @error('plan_id') <p class="pl-err" role="alert">{{ $message }}</p> @enderror
                                @error('note') <p class="pl-err" role="alert">{{ $message }}</p> @enderror
                            @endif
                            <button type="submit" class="pl-btn pl-btn-solid">Send request</button>
                            <p class="pl-fine">Staff review every request. Nothing changes on your membership until we confirm.</p>
                        </form>
                    </details>
                @endif
            </div>
        </article>
    @endforeach
</div>

@if ($plans->count() > 0)
    <section class="pl-matrix" aria-labelledby="pl-matrix-h">
        <h2 id="pl-matrix-h" class="pl-h2">Compare in detail</h2>
        <table class="pl-table">
            <thead>
                <tr>
                    <th scope="col"><span class="sr-only">Feature</span></th>
                    @foreach ($plans as $plan)
                        <th scope="col">{{ $plan->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($matrix as [$label, $cells])
                    <tr>
                        <th scope="row">{{ $label }}</th>
                        @foreach ($plans as $plan)
                            @php $cell = $cells[$plan->id] ?? false; @endphp
                            <td data-label="{{ $plan->name }}">
                                @if ($cell === true)
                                    <x-icon name="check" class="pl-yes" /><span class="sr-only">Included</span>
                                @elseif ($cell === false)
                                    <span class="pl-no" aria-hidden="true">&mdash;</span><span class="sr-only">Not included</span>
                                @else
                                    {{ $cell }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <th scope="row">Price</th>
                    @foreach ($plans as $plan)
                        <td data-label="{{ $plan->name }}">{{ $plan->priceLabel() }}{{ $plan->hasPrice() ? ' '.$plan->intervalLabel() : '' }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </section>
@else
    <p class="pl-empty">Plans will be published here soon.</p>
@endif
