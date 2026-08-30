<?php

declare(strict_types=1);

namespace Tests\Feature\Membership;

use DateTimeImmutable;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\ValueObjects\MemberId;
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

    public function test_save_persists_and_round_trips_an_assigned_member_id(): void
    {
        $application = MembershipApplication::submit(
            tier: Slug::fromString('sovereign-partner'),
            name: 'Kwame Asante',
            organisation: null,
            email: EmailAddress::fromString('kwame@asante.example'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a sovereign-level advisory relationship with Underground.',
        );

        $repository = new EloquentMembershipApplicationRepository;
        $repository->save($application);

        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        $application->approve(MemberId::assign(2026, 1));
        $repository->save($application);

        $found = $repository->findByReference($application->reference);

        $this->assertNotNull($found);
        $this->assertSame(MembershipApplicationStatus::Approved, $found->status());
        $this->assertSame('UG · 2026 · 000001', $found->memberId()?->value);
    }

    public function test_find_by_email_returns_the_most_recent_application_case_insensitively(): void
    {
        $older = MembershipApplication::submit(
            tier: Slug::fromString('sovereign-partner'),
            name: 'Older Application',
            organisation: null,
            email: EmailAddress::fromString('repeat@example.com'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a sovereign-level advisory relationship with Underground.',
            submittedAt: new DateTimeImmutable('2025-01-01'),
        );

        $newer = MembershipApplication::submit(
            tier: Slug::fromString('sovereign-partner'),
            name: 'Newer Application',
            organisation: null,
            email: EmailAddress::fromString('repeat@example.com'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a sovereign-level advisory relationship with Underground.',
            submittedAt: new DateTimeImmutable('2026-01-01'),
        );

        $repository = new EloquentMembershipApplicationRepository;
        $repository->save($older);
        $repository->save($newer);

        $found = $repository->findByEmail(EmailAddress::fromString('REPEAT@EXAMPLE.COM'));

        $this->assertNotNull($found);
        $this->assertSame('Newer Application', $found->name);
    }

    public function test_find_by_email_returns_null_when_no_application_exists(): void
    {
        $repository = new EloquentMembershipApplicationRepository;

        $this->assertNull($repository->findByEmail(EmailAddress::fromString('nobody@example.com')));
    }

    public function test_next_member_id_sequence_counts_up_from_assigned_member_ids(): void
    {
        $repository = new EloquentMembershipApplicationRepository;

        $this->assertSame(1, $repository->nextMemberIdSequence());

        $application = MembershipApplication::submit(
            tier: Slug::fromString('sovereign-partner'),
            name: 'Kwame Asante',
            organisation: null,
            email: EmailAddress::fromString('kwame@asante.example'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a sovereign-level advisory relationship with Underground.',
        );
        $repository->save($application);
        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        $application->approve(MemberId::assign(2026, $repository->nextMemberIdSequence()));
        $repository->save($application);

        $this->assertSame(2, $repository->nextMemberIdSequence());
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
