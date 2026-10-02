<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class NetworkConnectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'kind' => ['required', Rule::in(['connect', 'collaborate'])],
            'message' => ['required', 'string', 'min:20', 'max:600'],
            'topic' => ['nullable', 'required_if:kind,collaborate', 'string', 'min:3', 'max:120'],
            'sectors' => ['nullable', 'array', 'max:14'],
            'sectors.*' => ['string', Rule::in(array_keys(config('network.sectors')))],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'message.min' => 'Please write at least 20 characters so they know why you are reaching out.',
            'message.max' => 'Please keep your message to 600 characters.',
            'topic.required_if' => 'Add a short topic for the collaboration.',
        ];
    }
}
