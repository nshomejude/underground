<?php

declare(strict_types=1);

namespace Interfaces\Http\Api\V1\Requests;

use Domain\Engagement\ValueObjects\InterestArea;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates a submission to the confidential-inquiry API endpoint. Mirrors
 * App\Http\Requests\StoreConfidentialInquiryRequest, the web form's rules.
 */
final class SubmitInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'organisation' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:120'],
            'interest' => ['required', 'string', Rule::in(array_column(InterestArea::cases(), 'value'))],
            'brief' => ['required', 'string', 'min:20'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'brief.min' => 'Your brief must be at least 20 characters so it can be triaged.',
        ];
    }
}
