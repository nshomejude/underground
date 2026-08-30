<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Support\IconLibrary;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both the creation and the update of an EngagementModel. On
 * update, the slug uniqueness check ignores the record being edited (its
 * slug arrives on the route as {engagement_model}).
 */
final class EngagementModelRequest extends FormRequest
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
                Rule::unique('engagement_models', 'slug')->ignore($this->route('engagement_model'), 'slug'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
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
