<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\MemberAccess;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Create / edit a motion (member and admin forms share it). */
final class MotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && app(MemberAccess::class)->canCreateMotion($user);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'intent' => ['required', Rule::in(['draft', 'publish'])],
            'title' => ['required', 'string', 'min:5', 'max:160'],
            'summary' => ['nullable', 'string', 'max:400'],
            'body' => ['nullable', 'string', 'max:8000'],
            'kind' => ['required', Rule::in(['decision', 'election', 'poll'])],
            'min_tier_rank' => ['required', 'integer', 'between:1,3'],
            'options' => ['nullable', 'array', 'max:12'],
            'options.*' => ['nullable', 'string', 'max:80'],
            'quorum' => ['required', 'integer', 'between:1,10000'],
            'pass_threshold' => ['nullable', 'numeric', 'between:50,100'],
            'show_results' => ['required', Rule::in(['after_close', 'live'])],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['required', 'date'],
            'anonymous' => ['boolean'],
            'allow_change' => ['boolean'],
            'weighted' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'anonymous' => $this->boolean('anonymous'),
            'allow_change' => $this->boolean('allow_change'),
            'weighted' => $this->boolean('weighted'),
        ]);
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                if ($this->input('kind') !== 'decision') {
                    $options = $this->options();
                    if (count($options) < 2 || count($options) > 8) {
                        $validator->errors()->add('options', 'Give between 2 and 8 distinct choices.');
                    }
                } elseif ($this->input('pass_threshold') === null || $this->input('pass_threshold') === '') {
                    $validator->errors()->add('pass_threshold', 'Choose the pass threshold (50 to 100 percent).');
                }

                $opens = $this->opensAt() ?? CarbonImmutable::now();
                $closes = CarbonImmutable::parse((string) $this->input('closes_at'));

                if ($closes->lt($opens->addHour())) {
                    $validator->errors()->add('closes_at', 'Voting must close at least one hour after it opens.');
                } elseif ($closes->gt($opens->addDays(90))) {
                    $validator->errors()->add('closes_at', 'Voting can stay open for at most 90 days.');
                }
            },
        ];
    }

    /** @return list<string> */
    private function options(): array
    {
        $seen = [];
        $out = [];

        foreach ((array) $this->input('options', []) as $option) {
            $option = trim((string) $option);
            if ($option !== '' && ! isset($seen[mb_strtolower($option)])) {
                $seen[mb_strtolower($option)] = true;
                $out[] = $option;
            }
        }

        return $out;
    }

    private function opensAt(): ?CarbonImmutable
    {
        $raw = $this->input('opens_at');

        if ($raw === null || $raw === '') {
            return null;
        }

        $at = CarbonImmutable::parse((string) $raw);

        return $at->isFuture() ? $at : null;
    }

    /** @return array<string, mixed> */
    public function motionData(): array
    {
        $v = $this->validated();

        return [
            'title' => trim($v['title']),
            'summary' => isset($v['summary']) ? trim($v['summary']) : null,
            'body' => isset($v['body']) ? trim($v['body']) : null,
            'kind' => $v['kind'],
            'min_tier_rank' => (int) $v['min_tier_rank'],
            'choices' => $v['kind'] === 'decision' ? ['For', 'Against', 'Abstain'] : $this->options(),
            'quorum' => (int) $v['quorum'],
            'pass_threshold' => $v['pass_threshold'] ?? 50,
            'anonymous' => (bool) $v['anonymous'],
            'allow_change' => (bool) $v['allow_change'],
            'weighted' => (bool) $v['weighted'],
            'show_results' => $v['show_results'],
            'opens_at' => $this->opensAt(),
            'closes_at' => CarbonImmutable::parse((string) $v['closes_at']),
        ];
    }

    public function wantsToPublish(): bool
    {
        return $this->input('intent') === 'publish';
    }
}
