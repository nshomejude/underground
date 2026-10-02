<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\ProfileService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * One form per profile section; each posts a hidden `section` and only that
 * section's fields are validated and saved. The request always acts on the
 * signed-in member's own profile (there is no profile id to tamper with).
 */
final class ProfileUpdateRequest extends FormRequest
{
    /** @var array<string, list<string>> */
    public const SECTIONS = [
        'identity' => ['display_name', 'headline'],
        'about' => ['bio'],
        'location' => ['city', 'country', 'timezone', 'languages'],
        'focus' => ['sectors', 'supply_chain_roles'],
        'seeking' => ['seeking_kinds', 'seeking_summary', 'offering_summary'],
        'services' => ['services'],
        'portfolio' => ['portfolio'],
        'organisation' => ['organisation_name', 'organisation_role', 'organisation_size', 'organisation_website'],
        'links' => ['website', 'linkedin_url', 'public_email'],
        'visibility' => ['visibility', 'open_to_collaboration'],
    ];

    /** Section fields that are lists: an absent value means "cleared". */
    private const LISTS = ['languages', 'sectors', 'supply_chain_roles', 'seeking_kinds', 'services', 'portfolio'];

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $section = (string) $this->input('section');
        $fields = self::SECTIONS[$section] ?? [];
        $merge = [];

        foreach ($fields as $field) {
            if (in_array($field, self::LISTS, true) && ! $this->has($field)) {
                $merge[$field] = [];
            }
        }

        if (in_array('languages', $fields, true)) {
            $raw = $this->input('languages');
            $list = is_string($raw) ? explode(',', $raw) : (array) $raw;
            $merge['languages'] = collect($list)->map(fn ($v) => is_string($v) ? trim($v) : $v)
                ->filter(fn ($v) => $v !== '' && $v !== null)->values()->all();
        }

        foreach (['services', 'portfolio'] as $rows) {
            if (in_array($rows, $fields, true)) {
                $merge[$rows] = collect((array) $this->input($rows, []))
                    ->filter(fn ($row) => is_array($row) && collect($row)->filter(fn ($v) => is_string($v) && trim($v) !== '')->isNotEmpty())
                    ->map(fn (array $row) => array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row))
                    ->values()->all();
            }
        }

        if (in_array('open_to_collaboration', $fields, true)) {
            $merge['open_to_collaboration'] = $this->boolean('open_to_collaboration');
        }

        $this->merge($merge);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $sectors = array_keys((array) config('network.sectors'));
        $roles = array_keys((array) config('network.supply_chain_roles'));
        $kinds = array_keys((array) config('network.seeking_kinds'));
        $visibility = array_keys((array) config('network.profile_visibility'));
        $max = ProfileService::MAX_ROWS;
        $section = (string) $this->input('section');

        $all = [
            'display_name' => ['required', 'string', 'min:2', 'max:80'],
            'headline' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'timezone' => ['nullable', 'timezone:all'],
            'languages' => ['array', 'max:10'],
            'languages.*' => ['string', 'max:40'],
            'sectors' => ['array', 'max:'.count($sectors)],
            'sectors.*' => ['string', Rule::in($sectors)],
            'supply_chain_roles' => ['array', 'max:'.count($roles)],
            'supply_chain_roles.*' => ['string', Rule::in($roles)],
            'seeking_kinds' => ['array', 'max:'.count($kinds)],
            'seeking_kinds.*' => ['string', Rule::in($kinds)],
            'seeking_summary' => ['nullable', 'string', 'max:1000'],
            'offering_summary' => ['nullable', 'string', 'max:1000'],
            'services' => ['array', 'min:1', 'max:'.$max],
            'services.*.title' => ['required', 'string', 'max:120'],
            'services.*.description' => ['nullable', 'string', 'max:600'],
            'services.*.sector' => ['nullable', 'string', Rule::in($sectors)],
            'portfolio' => ['array', 'max:'.$max],
            'portfolio.*.title' => ['required', 'string', 'max:120'],
            'portfolio.*.summary' => ['nullable', 'string', 'max:600'],
            'portfolio.*.year' => ['nullable', 'integer', 'between:1950,'.((int) date('Y') + 1)],
            'portfolio.*.client_type' => ['nullable', 'string', 'max:120'],
            'portfolio.*.link' => ['nullable', 'url:http,https', 'max:255'],
            'organisation_name' => ['nullable', 'string', 'max:160'],
            'organisation_role' => ['nullable', 'string', 'max:120'],
            'organisation_size' => ['nullable', 'string', 'max:60'],
            'organisation_website' => ['nullable', 'url:http,https', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'linkedin_url' => ['nullable', 'url:http,https', 'max:255', 'regex:/^https?:\/\/([a-z0-9-]+\.)?linkedin\.com\//i'],
            'public_email' => ['nullable', 'email:rfc', 'max:190'],
            'visibility' => ['required', Rule::in($visibility)],
            'open_to_collaboration' => ['boolean'],
        ];

        $fields = self::SECTIONS[$section] ?? [];
        $rules = ['section' => ['required', Rule::in(array_keys(self::SECTIONS))]];

        foreach ($all as $key => $rule) {
            $root = explode('.', $key)[0];
            if (in_array($root, $fields, true)) {
                $rules[$key] = $rule;
            }
        }

        return $rules;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'services.min' => 'List at least one service you offer.',
            'services.*.title.required' => 'Each service needs a title.',
            'portfolio.*.title.required' => 'Each portfolio item needs a title.',
            'bio.max' => 'Your bio can be at most 2000 characters.',
            'linkedin_url.regex' => 'Enter a LinkedIn address (linkedin.com/...).',
            'sectors.*.in' => 'Choose sectors from the list.',
        ];
    }

    /** Only the section's fields. @return array<string, mixed> */
    public function sectionData(): array
    {
        return collect($this->validated())->except('section')->all();
    }
}
