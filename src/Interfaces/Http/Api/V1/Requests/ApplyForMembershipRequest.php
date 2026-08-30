<?php

declare(strict_types=1);

namespace Interfaces\Http\Api\V1\Requests;

use Application\Membership\Queries\ListMembershipTiers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates a submission to the membership application form. The tier must
 * match one of the vetted tiers Underground currently extends.
 */
final class ApplyForMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'tier' => ['required', 'string', Rule::in($this->tierSlugs())],
            'applicant_name' => ['required', 'string', 'max:255'],
            'organisation' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:120'],
            'statement' => ['required', 'string', 'min:40'],
        ];
    }

    /** @return list<string> */
    private function tierSlugs(): array
    {
        return array_map(
            static fn ($tier): string => $tier->slug->value,
            (app(ListMembershipTiers::class))(),
        );
    }
}
