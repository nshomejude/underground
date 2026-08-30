<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Application\Content\DataTransferObjects\LandingPage;
use Application\Insights\Queries\ListInsights;
use Domain\Content\Repositories\NarrativeRepository;

/**
 * The single use case behind the landing page, in both of its shapes.
 */
final readonly class ComposeLandingPage
{
    public function __construct(
        private NarrativeRepository $narrative,
        private ListMetrics $metrics,
        private ListCapabilities $capabilities,
        private ListSectors $sectors,
        private ListEngagementModels $engagementModels,
        private ListPillars $pillars,
        private ListInsights $insights,
    ) {}

    public function __invoke(): LandingPage
    {
        return new LandingPage(
            narrative: $this->narrative->current(),
            metrics: ($this->metrics)(),
            capabilities: ($this->capabilities)(),
            sectors: ($this->sectors)(),
            engagementModels: ($this->engagementModels)(),
            pillars: ($this->pillars)(),
            insights: ($this->insights)(limit: 3),
        );
    }
}
