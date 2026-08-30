<?php

declare(strict_types=1);

namespace Tests\Feature\Engagement;

use Application\Engagement\Actions\SubmitConfidentialInquiry;
use Application\Engagement\Actions\TransitionInquiryStatus;
use Application\Engagement\DataTransferObjects\InquiryPayload;
use Domain\Engagement\Entities\ConfidentialInquiry;
use Domain\Engagement\ValueObjects\InquiryStatus;
use Domain\Engagement\ValueObjects\InterestArea;
use Domain\Shared\ValueObjects\EmailAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class InquiryTrackControllerTest extends TestCase
{
    use RefreshDatabase;

    private function submitInquiry(): ConfidentialInquiry
    {
        return ($this->app->make(SubmitConfidentialInquiry::class))(new InquiryPayload(
            name: 'Jordan Achebe',
            organisation: 'Achebe Holdings',
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            phone: '+1 202 555 0100',
            country: 'Nigeria',
            interest: InterestArea::InvestmentCapitalStrategy,
            brief: 'We are exploring a strategic capital raise across two jurisdictions.',
        ));
    }

    public function test_the_track_page_renders_with_no_reference_supplied(): void
    {
        $this->get(route('inquiries.track'))
            ->assertOk()
            ->assertSee('Track Your Inquiry');
    }

    public function test_a_valid_reference_shows_the_correct_status(): void
    {
        $inquiry = $this->submitInquiry();

        ($this->app->make(TransitionInquiryStatus::class))(
            $inquiry->reference->value,
            InquiryStatus::UnderReview,
        );

        $response = $this->get(route('inquiries.track', ['reference' => $inquiry->reference->value]));

        $response->assertOk();
        $response->assertSee($inquiry->reference->value);
        $response->assertSee('Under review');
        $response->assertSee('Investment & Capital Strategy');
        $response->assertSee($inquiry->submittedAt->format('j M Y'));
    }

    public function test_the_public_view_never_renders_private_details(): void
    {
        $inquiry = $this->submitInquiry();

        $response = $this->get(route('inquiries.track', ['reference' => $inquiry->reference->value]));

        $response->assertOk();
        $response->assertDontSee($inquiry->name);
        $response->assertDontSee($inquiry->organisation);
        $response->assertDontSee($inquiry->email->value);
        $response->assertDontSee($inquiry->phone);
        $response->assertDontSee($inquiry->brief);
    }

    public function test_an_unknown_but_well_formed_reference_shows_a_friendly_message(): void
    {
        $response = $this->get(route('inquiries.track', ['reference' => 'UG-2026-ZZZZZZ']));

        $response->assertOk();
        $response->assertSee('couldn&rsquo;t find an inquiry', false);
    }

    public function test_a_malformed_reference_shows_a_friendly_message_not_a_500(): void
    {
        $response = $this->get(route('inquiries.track', ['reference' => 'not-a-real-reference']));

        $response->assertOk();
        $response->assertSee('couldn&rsquo;t find an inquiry', false);
    }
}
