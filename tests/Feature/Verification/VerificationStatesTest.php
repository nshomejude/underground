<?php

declare(strict_types=1);

namespace Tests\Feature\Verification;

use App\Models\CompanyVerification;
use App\Models\IdentityVerification;
use App\Models\User;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\Actions\ApproveMembershipApplication;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Database\Seeders\MembershipTierSeeder;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class VerificationStatesTest extends TestCase
{
    use RefreshDatabase;

    private function member(): User
    {
        $this->seed(MembershipTierSeeder::class);
        $user = User::factory()->create(['email' => 'vs@example.com', 'email_verified_at' => now()]);
        $application = ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Verified Member',
            organisation: null,
            email: EmailAddress::fromString('vs@example.com'),
            phone: null,
            country: 'France',
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));
        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        ($this->app->make(ApproveMembershipApplication::class))($application);

        return $user;
    }

    /** @return array<string, mixed> */
    private function file(string $path): array
    {
        Storage::disk(config('network.verification_disk'))->put($path, 'x');

        return ['path' => $path, 'name' => 'scan.pdf', 'mime' => 'application/pdf', 'size' => 1000, 'width' => null, 'height' => null];
    }

    /** @return array<string, array{0: string}> */
    public static function states(): array
    {
        return ['draft' => ['draft'], 'submitted' => ['submitted'], 'in_review' => ['inReview'], 'approved' => ['approved'], 'rejected' => ['rejected']];
    }

    #[DataProvider('states')]
    public function test_pages_render_in_every_state(string $state): void
    {
        Storage::fake(config('network.verification_disk'));
        $user = $this->member();
        $idf = IdentityVerification::factory()->for($user);
        $cof = CompanyVerification::factory()->for($user);
        if ($state !== 'draft') {
            $idf = $idf->{$state}();
            $cof = $state === 'rejected'
                ? $cof->state(['status' => 'rejected', 'last_step' => 4, 'rejection_reason' => 'Unreadable', 'reviewed_at' => now()])
                : $cof->{$state}();
        }
        $idf->create([
            'files' => ['front' => $this->file('v/f.pdf'), 'back' => $this->file('v/b.pdf')],
            'document_type' => 'national_id', 'last_step' => 4,
        ]);
        $cof->create([
            'documents' => ['registration_certificate' => $this->file('v/r.pdf'), 'proof_of_address' => $this->file('v/p.pdf')],
            'last_step' => 4,
        ]);

        $this->actingAs($user)->get(route('verification.index'))->assertOk();
        foreach (['identity', 'company'] as $kind) {
            $this->get(route("verification.$kind.show"))->assertOk();
            if ($state === 'draft') {
                foreach ([1, 2, 3, 4] as $step) {
                    $this->get(route("verification.$kind.show", ['step' => $step]))->assertOk();
                }
            }
        }
    }
}
