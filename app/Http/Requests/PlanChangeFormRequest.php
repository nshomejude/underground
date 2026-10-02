<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** A member asking to move to another plan. */
final class PlanChangeFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'integer', Rule::exists('membership_plans', 'id')->where('is_active', true)],
            'note' => ['nullable', 'string', 'max:600'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:10000000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'note.max' => 'Please keep your note to 600 characters or fewer.',
            'plan_id.exists' => 'That plan is not available.',
        ];
    }
}
