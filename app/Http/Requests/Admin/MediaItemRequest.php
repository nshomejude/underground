<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Domain\Content\ValueObjects\MediaItemStatus;
use Domain\Content\ValueObjects\MediaType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both the creation and the update of a MediaItem. On update,
 * the slug uniqueness check ignores the record being edited (its slug
 * arrives on the route as {media_item}).
 */
final class MediaItemRequest extends FormRequest
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
                Rule::unique('media_items', 'slug')->ignore($this->route('media_item'), 'slug'),
            ],
            'type' => ['required', Rule::in(array_map(fn (MediaType $type) => $type->value, MediaType::cases()))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'date' => ['nullable', 'date'],
            'speaker' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_map(fn (MediaItemStatus $status) => $status->value, MediaItemStatus::cases()))],
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
