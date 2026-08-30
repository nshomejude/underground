<?php

declare(strict_types=1);

namespace Interfaces\Http\Api\V1\Controllers;

use Application\Content\Queries\ListPillars;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * The public contract for the four brand pillars.
 */
final class PillarController extends Controller
{
    public function __construct(private readonly ListPillars $pillars) {}

    public function index(): JsonResponse
    {
        $pillars = ($this->pillars)();

        return response()->json([
            'data' => array_map(static fn ($pillar): array => $pillar->toArray(), $pillars),
        ]);
    }
}
