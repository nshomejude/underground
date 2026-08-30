<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Validates a password change on the member account settings page. The
 * current password is checked against the authenticated user's stored hash
 * in AccountSettingsController::updatePassword.
 */
final class UpdatePasswordRequest extends FormRequest
{
    /**
     * Named so this form's errors never bleed into the profile form's
     * identically-named `current_password` field on the same page.
     */
    protected $errorBag = 'updatePassword';

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
