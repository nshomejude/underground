<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\Actions\ApproveMembershipApplication;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DEMO/SEED-ONLY accounts that let the member account area (/account) be
 * exercised end-to-end without a staff approval UI (explicitly out of scope
 * for this module — approvals go through the real domain transition, here,
 * standing in for that missing UI).
 *
 * These are well-known placeholder credentials, not real secrets:
 *
 *   Approved member  approved.member@underground.example / member-password
 *   Pending member   pending.member@underground.example  / member-password
 */
final class MembershipAccountDemoSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'member-password';

    public function run(ApplyForMembership $apply, ApproveMembershipApplication $approve): void
    {
        $this->seedApprovedMember($apply, $approve);
        $this->seedPendingMember($apply);
    }

    /** Demonstrates the "approved, shows the real card" /account state. */
    private function seedApprovedMember(ApplyForMembership $apply, ApproveMembershipApplication $approve): void
    {
        $email = 'approved.member@underground.example';

        User::query()->create([
            'name' => 'Isabelle Fontaine-Whitmore',
            'email' => $email,
            'password' => Hash::make(self::DEMO_PASSWORD),
            'email_verified_at' => now(),
        ]);

        $application = ($apply)(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Isabelle Fontaine-Whitmore',
            organisation: null,
            email: EmailAddress::fromString($email),
            phone: null,
            country: 'France',
            statement: 'Requesting consideration for a principal-level advisory relationship on behalf of a family office mandate.',
        ));

        // Real domain transitions, not a status column written directly.
        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        ($approve)($application);
    }

    /** Demonstrates the "still under review" /account state. */
    private function seedPendingMember(ApplyForMembership $apply): void
    {
        $email = 'pending.member@underground.example';

        User::query()->create([
            'name' => 'Marcus Reyes',
            'email' => $email,
            'password' => Hash::make(self::DEMO_PASSWORD),
            'email_verified_at' => now(),
        ]);

        ($apply)(new MembershipApplicationPayload(
            tier: Slug::fromString('corporate-affiliate'),
            name: 'Marcus Reyes',
            organisation: 'Castellane Atlantic Holdings',
            email: EmailAddress::fromString($email),
            phone: null,
            country: 'United States',
            statement: 'Requesting consideration for a corporate affiliate relationship on behalf of Castellane Atlantic Holdings.',
        ));
    }
}
