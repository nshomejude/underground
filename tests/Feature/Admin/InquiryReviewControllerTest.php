<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Application\Engagement\Actions\SubmitConfidentialInquiry;
use Application\Engagement\DataTransferObjects\InquiryPayload;
use Application\Engagement\Queries\ListInquiries;
use Domain\Engagement\Entities\ConfidentialInquiry;
use Domain\Engagement\ValueObjects\InquiryStatus;
use Domain\Engagement\ValueObjects\InterestArea;
use Domain\Shared\ValueObjects\EmailAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class InquiryReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    private function submitInquiry(string $email = 'prospect@example.com'): ConfidentialInquiry
    {
        return ($this->app->make(SubmitConfidentialInquiry::class))(new InquiryPayload(
            name: 'Jordan Achebe',
            organisation: null,
            email: EmailAddress::fromString($email),
            phone: null,
            country: null,
            interest: InterestArea::BusinessDevelopment,
            brief: 'Seeking a discreet introduction to explore a long-term advisory relationship.',
        ));
    }

    private function findInquiry(string $reference): ConfidentialInquiry
    {
        $all = ($this->app->make(ListInquiries::class))();

        return collect($all)->first(fn (ConfidentialInquiry $i) => $i->reference->value === $reference);
    }

    public function test_the_index_lists_inquiries_with_status_badges(): void
    {
        $inquiry = $this->submitInquiry();

        $response = $this->actingAs($this->admin)->get(route('admin.inquiries.index'));

        $response->assertOk();
        $response->assertSee($inquiry->reference->value);
        $response->assertSee($inquiry->name);
        $response->assertSee('Received');
    }

    public function test_a_legal_transition_succeeds(): void
    {
        $inquiry = $this->submitInquiry('legal@example.com');

        $response = $this->actingAs($this->admin)->post(
            route('admin.inquiries.transition', $inquiry->reference->value),
            ['status' => InquiryStatus::UnderReview->value],
        );

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $updated = $this->findInquiry($inquiry->reference->value);
        $this->assertSame(InquiryStatus::UnderReview, $updated->status());
    }

    public function test_an_illegal_transition_is_rejected_with_a_flash_error_not_a_500(): void
    {
        $inquiry = $this->submitInquiry('illegal@example.com');

        // Received -> Engaged is not a legal move; must go through UnderReview.
        $response = $this->actingAs($this->admin)->post(
            route('admin.inquiries.transition', $inquiry->reference->value),
            ['status' => InquiryStatus::Engaged->value],
        );

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $updated = $this->findInquiry($inquiry->reference->value);
        $this->assertSame(InquiryStatus::Received, $updated->status());
    }

    public function test_a_non_admin_cannot_transition_an_inquiry(): void
    {
        $inquiry = $this->submitInquiry('blocked@example.com');
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->post(
            route('admin.inquiries.transition', $inquiry->reference->value),
            ['status' => InquiryStatus::UnderReview->value],
        )->assertForbidden();
    }
}
