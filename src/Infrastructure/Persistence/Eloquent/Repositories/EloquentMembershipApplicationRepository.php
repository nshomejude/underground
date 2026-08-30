<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\Repositories\MembershipApplicationRepository;
use Domain\Membership\ValueObjects\MemberId;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Membership\ValueObjects\MembershipReference;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\MembershipApplicationRecord;
use Infrastructure\Persistence\Eloquent\Models\MembershipTierRecord;

final class EloquentMembershipApplicationRepository implements MembershipApplicationRepository
{
    public function save(MembershipApplication $application): void
    {
        $tierId = MembershipTierRecord::query()
            ->where('slug', $application->tier->value)
            ->value('id');

        if ($tierId === null) {
            throw new DomainException(sprintf(
                '"%s" is not a membership tier Underground extends.',
                $application->tier->value,
            ));
        }

        MembershipApplicationRecord::query()->updateOrCreate(
            ['reference' => $application->reference->value],
            [
                'tier_id' => $tierId,
                'applicant_name' => $application->name,
                'organisation' => $application->organisation,
                'email' => $application->email->value,
                'phone' => $application->phone,
                'country' => $application->country,
                'statement' => $application->statement,
                'status' => $application->status()->value,
                'submitted_at' => $application->submittedAt,
                'member_id' => $application->memberId()?->value,
            ],
        );
    }

    public function findByReference(MembershipReference $reference): ?MembershipApplication
    {
        $record = MembershipApplicationRecord::query()
            ->with('tier')
            ->where('reference', $reference->value)
            ->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function findByEmail(EmailAddress $email): ?MembershipApplication
    {
        $record = MembershipApplicationRecord::query()
            ->with('tier')
            ->whereRaw('LOWER(email) = ?', [strtolower($email->value)])
            ->orderByDesc('submitted_at')
            ->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function nextMemberIdSequence(): int
    {
        return MembershipApplicationRecord::query()->whereNotNull('member_id')->count() + 1;
    }

    private function toEntity(MembershipApplicationRecord $record): MembershipApplication
    {
        return MembershipApplication::reconstitute(
            reference: MembershipReference::fromString($record->reference),
            tier: Slug::fromString($record->tier->slug),
            name: $record->applicant_name,
            organisation: $record->organisation,
            email: EmailAddress::fromString($record->email),
            phone: $record->phone,
            country: $record->country,
            statement: $record->statement,
            submittedAt: $record->submitted_at,
            status: MembershipApplicationStatus::from($record->status),
            memberId: $record->member_id === null ? null : MemberId::fromString($record->member_id),
        );
    }
}
