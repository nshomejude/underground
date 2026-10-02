<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Shareable directory filters (GET query string). */
final class NetworkDirectoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'sector' => ['nullable', 'array', 'max:14'],
            'sector.*' => ['string', Rule::in(array_keys(config('network.sectors')))],
            'role' => ['nullable', 'string', Rule::in(array_keys(config('network.supply_chain_roles')))],
            'tier' => ['nullable', 'string', Rule::in(array_keys(config('network.tier_ranks')))],
            'country' => ['nullable', 'string', 'max:80'],
            'open' => ['nullable', 'boolean'],
            'company' => ['nullable', 'boolean'],
            'sort' => ['nullable', Rule::in(['match', 'newest', 'name'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /** @return array{q: string, sector: list<string>, role: string, tier: string, country: string, open: bool, company: bool, sort: string} */
    public function filters(): array
    {
        $v = $this->validated();

        return [
            'q' => trim((string) ($v['q'] ?? '')),
            'sector' => array_values($v['sector'] ?? []),
            'role' => (string) ($v['role'] ?? ''),
            'tier' => (string) ($v['tier'] ?? ''),
            'country' => (string) ($v['country'] ?? ''),
            'open' => (bool) ($v['open'] ?? false),
            'company' => (bool) ($v['company'] ?? false),
            'sort' => (string) ($v['sort'] ?? 'match'),
        ];
    }

    public function hasActiveFilters(): bool
    {
        $f = $this->filters();

        return $f['q'] !== '' || $f['sector'] !== [] || $f['role'] !== '' || $f['tier'] !== ''
            || $f['country'] !== '' || $f['open'] || $f['company'];
    }
}
