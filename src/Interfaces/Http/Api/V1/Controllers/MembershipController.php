<?php

declare(strict_types=1);

namespace Interfaces\Http\Api\V1\Controllers;

use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Application\Membership\Queries\ListMembershipTiers;
use Application\Membership\Queries\TrackMembershipApplication;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Interfaces\Http\Api\V1\Requests\ApplyForMembershipRequest;

/**
 * The Membership context's public contract: browse the vetted tiers, submit
 * an application, and track it by its reference. There is no public
 * checkout — every application is reviewed before a tier is granted.
 */
final class MembershipController extends Controller
{
    public function __construct(
        private readonly ListMembershipTiers $tiers,
        private readonly ApplyForMembership $apply,
        private readonly TrackMembershipApplication $track,
    ) {}

    public function tiers(): JsonResponse
    {
        $tiers = ($this->tiers)();

        return response()->json([
            'data' => array_map(static fn ($tier): array => $tier->toArray(), $tiers),
        ]);
    }

    public function apply(ApplyForMembershipRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $payload = new MembershipApplicationPayload(
            tier: Slug::fromString($validated['tier']),
            name: $validated['applicant_name'],
            organisation: $validated['organisation'] ?? null,
            email: EmailAddress::fromString($validated['email']),
            phone: $validated['phone'] ?? null,
            country: $validated['country'] ?? null,
            statement: $validated['statement'],
        );

        try {
            $application = ($this->apply)($payload);
        } catch (DomainException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json(['data' => $application->toArray()], 201);
    }

    public function show(string $reference): JsonResponse
    {
        try {
            $application = ($this->track)($reference);
        } catch (DomainException) {
            $application = null;
        }

        if ($application === null) {
            return response()->json(['message' => 'Membership application not found.'], 404);
        }

        return response()->json(['data' => $application->toArray()]);
    }
}
