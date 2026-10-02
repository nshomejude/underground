<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MembershipPlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class MembershipPlan extends Model
{
    /** @use HasFactory<MembershipPlanFactory> */
    use HasFactory;

    public const INTERVALS = [
        'by_invitation' => 'By application',
        'month' => 'per month',
        'year' => 'per year',
        'one_time' => 'one-time',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'limits' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /** 1..3 (corporate affiliate .. sovereign partner). */
    public function rank(): int
    {
        return (int) config('network.tier_ranks.'.$this->tier_slug, 0);
    }

    /** True when staff have set a real price (otherwise pricing is by application). */
    public function hasPrice(): bool
    {
        return $this->price_cents !== null && $this->billing_interval !== 'by_invitation';
    }

    /** "By application" until staff set a real price; then "USD 12,000". */
    public function priceLabel(): string
    {
        if (! $this->hasPrice()) {
            return 'By application';
        }

        $amount = $this->price_cents % 100 === 0
            ? number_format($this->price_cents / 100)
            : number_format($this->price_cents / 100, 2);

        return trim($this->currency.' '.$amount);
    }

    /** Upper end of the annual fee band in cents (null when there is no band). */
    public function maxCents(): ?int
    {
        $max = $this->limits['price_max_cents'] ?? null;

        return $max === null ? null : (int) $max;
    }

    /** "USD 10,000 to 100,000" when a band exists, else the plain label. */
    public function rangeLabel(): string
    {
        $max = $this->maxCents();

        if (! $this->hasPrice() || $max === null || $max <= $this->price_cents) {
            return $this->priceLabel();
        }

        return $this->currency.' '.number_format($this->price_cents / 100).' to '.number_format($max / 100);
    }

    /** The interval suffix ("per year"); empty when pricing is by application. */
    public function intervalLabel(): string
    {
        return $this->hasPrice() ? (self::INTERVALS[$this->billing_interval] ?? '') : '';
    }
}
