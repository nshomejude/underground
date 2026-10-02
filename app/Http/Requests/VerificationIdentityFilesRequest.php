<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class VerificationIdentityFilesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $kb = (int) config('verification.identity_max_kb');

        return [
            'front' => VerificationFileRules::rules(true, $kb),
            'back' => VerificationFileRules::rules(true, $kb),
            'selfie' => VerificationFileRules::rules(false, $kb),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return VerificationFileRules::messages('front') + VerificationFileRules::messages('back') + VerificationFileRules::messages('selfie');
    }
}
