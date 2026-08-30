<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both the creation and the update of a Sector. On update, the
 * slug uniqueness check ignores the record being edited (its slug arrives
 * on the route as {sector}), so renaming a slug to itself is not rejected.
 */
final class SectorRequest extends FormRequest
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
                Rule::unique('sectors', 'slug')->ignore($this->route('sector'), 'slug'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'motif' => ['required', 'string', 'max:255'],
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
