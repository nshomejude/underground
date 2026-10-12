<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Domain\Consultation\ValueObjects\BudgetRange;
use Domain\Consultation\ValueObjects\ConsultationSector;
use Domain\Consultation\ValueObjects\MaturityStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates a submission to the Underground Consultation Desk's free
 * qualification form. Mirrors App\Http\Requests\
 * StoreConfidentialInquiryRequest's shape — see that class for the sibling
 * (paid-mandate) form.
 */
final class StoreConsultationRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'organisation' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'country' => ['nullable', 'string', 'max:120'],
            'sector' => ['required', 'string', Rule::in(array_column(ConsultationSector::cases(), 'value'))],
            'maturity_stage' => ['required', 'string', Rule::in(array_column(MaturityStage::cases(), 'value'))],
            'project_description' => ['required', 'string', 'min:20'],
            'need_description' => ['required', 'string', 'min:20'],
            'budget_range' => ['required', 'string', Rule::in(array_column(BudgetRange::cases(), 'value'))],
            'has_supporting_documents' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'project_description' => 'project description',
            'need_description' => 'need description',
            'maturity_stage' => 'maturity stage',
            'budget_range' => 'budget range',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'project_description.min' => 'Your project description must be at least 20 characters so it can be reviewed.',
            'need_description.min' => 'Your need description must be at least 20 characters so it can be reviewed.',
        ];
    }
}
