<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MembershipTierRequest;
use Domain\Membership\Entities\MembershipTier;
use Domain\Membership\Repositories\MembershipTierRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for MembershipTier — the /membership tier cards. See
 * Domain\Membership\Entities\MembershipTier.
 */
final class MembershipTierAdminController extends Controller
{
    public function __construct(private readonly MembershipTierRepository $tiers) {}

    public function index(): View
    {
        return view('admin.membership-tiers.index', ['tiers' => $this->tiers->all()]);
    }

    public function create(): View
    {
        return view('admin.membership-tiers.create');
    }

    public function store(MembershipTierRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->tiers->save(new MembershipTier(
            slug: Slug::fromString($data['slug']),
            name: $data['name'],
            audience: $data['audience'],
            icon: $data['icon'],
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.membership-tiers.index')->with('status', 'Membership tier created.');
    }

    public function edit(string $membership_tier): View
    {
        $found = $this->tiers->findBySlug(Slug::fromString($membership_tier));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known membership tier.', $membership_tier));
        }

        return view('admin.membership-tiers.edit', ['tier' => $found]);
    }

    public function update(string $membership_tier, MembershipTierRequest $request): RedirectResponse
    {
        $original = $this->tiers->findBySlug(Slug::fromString($membership_tier));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known membership tier.', $membership_tier));
        }

        $data = $request->validated();

        $this->tiers->save(
            new MembershipTier(
                slug: Slug::fromString($data['slug']),
                name: $data['name'],
                audience: $data['audience'],
                icon: $data['icon'],
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.membership-tiers.index')->with('status', 'Membership tier updated.');
    }

    public function destroy(string $membership_tier): RedirectResponse
    {
        $this->tiers->delete(Slug::fromString($membership_tier));

        return redirect()->route('admin.membership-tiers.index')->with('status', 'Membership tier removed.');
    }
}
