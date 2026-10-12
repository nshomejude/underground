<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both the creation and the update of a GalleryPhoto. On
 * update, the slug uniqueness check ignores the record being edited (its
 * slug arrives on the route as {gallery_photo}).
 */
final class GalleryPhotoRequest extends FormRequest
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
                Rule::unique('gallery_photos', 'slug')->ignore($this->route('gallery_photo'), 'slug'),
            ],
            'caption' => ['required', 'string', 'max:255'],
            'context' => ['nullable', 'string'],
            'captured_on' => ['nullable', 'string', 'max:100'],
            'photo_rights_note' => ['required', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'position' => ['required', 'integer', 'min:0'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
            'photo_rights_note.required' => 'State the basis for using this photo (e.g. consent given, firm-owned, licensed) before it can be published.',
        ];
    }
}
