<?php

declare(strict_types=1);

namespace Interfaces\Http\Api\V1\Controllers;

use Application\Insights\Queries\ListInsights;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * The public contract for browsing the think tank's published insights.
 */
final class InsightController extends Controller
{
    public function __construct(private readonly ListInsights $insights) {}

    public function index(Request $request): JsonResponse
    {
        $limit = $request->query('limit');

        $insights = ($this->insights)($limit === null ? null : (int) $limit);

        return response()->json([
            'data' => array_map(static fn ($insight): array => $insight->toArray(), $insights),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $insight = $this->insights->bySlug($slug);
        } catch (DomainException) {
            $insight = null;
        }

        if ($insight === null || ! $insight->isPublished()) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['data' => $insight->toArray()]);
    }
}
