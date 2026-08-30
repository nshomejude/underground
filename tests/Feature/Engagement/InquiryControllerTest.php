<?php

declare(strict_types=1);

namespace Tests\Feature\Engagement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\InquiryRecord;
use Tests\TestCase;

final class InquiryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_form_page_renders(): void
    {
        $this->get(route('inquiries.create'))
            ->assertOk()
            ->assertSee('Start a Confidential Conversation');
    }

    public function test_a_valid_submission_creates_an_inquiry_and_shows_its_reference(): void
    {
        $response = $this->from(route('inquiries.create'))->post(route('inquiries.store'), [
            'name' => 'Jordan Achebe',
            'organisation' => 'Achebe Holdings',
            'email' => 'jordan@achebe-holdings.example',
            'phone' => '+1 202 555 0100',
            'country' => 'Nigeria',
            'interest' => 'investment-capital-strategy',
            'brief' => 'We are exploring a strategic capital raise across two jurisdictions.',
        ]);

        $response->assertRedirect(route('inquiries.create'));
        $response->assertSessionHas('reference');

        $this->assertDatabaseCount('confidential_inquiries', 1);

        $record = InquiryRecord::query()->first();
        $this->assertSame('Jordan Achebe', $record->name);
        $this->assertSame('jordan@achebe-holdings.example', $record->email);
        $this->assertSame('investment-capital-strategy', $record->interest);

        $reference = $response->getSession()->get('reference');
        $this->assertSame($reference, $record->reference);

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('Inquiry Received')
            ->assertSee($reference);
    }

    public function test_an_invalid_submission_is_rejected_and_persists_nothing(): void
    {
        $response = $this->from(route('inquiries.create'))->post(route('inquiries.store'), [
            'name' => 'Jordan Achebe',
            'email' => 'not-an-email',
            'interest' => 'not-a-real-interest',
            'brief' => 'too short',
        ]);

        $response->assertRedirect(route('inquiries.create'));
        $response->assertSessionHasErrors(['email', 'interest', 'brief']);

        $this->assertDatabaseCount('confidential_inquiries', 0);
    }

    public function test_a_brief_that_is_too_short_is_rejected(): void
    {
        $response = $this->post(route('inquiries.store'), [
            'name' => 'Jordan Achebe',
            'email' => 'jordan@achebe-holdings.example',
            'interest' => 'investment-capital-strategy',
            'brief' => 'far too short',
        ]);

        $response->assertSessionHasErrors('brief');
        $this->assertDatabaseCount('confidential_inquiries', 0);
    }
}
