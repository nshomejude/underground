<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates a profile update (name/email) on the member account settings
 * page. `current_password` is only meaningfully required when the email is
 * actually changing — see AccountSettingsController::update, which checks
 * it against the authenticated user's stored hash.
 */
final class UpdateAccountSettingsRequest extends FormRequest
{
    /**
     * Named so this form's errors never bleed into the password-change
     * form's identically-named `current_password` field on the same page.
     */
    protected $errorBag = 'updateProfile';

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()?->id),
            ],
            'current_password' => ['nullable', 'string'],
        ];
    }
}
