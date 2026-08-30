<?php

declare(strict_types=1);

namespace Tests\Feature\Membership;

use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Membership\ValueObjects\MembershipReference;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\MembershipTierRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentMembershipApplicationRepository;
use Tests\TestCase;

final class EloquentMembershipApplicationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        MembershipTierRecord::query()->create([
            'slug' => 'sovereign-partner',
            'name' => 'Sovereign Partner',
            'audience' => 'Governments and sovereign funds.',
            'icon' => 'landmark',
            'position' => 1,
        ]);
    }

    public function test_save_then_find_by_reference_round_trips_an_application(): void
    {
        $application = MembershipApplication::submit(
            tier: Slug::fromString('sovereign-partner'),
            name: 'Jordan Achebe',
            organisation: 'Achebe Holdings',
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            phone: '+1 202 555 0100',
            country: 'Nigeria',
            statement: 'We represent a sovereign mandate seeking a discreet, long-term advisory relationship.',
        );

        $repository = new EloquentMembershipApplicationRepository;
        $repository->save($application);

        $found = $repository->findByReference($application->reference);

        $this->assertNotNull($found);
        $this->assertSame($application->reference->value, $found->reference->value);
        $this->assertSame('sovereign-partner', $found->tier->value);
        $this->assertSame('Jordan Achebe', $found->name);
        $this->assertSame('Achebe Holdings', $found->organisation);
        $this->assertSame('jordan@achebe-holdings.example', $found->email->value);
        $this->assertSame(MembershipApplicationStatus::Submitted, $found->status());
    }

    public function test_save_persists_a_status_transition(): void
    {
        $application = MembershipApplication::submit(
            tier: Slug::fromString('sovereign-partner'),
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString('amara@diallo.example'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a sovereign-level advisory relationship with Underground.',
        );

        $repository = new EloquentMembershipApplicationRepository;
        $repository->save($application);

        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        $repository->save($application);

        $found = $repository->findByReference($application->reference);

        $this->assertNotNull($found);
        $this->assertSame(MembershipApplicationStatus::UnderReview, $found->status());
    }

    public function test_find_by_reference_returns_null_when_missing(): void
    {
        $repository = new EloquentMembershipApplicationRepository;

        $this->assertNull($repository->findByReference(
            MembershipReference::fromString('UGM-2026-7KQ4XB'),
        ));
    }

    public function test_save_rejects_an_application_for_an_unknown_tier(): void
    {
        $application = MembershipApplication::submit(
            tier: Slug::fromString('unknown-tier'),
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString('amara@diallo.example'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for an advisory relationship with Underground.',
        );

        $repository = new EloquentMembershipApplicationRepository;

        $this->expectException(DomainException::class);

        $repository->save($application);
    }
}
