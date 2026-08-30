<?php

declare(strict_types=1);

namespace Tests\Unit\Membership;

use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Tests\TestCase;

final class MembershipApplicationStatusTest extends TestCase
{
    public function test_submitted_can_only_transition_to_under_review(): void
    {
        $this->assertTrue(MembershipApplicationStatus::Submitted->canTransitionTo(MembershipApplicationStatus::UnderReview));
        $this->assertFalse(MembershipApplicationStatus::Submitted->canTransitionTo(MembershipApplicationStatus::Approved));
        $this->assertFalse(MembershipApplicationStatus::Submitted->canTransitionTo(MembershipApplicationStatus::Declined));
    }

    public function test_under_review_can_transition_to_approved_or_declined(): void
    {
        $this->assertTrue(MembershipApplicationStatus::UnderReview->canTransitionTo(MembershipApplicationStatus::Approved));
        $this->assertTrue(MembershipApplicationStatus::UnderReview->canTransitionTo(MembershipApplicationStatus::Declined));
        $this->assertFalse(MembershipApplicationStatus::UnderReview->canTransitionTo(MembershipApplicationStatus::Submitted));
    }

    public function test_approved_and_declined_are_terminal(): void
    {
        $this->assertTrue(MembershipApplicationStatus::Approved->isTerminal());
        $this->assertTrue(MembershipApplicationStatus::Declined->isTerminal());
        $this->assertSame([], MembershipApplicationStatus::Approved->allowedTransitions());
        $this->assertSame([], MembershipApplicationStatus::Declined->allowedTransitions());
    }

    public function test_labels_are_human_readable(): void
    {
        $this->assertSame('Submitted', MembershipApplicationStatus::Submitted->label());
        $this->assertSame('Under review', MembershipApplicationStatus::UnderReview->label());
        $this->assertSame('Approved', MembershipApplicationStatus::Approved->label());
        $this->assertSame('Declined', MembershipApplicationStatus::Declined->label());
    }
}
