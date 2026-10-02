<?php

declare(strict_types=1);

namespace Tests\Feature\Verification;

use App\Models\User;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\Actions\ApproveMembershipApplication;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Database\Seeders\MembershipTierSeeder;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class VerificationPagesTest extends TestCase
{
    use RefreshDatabase;

    private function member(): User
    {
        $this->seed(MembershipTierSeeder::class);
        $user = User::factory()->create(['email' => 'vf@example.com', 'email_verified_at' => now()]);

        $application = ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Verified Member',
            organisation: null,
            email: EmailAddress::fromString('vf@example.com'),
            phone: null,
            country: 'France',
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));
        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        ($this->app->make(ApproveMembershipApplication::class))($application);

        return $user;
    }

    public function test_the_hub_renders_for_a_signed_in_member(): void
    {
        $this->actingAs($this->member())->get(route('verification.index'))->assertOk();
    }

    public function test_every_verification_get_page_renders(): void
    {
        $user = $this->member();
        $checked = 0;

        foreach (Route::getRoutes() as $route) {
            $name = (string) $route->getName();
            if (! str_starts_with($name, 'verification.') || ! in_array('GET', $route->methods(), true) || $route->parameterNames() !== []) {
                continue;
            }
            $response = $this->actingAs($user)->get(route($name));
            $response->status() === 302 ? $response->assertRedirect() : $response->assertOk();
            $checked++;
        }

        $this->assertGreaterThanOrEqual(3, $checked);
    }
}
