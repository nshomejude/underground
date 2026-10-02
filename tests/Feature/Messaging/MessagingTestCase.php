<?php

declare(strict_types=1);

namespace Tests\Feature\Messaging;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\User;
use App\Services\ConversationService;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\Actions\ApproveMembershipApplication;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Database\Seeders\MembershipTierSeeder;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class MessagingTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MembershipTierSeeder::class);
    }

    protected function member(string $name = 'Member', string $tier = 'sovereign-partner'): User
    {
        $user = User::factory()->create(['name' => $name]);

        $application = ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString($tier),
            name: $name,
            organisation: 'Org',
            email: EmailAddress::fromString($user->email),
            phone: '+234 800 000 0000',
            country: 'Nigeria',
            statement: 'We are seeking a discreet strategic partner to advise on regional infrastructure financing.',
        ));
        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        ($this->app->make(ApproveMembershipApplication::class))($application);

        return $user;
    }

    /** @return array{0: User, 1: User, 2: Conversation} */
    protected function connectedPair(string $status = 'accepted'): array
    {
        $a = $this->member('Alice Adeyemi');
        $b = $this->member('Bruno Bello', 'principal-circle');

        $connection = Connection::query()->create([
            'requester_id' => $a->id,
            'addressee_id' => $b->id,
            'status' => $status,
            'responded_at' => now(),
        ]);

        return [$a, $b, app(ConversationService::class)->ensureFor($connection)];
    }
}
