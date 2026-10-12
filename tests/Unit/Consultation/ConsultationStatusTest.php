<?php

declare(strict_types=1);

namespace Tests\Unit\Consultation;

use Domain\Consultation\ValueObjects\ConsultationStatus;
use Tests\TestCase;

final class ConsultationStatusTest extends TestCase
{
    public function test_submitted_can_transition_to_under_review_or_declined(): void
    {
        $this->assertTrue(ConsultationStatus::Submitted->canTransitionTo(ConsultationStatus::UnderReview));
        $this->assertTrue(ConsultationStatus::Submitted->canTransitionTo(ConsultationStatus::Declined));
        $this->assertFalse(ConsultationStatus::Submitted->canTransitionTo(ConsultationStatus::Qualified));
    }

    public function test_under_review_can_transition_to_qualified_or_declined(): void
    {
        $this->assertTrue(ConsultationStatus::UnderReview->canTransitionTo(ConsultationStatus::Qualified));
        $this->assertTrue(ConsultationStatus::UnderReview->canTransitionTo(ConsultationStatus::Declined));
        $this->assertFalse(ConsultationStatus::UnderReview->canTransitionTo(ConsultationStatus::Submitted));
        $this->assertFalse(ConsultationStatus::UnderReview->canTransitionTo(ConsultationStatus::ProposalSent));
    }

    public function test_qualified_can_transition_to_proposal_sent_or_declined(): void
    {
        $this->assertTrue(ConsultationStatus::Qualified->canTransitionTo(ConsultationStatus::ProposalSent));
        $this->assertTrue(ConsultationStatus::Qualified->canTransitionTo(ConsultationStatus::Declined));
        $this->assertFalse(ConsultationStatus::Qualified->canTransitionTo(ConsultationStatus::UnderReview));
    }

    public function test_proposal_sent_can_only_reach_declined(): void
    {
        $this->assertSame([ConsultationStatus::Declined], ConsultationStatus::ProposalSent->allowedTransitions());
    }

    public function test_declined_is_terminal(): void
    {
        $this->assertTrue(ConsultationStatus::Declined->isTerminal());
        $this->assertSame([], ConsultationStatus::Declined->allowedTransitions());
        $this->assertFalse(ConsultationStatus::Declined->canTransitionTo(ConsultationStatus::Submitted));
    }

    public function test_submitted_is_not_terminal(): void
    {
        $this->assertFalse(ConsultationStatus::Submitted->isTerminal());
    }

    public function test_label_returns_a_human_readable_string_for_every_case(): void
    {
        $this->assertSame('Submitted', ConsultationStatus::Submitted->label());
        $this->assertSame('Under review', ConsultationStatus::UnderReview->label());
        $this->assertSame('Qualified', ConsultationStatus::Qualified->label());
        $this->assertSame('Proposal sent', ConsultationStatus::ProposalSent->label());
        $this->assertSame('Declined', ConsultationStatus::Declined->label());
    }
}
