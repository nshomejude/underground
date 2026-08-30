<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMembershipApplicationRequest;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Application\Membership\Queries\ListMembershipTiers;
use Domain\Membership\Entities\MembershipTier;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
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

    /**
     * A visual preview of the physical membership card issued once an
     * application clears review — one illustrative sample per tier. There is
     * no per-member lookup here yet; a real card would be populated from an
     * approved MembershipApplication plus the permanent member id assigned
     * at issuance, using this same component.
     */
    public function cards(): View
    {
        $tiers = ($this->tiers)();

        $bySlug = static fn (string $slug): ?MembershipTier => collect($tiers)
            ->first(fn (MembershipTier $tier): bool => $tier->slug->value === $slug);

        $samples = [
            [
                'tier' => $bySlug('sovereign-partner'),
                'variant' => 'organisation',
                'name' => 'Republic of Valcoria — Ministry of Finance',
                'representative' => 'Dr. Amara N. Osei',
                'representativeTitle' => 'Permanent Secretary',
                'memberId' => 'UG · 2018 · 000012',
                'issuedOn' => Carbon::parse('2024-01-15'),
                'validThrough' => Carbon::parse('2029-01-14'),
            ],
            [
                'tier' => $bySlug('principal-circle'),
                'variant' => 'individual',
                'name' => 'Isabelle Fontaine-Whitmore',
                'representative' => null,
                'representativeTitle' => null,
                'memberId' => 'UG · 2023 · 004821',
                'issuedOn' => Carbon::parse('2026-06-01'),
                'validThrough' => Carbon::parse('2031-05-31'),
            ],
            [
                'tier' => $bySlug('corporate-affiliate'),
                'variant' => 'organisation',
                'name' => 'Castellane Atlantic Holdings',
                'representative' => 'Marcus Reyes',
                'representativeTitle' => 'Chief Strategy Officer',
                'memberId' => 'UG · 2021 · 001147',
                'issuedOn' => Carbon::parse('2025-03-01'),
                'validThrough' => Carbon::parse('2030-02-28'),
            ],
        ];

        return view('membership.cards', ['samples' => $samples]);
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
            ->with('reference', $application->reference->value);
    }
}
