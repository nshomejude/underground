<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Domain\Content\Repositories\CapabilityRepository;
use Domain\Content\Repositories\EngagementModelRepository;
use Domain\Content\Repositories\MetricRepository;
use Domain\Content\Repositories\NarrativeRepository;
use Domain\Content\Repositories\PillarRepository;
use Domain\Content\Repositories\SectorRepository;
use Domain\Engagement\Repositories\InquiryRepository;
use Domain\Insights\Repositories\InsightRepository;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentCapabilityRepository;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentEngagementModelRepository;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentInquiryRepository;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentInsightRepository;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentMetricRepository;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentNarrativeRepository;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentPillarRepository;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentSectorRepository;

/**
 * Binds the domain's repository contracts to their Eloquent implementations.
 * This is the only place the domain layer and the database meet.
 */
final class DomainServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    private const REPOSITORIES = [
        CapabilityRepository::class => EloquentCapabilityRepository::class,
        SectorRepository::class => EloquentSectorRepository::class,
        MetricRepository::class => EloquentMetricRepository::class,
        InsightRepository::class => EloquentInsightRepository::class,
        InquiryRepository::class => EloquentInquiryRepository::class,
        EngagementModelRepository::class => EloquentEngagementModelRepository::class,
        PillarRepository::class => EloquentPillarRepository::class,
        NarrativeRepository::class => EloquentNarrativeRepository::class,
    ];

    public function register(): void
    {
        foreach (self::REPOSITORIES as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }
}
