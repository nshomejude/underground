<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class VerificationCompanyDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:160'],
            'trading_name' => ['nullable', 'string', 'max:160'],
            'registration_number' => ['required', 'string', 'max:60'],
            'country' => ['required', 'string', 'max:80'],
            'incorporation_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today', 'after:1800-01-01'],
            'website' => ['nullable', 'url:http,https', 'max:200'],
            'registered_address' => ['required', 'string', 'max:500'],
            'applicant_role' => ['required', 'string', 'max:120'],
            'sectors' => ['nullable', 'array', 'max:6'],
            'sectors.*' => ['string', Rule::in(array_keys(config('network.sectors')))],
        ];
    }
}
