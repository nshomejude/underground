<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class SiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'social_links' => ['array'],
            'social_links.*.label' => ['nullable', 'string', 'max:50'],
            'social_links.*.url' => ['nullable', 'url', 'max:255'],
            'footer_note' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['required', 'string', 'max:255'],
            'meta_description' => ['required', 'string', 'max:500'],
            'og_image_url' => ['nullable', 'url', 'max:255'],
            'twitter_handle' => ['nullable', 'string', 'max:50'],
            'maintenance_mode' => ['sometimes', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:500'],
            'public_registration_enabled' => ['sometimes', 'boolean'],
            'gear_animation_enabled' => ['sometimes', 'boolean'],
            'network_animation_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
