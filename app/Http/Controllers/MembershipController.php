<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMembershipApplicationRequest;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Application\Membership\Queries\ListMembershipTiers;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * The Membership context's public contract: browse the vetted tiers and
 * submit an application for one. There is no public checkout — every
 * application is reviewed before a tier is granted.
 */
final class MembershipController extends Controller
{
    public function __construct(
        private readonly ListMembershipTiers $tiers,
        private readonly ApplyForMembership $apply,
    ) {}

    public function index(): View
    {
        return view('membership.index', [
            'tiers' => ($this->tiers)(),
        ]);
    }

    public function create(string $tier): View
    {
        $membershipTier = $this->tiers->bySlug($tier);

        if ($membershipTier === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a membership tier Underground extends.', $tier));
        }

        return view('membership.apply', [
            'tier' => $membershipTier,
        ]);
    }

    public function store(string $tier, StoreMembershipApplicationRequest $request): RedirectResponse
    {
        if ($this->tiers->bySlug($tier) === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a membership tier Underground extends.', $tier));
        }

        $validated = $request->validated();

        $payload = new MembershipApplicationPayload(
            tier: Slug::fromString($tier),
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
            return redirect()
                ->route('membership.apply', ['tier' => $tier])
                ->withInput()
                ->withErrors(['statement' => $exception->getMessage()]);
        }

        return redirect()
            ->route('membership.apply', ['tier' => $tier])
            ->with('reference', $application->reference);
    }
}
