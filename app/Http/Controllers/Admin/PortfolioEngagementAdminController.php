<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PortfolioEngagementRequest;
use Domain\Content\Entities\PortfolioEngagement;
use Domain\Content\Repositories\PortfolioEngagementRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for PortfolioEngagement — the /portfolio engagement cards. See
 * Domain\Content\Entities\PortfolioEngagement.
 */
final class PortfolioEngagementAdminController extends Controller
{
    public function __construct(private readonly PortfolioEngagementRepository $engagements) {}

    public function index(): View
    {
        return view('admin.portfolio.index', ['engagements' => $this->engagements->all()]);
    }

    public function create(): View
    {
        return view('admin.portfolio.create');
    }

    public function store(PortfolioEngagementRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->engagements->save(new PortfolioEngagement(
            slug: Slug::fromString($data['slug']),
            sector: $data['sector'],
            title: $data['title'],
            summary: $data['summary'],
            outcome: $data['outcome'],
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.portfolio.index')->with('status', 'Engagement created.');
    }

    public function edit(string $portfolio_engagement): View
    {
        $found = $this->engagements->findBySlug(Slug::fromString($portfolio_engagement));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known engagement.', $portfolio_engagement));
        }

        return view('admin.portfolio.edit', ['engagement' => $found]);
    }

    public function update(string $portfolio_engagement, PortfolioEngagementRequest $request): RedirectResponse
    {
        $original = $this->engagements->findBySlug(Slug::fromString($portfolio_engagement));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known engagement.', $portfolio_engagement));
        }

        $data = $request->validated();

        $this->engagements->save(
            new PortfolioEngagement(
                slug: Slug::fromString($data['slug']),
                sector: $data['sector'],
                title: $data['title'],
                summary: $data['summary'],
                outcome: $data['outcome'],
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.portfolio.index')->with('status', 'Engagement updated.');
    }

    public function destroy(string $portfolio_engagement): RedirectResponse
    {
        $this->engagements->delete(Slug::fromString($portfolio_engagement));

        return redirect()->route('admin.portfolio.index')->with('status', 'Engagement removed.');
    }
}
