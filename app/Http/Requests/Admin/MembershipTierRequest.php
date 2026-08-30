<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Support\IconLibrary;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both the creation and the update of a MembershipTier. On
 * update, the slug uniqueness check ignores the record being edited (its
 * slug arrives on the route as {membership_tier}).
 */
final class MembershipTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'slug' => [
                'required', 'string', 'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('membership_tiers', 'slug')->ignore($this->route('membership_tier'), 'slug'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'audience' => ['required', 'string'],
            'icon' => ['required', 'string', Rule::in(IconLibrary::NAMES)],
            'position' => ['required', 'integer', 'min:0'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
        ];
    }
}
