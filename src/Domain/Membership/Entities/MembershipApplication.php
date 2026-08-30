<?php

declare(strict_types=1);

namespace Domain\Membership\Entities;

use DateTimeImmutable;
use Domain\Membership\Exceptions\IllegalMembershipTransition;
use Domain\Membership\ValueObjects\MemberId;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Membership\ValueObjects\MembershipReference;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;

/**
 * The aggregate root of the Membership context: an approach made through the
 * membership application form for a given tier.
 */
final class MembershipApplication
{
    private const MINIMUM_STATEMENT_LENGTH = 40;

    private function __construct(
        public readonly MembershipReference $reference,
        public readonly Slug $tier,
        public readonly string $name,
        public readonly ?string $organisation,
        public readonly EmailAddress $email,
        public readonly ?string $phone,
        public readonly ?string $country,
        public readonly string $statement,
        public readonly DateTimeImmutable $submittedAt,
        private MembershipApplicationStatus $status,
        private ?MemberId $memberId = null,
    ) {}

    public static function submit(
        Slug $tier,
        string $name,
        ?string $organisation,
        EmailAddress $email,
        ?string $phone,
        ?string $country,
        string $statement,
        ?DateTimeImmutable $submittedAt = null,
        ?MembershipReference $reference = null,
    ): self {
        $name = trim($name);
        $statement = trim($statement);

        if ($name === '') {
            throw new DomainException('A membership application must carry a name.');
        }

        if (mb_strlen($statement) < self::MINIMUM_STATEMENT_LENGTH) {
            throw new DomainException(sprintf(
                'A statement must be at least %d characters so it can be reviewed.',
                self::MINIMUM_STATEMENT_LENGTH,
            ));
        }

        $submittedAt ??= new DateTimeImmutable;

        return new self(
            reference: $reference ?? MembershipReference::generate((int) $submittedAt->format('Y')),
            tier: $tier,
            name: $name,
            organisation: self::nullIfBlank($organisation),
            email: $email,
            phone: self::nullIfBlank($phone),
            country: self::nullIfBlank($country),
            statement: $statement,
            submittedAt: $submittedAt,
            status: MembershipApplicationStatus::Submitted,
        );
    }

    /** Rehydrate an aggregate that already exists in storage. */
    public static function reconstitute(
        MembershipReference $reference,
        Slug $tier,
        string $name,
        ?string $organisation,
        EmailAddress $email,
        ?string $phone,
        ?string $country,
        string $statement,
        DateTimeImmutable $submittedAt,
        MembershipApplicationStatus $status,
        ?MemberId $memberId = null,
    ): self {
        return new self(
            $reference, $tier, $name, $organisation, $email,
            $phone, $country, $statement, $submittedAt, $status, $memberId,
        );
    }

    public function status(): MembershipApplicationStatus
    {
        return $this->status;
    }

    public function memberId(): ?MemberId
    {
        return $this->memberId;
    }

    public function transitionTo(MembershipApplicationStatus $target): void
    {
        if (! $this->status->canTransitionTo($target)) {
            throw IllegalMembershipTransition::between($this->status, $target);
        }

        $this->status = $target;
    }

    /**
     * Approve the application, issuing its permanent member id in the same
     * motion — the card credential exists from the instant membership is
     * granted, never as a separate step an operator can forget.
     *
     * Idempotent: calling this again (e.g. a retried job) leaves an
     * already-assigned member id untouched rather than replacing it, and
     * skips the transition if the application is already approved. The
     * caller supplies the id (see Application\Membership\Actions\
     * ApproveMembershipApplication) because only storage knows the next
     * sequence number — the aggregate itself stays free of persistence
     * concerns.
     */
    public function approve(MemberId $memberId): void
    {
        if ($this->status !== MembershipApplicationStatus::Approved) {
            $this->transitionTo(MembershipApplicationStatus::Approved);
        }

        $this->memberId ??= $memberId;
    }

    public function toArray(): array
    {
        return [
            'reference' => $this->reference->value,
            'tier' => $this->tier->value,
            'name' => $this->name,
            'organisation' => $this->organisation,
            'email' => $this->email->value,
            'phone' => $this->phone,
            'country' => $this->country,
            'statement' => $this->statement,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'submitted_at' => $this->submittedAt->format(DATE_ATOM),
            'member_id' => $this->memberId?->value,
        ];
    }

    private static function nullIfBlank(?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        return $value === '' ? null : $value;
    }
}
