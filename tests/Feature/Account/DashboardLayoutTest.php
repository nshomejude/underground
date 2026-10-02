<?php

declare(strict_types=1);

namespace Tests\Feature\Account;

use App\Models\User;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\Actions\ApproveMembershipApplication;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Database\Seeders\MembershipTierSeeder;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DashboardLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_benefits_panel_precedes_the_card_panel_in_the_approved_state(): void
    {
        $this->seed(MembershipTierSeeder::class);
        $user = User::factory()->create(['email' => 'layout@example.com']);

        $application = ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Layout Tester',
            organisation: null,
            email: EmailAddress::fromString('layout@example.com'),
            phone: null,
            country: 'France',
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));
        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        ($this->app->make(ApproveMembershipApplication::class))($application);

        $html = $this->actingAs($user)->get(route('account.show'))->assertOk()->getContent();

        $benefits = strpos($html, 'id="h-ben"');
        $card = strpos($html, 'id="card"');

        $this->assertNotFalse($benefits);
        $this->assertNotFalse($card);
        $this->assertLessThan($card, $benefits);
    }
}
