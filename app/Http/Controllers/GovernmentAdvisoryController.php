<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListCapabilities;
use Application\Content\Queries\ListEngagementModels;
use Application\Content\Queries\ListSectors;
use Illuminate\Contracts\View\View;

/**
 * Dedicated page for heads of state, ministers and cabinet offices
 * considering a cooperation framework with Underground Network: what an
 * initial diagnostic looks like, how a roadmap and a visit are prepared,
 * and how we report back afterward. The page is static editorial content
 * (there is no "government advisory" catalogue record), but it surfaces
 * the real capabilities, sector and engagement models that already exist
 * so nothing it points to is invented.
 */
final class GovernmentAdvisoryController extends Controller
{
    public function __construct(
        private readonly ListCapabilities $capabilities,
        private readonly ListSectors $sectors,
        private readonly ListEngagementModels $engagementModels,
    ) {}

    public function index(): View
    {
        return view('government-advisory.index', [
            'relatedCapabilities' => $this->pick(($this->capabilities)(), [
                'government-political-affairs',
                'international-relations-diplomacy',
                'strategic-intelligence-analysis',
            ]),
            'relatedEngagementModels' => $this->pick(($this->engagementModels)(), [
                'strategic-advisory-retainers',
                'government-affairs-lobbying',
            ]),
            'sector' => $this->sectors->bySlug('government-public-sector'),
        ]);
    }

    /**
     * @param  list<object>  $items
     * @param  list<string>  $slugs
     * @return list<object>
     */
    private function pick(array $items, array $slugs): array
    {
        return array_values(array_filter(
            $items,
            static fn ($item): bool => in_array($item->slug->value, $slugs, true),
        ));
    }
}
