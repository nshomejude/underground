<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the flat form fields behind the Narrative singleton. The
 * headline (one line per rendered line) and navigation (a fixed set of
 * label/href rows) are assembled back into their list shapes by the
 * controller after validation — see NarrativeAdminController::update().
 */
final class NarrativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'company' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'eyebrow' => ['required', 'string', 'max:255'],
            'headline_text' => ['required', 'string'],
            'accent_line' => ['required', 'string', 'max:255'],
            'intro' => ['required', 'string'],
            'primary_cta_label' => ['required', 'string', 'max:255'],
            'primary_cta_href' => ['required', 'string', 'max:255'],
            'secondary_cta_label' => ['required', 'string', 'max:255'],
            'secondary_cta_href' => ['required', 'string', 'max:255'],
            'creed_title' => ['required', 'string', 'max:255'],
            'creed_body' => ['required', 'string'],
            'capabilities_eyebrow' => ['required', 'string', 'max:255'],
            'capabilities_heading' => ['required', 'string', 'max:255'],
            'sectors_heading' => ['required', 'string', 'max:255'],
            'reach_heading' => ['required', 'string', 'max:255'],
            'reach_body' => ['required', 'string'],
            'reach_cta_label' => ['required', 'string', 'max:255'],
            'reach_cta_href' => ['required', 'string', 'max:255'],
            'engagement_heading' => ['required', 'string', 'max:255'],
            'closing_heading' => ['required', 'string', 'max:255'],
            'closing_support' => ['required', 'string'],
            'closing_cta_label' => ['required', 'string', 'max:255'],
            'closing_cta_href' => ['required', 'string', 'max:255'],
            'navigation' => ['array'],
            'navigation.*.label' => ['nullable', 'string', 'max:255'],
            'navigation.*.href' => ['nullable', 'string', 'max:255'],
            'copyright' => ['required', 'string', 'max:255'],
        ];
    }
}
