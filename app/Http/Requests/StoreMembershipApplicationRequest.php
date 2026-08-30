<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates a submission to the membership application form. The tier is
 * carried on the route (see MembershipController::store()) and is checked
 * against the vetted tier list there rather than here.
 */
final class StoreMembershipApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'applicant_name' => ['required', 'string', 'max:255'],
            'organisation' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:120'],
            'statement' => ['required', 'string', 'min:40'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'statement.min' => 'Your statement must be at least 40 characters so it can be reviewed.',
        ];
    }
}
