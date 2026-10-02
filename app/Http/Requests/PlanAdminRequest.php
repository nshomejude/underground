<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\MembershipPlan;
use App\Services\PlanService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/** Validates creating and updating a membership plan (admin). */
final class PlanAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $plan = $this->route('plan');

        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('membership_plans', 'slug')->ignore($plan instanceof MembershipPlan ? $plan->id : null),
            ],
            'tagline' => ['nullable', 'string', 'max:200'],
            'tier_slug' => ['required', Rule::in(array_keys(config('network.tier_ranks')))],
            'price' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'currency' => ['required', 'string', 'size:3', 'alpha'],
            'billing_interval' => ['required', Rule::in(array_keys(MembershipPlan::INTERVALS))],
            'features' => ['nullable', 'string', 'max:4000'],
            'connection_requests_per_month' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'vote_rank' => ['required', Rule::in([1, 2, 3])],
            'can_create_motions' => ['sometimes', 'boolean'],
            'forum_access' => ['required', Rule::in(array_keys(PlanService::FORUM_LABELS))],
            'inquiry_response' => ['required', Rule::in(array_keys(PlanService::RESPONSE_LABELS))],
            'cta_label' => ['nullable', 'string', 'max:60'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'position' => ['required', 'integer', 'min:0', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $price = $this->input('price');
                if ($price !== null && $price !== '' && $this->input('billing_interval') === 'by_invitation') {
                    $validator->errors()->add('billing_interval', 'Choose a billing interval when a price is set, or clear the price to keep it by application.');
                }
            },
        ];
    }

    /** The validated input shaped for the membership_plans table. @return array<string, mixed> */
    public function planAttributes(): array
    {
        $data = $this->validated();
        $price = $data['price'] ?? null;

        $features = collect(preg_split('/\R/', (string) ($data['features'] ?? '')))
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();

        $perMonth = $data['connection_requests_per_month'] ?? null;

        return [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'tagline' => $data['tagline'] ?? null,
            'tier_slug' => $data['tier_slug'],
            'price_cents' => $price === null || $price === '' ? null : (int) round(((float) $price) * 100),
            'currency' => strtoupper($data['currency']),
            'billing_interval' => $data['billing_interval'],
            'features' => $features,
            'limits' => [
                'connection_requests_per_month' => $perMonth === null || $perMonth === '' ? null : (int) $perMonth,
                'vote_rank' => (int) $data['vote_rank'],
                'can_create_motions' => $this->boolean('can_create_motions'),
                'forum_access' => $data['forum_access'],
                'inquiry_response' => $data['inquiry_response'],
            ],
            'cta_label' => $data['cta_label'] ?? null,
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
            'position' => (int) $data['position'],
        ];
    }
}
