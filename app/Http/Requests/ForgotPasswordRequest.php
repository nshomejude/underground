<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the "email me a reset link" request. Deliberately does not
 * check that the email belongs to an account — see ForgotPasswordController,
 * which always returns the same generic confirmation regardless of whether
 * the address is registered, to avoid leaking account existence.
 */
final class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }
}
