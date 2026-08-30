<?php

declare(strict_types=1);

namespace Tests\Feature\Membership;

use Application\Membership\Support\MembershipStubDirectory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MembershipControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_index_page_lists_the_vetted_tiers(): void
    {
        $this->get(route('membership.index'))
            ->assertOk()
            ->assertSee('Sovereign Partner')
            ->assertSee('Principal Circle')
            ->assertSee('Corporate Affiliate');
    }

    public function test_the_apply_page_renders_for_a_valid_tier(): void
    {
        $this->get(route('membership.apply', ['tier' => 'sovereign-partner']))
            ->assertOk()
            ->assertSee('Apply for Sovereign Partner');
    }

    public function test_the_apply_page_404s_for_an_unknown_tier(): void
    {
        $this->get(route('membership.apply', ['tier' => 'not-a-real-tier']))
            ->assertNotFound();
    }

    public function test_a_valid_submission_records_an_application_and_shows_its_reference(): void
    {
        $response = $this->from(route('membership.apply', ['tier' => 'sovereign-partner']))
            ->post(route('membership.store', ['tier' => 'sovereign-partner']), $this->validPayload());

        $response->assertRedirect(route('membership.apply', ['tier' => 'sovereign-partner']));
        $response->assertSessionHas('reference');

        $reference = $response->getSession()->get('reference');
        $this->assertMatchesRegularExpression('/^UGM-\d{4}-[A-Z0-9]{6}$/', $reference);

        $stored = MembershipStubDirectory::find($reference);
        $this->assertNotNull($stored);
        $this->assertSame('Amara Okafor', $stored->name);
        $this->assertSame('sovereign-partner', $stored->tier->value);

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('Application Received')
            ->assertSee($reference);
    }

    public function test_the_store_action_404s_for_an_unknown_tier(): void
    {
        $this->post(route('membership.store', ['tier' => 'not-a-real-tier']), $this->validPayload())
            ->assertNotFound();
    }

    public function test_an_invalid_submission_is_rejected_and_persists_nothing(): void
    {
        $response = $this->from(route('membership.apply', ['tier' => 'sovereign-partner']))
            ->post(route('membership.store', ['tier' => 'sovereign-partner']), [
                'applicant_name' => 'Amara Okafor',
                'email' => 'not-an-email',
                'statement' => 'Too short.',
            ]);

        $response->assertRedirect(route('membership.apply', ['tier' => 'sovereign-partner']));
        $response->assertSessionHasErrors(['email', 'statement']);
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'applicant_name' => 'Amara Okafor',
            'organisation' => 'Ministry of Trade',
            'email' => 'amara.okafor@example.com',
            'phone' => '+234 800 000 0000',
            'country' => 'Nigeria',
            'statement' => 'We are seeking a discreet strategic partner to advise on regional infrastructure financing.',
        ];
    }
}
