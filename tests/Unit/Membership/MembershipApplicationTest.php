<?php

declare(strict_types=1);

namespace Tests\Unit\Membership;

use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\Exceptions\IllegalMembershipTransition;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Tests\TestCase;

final class MembershipApplicationTest extends TestCase
{
    public function test_submit_creates_an_application_in_submitted_status(): void
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

        $this->assertSame(MembershipApplicationStatus::Submitted, $application->status());
        $this->assertSame('sovereign-partner', $application->tier->value);
        $this->assertStringStartsWith('UGM-', $application->reference->value);
    }

    public function test_submit_rejects_a_blank_name(): void
    {
        $this->expectException(DomainException::class);

        MembershipApplication::submit(
            tier: Slug::fromString('sovereign-partner'),
            name: '   ',
            organisation: null,
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            phone: null,
            country: null,
            statement: 'We represent a sovereign mandate seeking a discreet, long-term advisory relationship.',
        );
    }

    public function test_submit_rejects_a_statement_below_the_minimum_length(): void
    {
        $this->expectException(DomainException::class);

        MembershipApplication::submit(
            tier: Slug::fromString('sovereign-partner'),
            name: 'Jordan Achebe',
            organisation: null,
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            phone: null,
            country: null,
            statement: 'Too short.',
        );
    }

    public function test_transition_to_follows_the_allowed_status_machine(): void
    {
        $application = MembershipApplication::submit(
            tier: Slug::fromString('principal-circle'),
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString('amara@diallo.example'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        );

        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        $this->assertSame(MembershipApplicationStatus::UnderReview, $application->status());

        $application->transitionTo(MembershipApplicationStatus::Approved);
        $this->assertSame(MembershipApplicationStatus::Approved, $application->status());
    }

    public function test_transition_to_rejects_an_illegal_move(): void
    {
        $application = MembershipApplication::submit(
            tier: Slug::fromString('corporate-affiliate'),
            name: 'Amara Diallo',
            organisation: 'Diallo Group',
            email: EmailAddress::fromString('amara@diallo.example'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a corporate affiliate relationship with Underground.',
        );

        $this->expectException(IllegalMembershipTransition::class);

        $application->transitionTo(MembershipApplicationStatus::Approved);
    }

    public function test_to_array_exposes_the_expected_shape(): void
    {
        $application = MembershipApplication::submit(
            tier: Slug::fromString('corporate-affiliate'),
            name: 'Amara Diallo',
            organisation: 'Diallo Group',
            email: EmailAddress::fromString('amara@diallo.example'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a corporate affiliate relationship with Underground.',
        );

        $data = $application->toArray();

        $this->assertSame('corporate-affiliate', $data['tier']);
        $this->assertSame('Amara Diallo', $data['name']);
        $this->assertSame('submitted', $data['status']);
        $this->assertSame('Submitted', $data['status_label']);
        $this->assertArrayHasKey('reference', $data);
        $this->assertArrayHasKey('submitted_at', $data);
    }
}
