<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class VerificationCompanyDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = [];
        foreach (array_keys(config('network.company_document_types')) as $type) {
            $rules[$type] = [
                'nullable', 'file', 'mimetypes:'.VerificationFileRules::DOCS,
                'max:'.(int) config('verification.company_max_kb'),
                VerificationFileRules::minDimension(),
            ];
        }

        return $rules;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        $m = [];
        foreach (array_keys(config('network.company_document_types')) as $type) {
            $m += VerificationFileRules::messages($type);
        }

        return $m;
    }
}
