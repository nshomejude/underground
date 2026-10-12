<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Support\IconLibrary;
use Domain\Content\ValueObjects\EmploymentStatus;
use Domain\Content\ValueObjects\TeamRoleCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both the creation and the update of a TeamMember. On update,
 * the slug uniqueness check ignores the record being edited (its slug
 * arrives on the route as {team_member}).
 */
final class TeamMemberRequest extends FormRequest
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
                Rule::unique('team_members', 'slug')->ignore($this->route('team_member'), 'slug'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'background' => ['required', 'string'],
            'has_portrait' => ['sometimes', 'boolean'],
            'icon' => [
                Rule::requiredIf(! $this->boolean('has_portrait')),
                'nullable', 'string', Rule::in(IconLibrary::NAMES),
            ],
            'role_category' => ['required', 'string', Rule::in(array_map(
                static fn (TeamRoleCategory $case): string => $case->value,
                TeamRoleCategory::cases(),
            ))],
            'employment_status' => ['required', 'string', Rule::in(array_map(
                static fn (EmploymentStatus $case): string => $case->value,
                EmploymentStatus::cases(),
            ))],
            'languages' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'integer', 'min:0'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
            'icon.required' => 'An icon is required unless this member uses the founder portrait.',
        ];
    }
}
