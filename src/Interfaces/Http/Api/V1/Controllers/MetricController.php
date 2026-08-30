<?php

declare(strict_types=1);

namespace Interfaces\Http\Api\V1\Controllers;

use Application\Content\Queries\ListMetrics;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * The public contract for the credibility bar's proof points.
 */
final class MetricController extends Controller
{
    public function __construct(private readonly ListMetrics $metrics) {}

    public function index(): JsonResponse
    {
        $metrics = ($this->metrics)();

        return response()->json([
            'data' => array_map(static fn ($metric): array => $metric->toArray(), $metrics),
        ]);
    }
}
