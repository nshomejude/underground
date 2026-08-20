<?php

declare(strict_types=1);

namespace Application\Content\DataTransferObjects;

use Domain\Content\Entities\Capability;
use Domain\Content\Entities\EngagementModel;
use Domain\Content\Entities\Metric;
use Domain\Content\Entities\Narrative;
use Domain\Content\Entities\Pillar;
use Domain\Content\Entities\Sector;
use Domain\Insights\Entities\Insight;

/**
 * Everything the landing page needs, assembled once. The HTML view and the
 * `GET /api/v1/landing-page` endpoint are both rendered from this object, so
 * the two can never drift.
 */
final readonly class LandingPage
{
    /**
     * @param  list<Metric>  $metrics
     * @param  list<Capability>  $capabilities
     * @param  list<Sector>  $sectors
     * @param  list<EngagementModel>  $engagementModels
     * @param  list<Pillar>  $pillars
     * @param  list<Insight>  $insights
     */
    public function __construct(
        public Narrative $narrative,
        public array $metrics,
        public array $capabilities,
        public array $sectors,
        public array $engagementModels,
        public array $pillars,
        public array $insights,
    ) {
    }

    /** @return list<Capability> the three promoted to the mobile summary list */
    public function featuredCapabilities(int $limit = 3): array
    {
        $featured = array_values(array_filter(
            $this->capabilities,
            static fn (Capability $capability): bool => $capability->isFeatured,
        ));

        return array_slice($featured !== [] ? $featured : $this->capabilities, 0, $limit);
    }

    public function toArray(): array
    {
        return [
            'narrative' => $this->narrative->toArray(),
            'metrics' => array_map(static fn (Metric $m) => $m->toArray(), $this->metrics),
            'capabilities' => array_map(static fn (Capability $c) => $c->toArray(), $this->capabilities),
            'sectors' => array_map(static fn (Sector $s) => $s->toArray(), $this->sectors),
            'engagement_models' => array_map(static fn (EngagementModel $e) => $e->toArray(), $this->engagementModels),
            'pillars' => array_map(static fn (Pillar $p) => $p->toArray(), $this->pillars),
            'insights' => array_map(static fn (Insight $i) => $i->toArray(), $this->insights),
        ];
    }
}
