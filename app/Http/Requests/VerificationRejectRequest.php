<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class VerificationRejectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'reason' => ['required', Rule::in(array_keys(config('verification.rejection_reasons')))],
            'message' => ['nullable', 'string', 'max:1000', Rule::requiredIf(fn () => $this->input('reason') === 'other')],
            'notes' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
