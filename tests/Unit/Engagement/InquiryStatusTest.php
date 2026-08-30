<?php

declare(strict_types=1);

namespace Tests\Unit\Engagement;

use Domain\Engagement\ValueObjects\InquiryStatus;
use Tests\TestCase;

final class InquiryStatusTest extends TestCase
{
    public function test_received_can_transition_to_under_review_declined_or_archived(): void
    {
        $this->assertTrue(InquiryStatus::Received->canTransitionTo(InquiryStatus::UnderReview));
        $this->assertTrue(InquiryStatus::Received->canTransitionTo(InquiryStatus::Declined));
        $this->assertTrue(InquiryStatus::Received->canTransitionTo(InquiryStatus::Archived));
        $this->assertFalse(InquiryStatus::Received->canTransitionTo(InquiryStatus::Engaged));
    }

    public function test_under_review_can_transition_to_engaged_declined_or_archived(): void
    {
        $this->assertTrue(InquiryStatus::UnderReview->canTransitionTo(InquiryStatus::Engaged));
        $this->assertTrue(InquiryStatus::UnderReview->canTransitionTo(InquiryStatus::Declined));
        $this->assertTrue(InquiryStatus::UnderReview->canTransitionTo(InquiryStatus::Archived));
        $this->assertFalse(InquiryStatus::UnderReview->canTransitionTo(InquiryStatus::Received));
    }

    public function test_engaged_and_declined_can_only_reach_archived(): void
    {
        $this->assertSame([InquiryStatus::Archived], InquiryStatus::Engaged->allowedTransitions());
        $this->assertSame([InquiryStatus::Archived], InquiryStatus::Declined->allowedTransitions());
    }

    public function test_archived_is_terminal(): void
    {
        $this->assertTrue(InquiryStatus::Archived->isTerminal());
        $this->assertSame([], InquiryStatus::Archived->allowedTransitions());
        $this->assertFalse(InquiryStatus::Archived->canTransitionTo(InquiryStatus::Received));
    }

    public function test_received_is_not_terminal(): void
    {
        $this->assertFalse(InquiryStatus::Received->isTerminal());
    }

    public function test_label_returns_a_human_readable_string_for_every_case(): void
    {
        $this->assertSame('Received', InquiryStatus::Received->label());
        $this->assertSame('Under review', InquiryStatus::UnderReview->label());
        $this->assertSame('Engaged', InquiryStatus::Engaged->label());
        $this->assertSame('Declined', InquiryStatus::Declined->label());
        $this->assertSame('Archived', InquiryStatus::Archived->label());
    }
}
