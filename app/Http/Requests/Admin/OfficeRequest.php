<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Domain\Content\ValueObjects\OfficeStatus;
use Domain\Content\ValueObjects\RelationshipType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both the creation and the update of an Office (an alliance
 * location or regional coverage entry). On update, the slug uniqueness
 * check ignores the record being edited (its slug arrives on the route
 * as {office}).
 */
final class OfficeRequest extends FormRequest
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
                Rule::unique('offices', 'slug')->ignore($this->route('office'), 'slug'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'blurb' => ['required', 'string', 'max:2000'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'countries_text' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(OfficeStatus::class)],
            'relationship_type' => ['required', Rule::enum(RelationshipType::class)],
            'is_headquarters' => ['sometimes', 'boolean'],
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
