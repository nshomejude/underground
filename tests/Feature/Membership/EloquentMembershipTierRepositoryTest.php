<?php

declare(strict_types=1);

namespace Tests\Feature\Membership;

use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\MembershipTierRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentMembershipTierRepository;
use Tests\TestCase;

final class EloquentMembershipTierRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_returns_tiers_ordered_by_position(): void
    {
        MembershipTierRecord::query()->create([
            'slug' => 'principal-circle',
            'name' => 'Principal Circle',
            'audience' => 'Principals and family offices.',
            'icon' => 'gem',
            'position' => 2,
        ]);

        MembershipTierRecord::query()->create([
            'slug' => 'sovereign-partner',
            'name' => 'Sovereign Partner',
            'audience' => 'Governments and sovereign funds.',
            'icon' => 'landmark',
            'position' => 1,
        ]);

        $repository = new EloquentMembershipTierRepository;

        $tiers = $repository->all();

        $this->assertCount(2, $tiers);
        $this->assertSame('sovereign-partner', $tiers[0]->slug->value);
        $this->assertSame('principal-circle', $tiers[1]->slug->value);
    }

    public function test_find_by_slug_returns_a_matching_tier(): void
    {
        MembershipTierRecord::query()->create([
            'slug' => 'corporate-affiliate',
            'name' => 'Corporate Affiliate',
            'audience' => 'Corporations and institutions.',
            'icon' => 'building-2',
            'position' => 3,
        ]);

        $repository = new EloquentMembershipTierRepository;

        $tier = $repository->findBySlug(Slug::fromString('corporate-affiliate'));

        $this->assertNotNull($tier);
        $this->assertSame('Corporate Affiliate', $tier->name);
    }

    public function test_find_by_slug_returns_null_when_missing(): void
    {
        $repository = new EloquentMembershipTierRepository;

        $this->assertNull($repository->findBySlug(Slug::fromString('does-not-exist')));
    }
}
