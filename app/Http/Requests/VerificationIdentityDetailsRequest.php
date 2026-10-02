<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class VerificationIdentityDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'document_type' => ['required', Rule::in(array_keys(config('network.identity_document_types')))],
            'document_country' => ['required', 'string', 'max:80'],
            'full_name_on_document' => ['required', 'string', 'min:3', 'max:120'],
            'date_of_birth' => ['required', 'date_format:Y-m-d', 'before:-16 years', 'after:1900-01-01'],
            'document_expiry' => ['required', 'date_format:Y-m-d', 'after:today'],
            'document_number_last4' => ['required', 'string', 'min:1', 'max:8', 'regex:/^[A-Za-z0-9 ]+$/'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'document_expiry.after' => 'This document has expired. Please use a valid, unexpired document.',
            'date_of_birth.before' => 'Members must be at least 16 years old.',
            'document_number_last4.regex' => 'Use letters and numbers only.',
        ];
    }
}
