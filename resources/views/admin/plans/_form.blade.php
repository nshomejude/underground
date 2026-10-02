@php
    $limits = $plan->limits ?? [];
    $selectClass = 'rounded-adm border border-hairline bg-surface px-3 py-2 text-sm text-cream focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/25';
    $featuresText = old('features', implode("\n", $plan->features ?? []));
    $priceValue = $plan->price_cents === null ? null : ($plan->price_cents % 100 === 0 ? $plan->price_cents / 100 : number_format($plan->price_cents / 100, 2, '.', ''));
@endphp

<x-admin.field name="name" label="Name" :value="$plan->name" />
<x-admin.field name="slug" label="Slug" :value="$plan->slug" placeholder="principal-circle" />
<x-admin.field name="tagline" label="Tagline" :value="$plan->tagline" :required="false" />

<div class="flex flex-col gap-1.5">
    <label for="tier_slug" class="text-[13px] font-medium text-body">Membership tier</label>
    <select name="tier_slug" id="tier_slug" class="{{ $selectClass }}">
        @foreach (array_keys(config('network.tier_ranks')) as $slug)
            <option value="{{ $slug }}" @selected(old('tier_slug', $plan->tier_slug) === $slug)>{{ str($slug)->replace('-', ' ')->title() }}</option>
        @endforeach
    </select>
    @error('tier_slug') <p class="text-xs text-danger">{{ $message }}</p> @enderror
</div>

<fieldset class="flex flex-col gap-4 rounded-adm border border-hairline p-4">
    <legend class="px-2 text-[13px] font-medium text-body">Pricing (leave the price empty to show "By application")</legend>
    <x-admin.field name="price" label="Price (major units, e.g. 12000)" type="number" step="0.01" min="0" :value="$priceValue" :required="false" />
    <x-admin.field name="currency" label="Currency (3 letters)" :value="$plan->currency ?? 'USD'" maxlength="3" />
    <div class="flex flex-col gap-1.5">
        <label for="billing_interval" class="text-[13px] font-medium text-body">Billing interval</label>
        <select name="billing_interval" id="billing_interval" class="{{ $selectClass }}">
            @foreach (\App\Models\MembershipPlan::INTERVALS as $value => $label)
                <option value="{{ $value }}" @selected(old('billing_interval', $plan->billing_interval) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('billing_interval') <p class="text-xs text-danger">{{ $message }}</p> @enderror
    </div>
</fieldset>

<x-admin.textarea-field name="features" label="Features (one per line)" rows="8" :value="$featuresText" :required="false" />

<fieldset class="flex flex-col gap-4 rounded-adm border border-hairline p-4">
    <legend class="px-2 text-[13px] font-medium text-body">Limits</legend>
    <x-admin.field name="connection_requests_per_month" label="Connection requests per month (empty = unlimited)" type="number" min="0" :value="$limits['connection_requests_per_month'] ?? null" :required="false" />
    <div class="flex flex-col gap-1.5">
        <label for="vote_rank" class="text-[13px] font-medium text-body">Votes on motions up to</label>
        <select name="vote_rank" id="vote_rank" class="{{ $selectClass }}">
            @foreach (\App\Services\PlanService::VOTE_LABELS as $value => $label)
                <option value="{{ $value }}" @selected((int) old('vote_rank', $limits['vote_rank'] ?? 1) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('vote_rank') <p class="text-xs text-danger">{{ $message }}</p> @enderror
    </div>
    <x-admin.checkbox-field name="can_create_motions" label="May open motions for a vote" :checked="$limits['can_create_motions'] ?? false" />
    <div class="flex flex-col gap-1.5">
        <label for="forum_access" class="text-[13px] font-medium text-body">Invitation-only forums</label>
        <select name="forum_access" id="forum_access" class="{{ $selectClass }}">
            @foreach (\App\Services\PlanService::FORUM_LABELS as $value => $label)
                <option value="{{ $value }}" @selected(old('forum_access', $limits['forum_access'] ?? 'none') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('forum_access') <p class="text-xs text-danger">{{ $message }}</p> @enderror
    </div>
    <div class="flex flex-col gap-1.5">
        <label for="inquiry_response" class="text-[13px] font-medium text-body">Inquiry response</label>
        <select name="inquiry_response" id="inquiry_response" class="{{ $selectClass }}">
            @foreach (\App\Services\PlanService::RESPONSE_LABELS as $value => $label)
                <option value="{{ $value }}" @selected(old('inquiry_response', $limits['inquiry_response'] ?? 'standard') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('inquiry_response') <p class="text-xs text-danger">{{ $message }}</p> @enderror
    </div>
</fieldset>

<x-admin.field name="cta_label" label="Button label on the public page (optional)" :value="$plan->cta_label" :required="false" />
<x-admin.checkbox-field name="is_featured" label="Featured (&ldquo;Most chosen&rdquo;)" :checked="$plan->is_featured" />
<x-admin.checkbox-field name="is_active" label="Active (shown to the public)" :checked="$plan->is_active ?? true" />
<x-admin.field name="position" label="Position" type="number" min="0" :value="$plan->position ?? 0" />
