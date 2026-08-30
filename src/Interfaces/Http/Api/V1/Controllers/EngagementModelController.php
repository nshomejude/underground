<?php

declare(strict_types=1);

namespace Interfaces\Http\Api\V1\Controllers;

use Application\Content\Queries\ListEngagementModels;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * The public contract for the ways a client can retain the firm.
 */
final class EngagementModelController extends Controller
{
    public function __construct(private readonly ListEngagementModels $engagementModels) {}

    public function index(): JsonResponse
    {
        $models = ($this->engagementModels)();

        return response()->json([
            'data' => array_map(static fn ($model): array => $model->toArray(), $models),
        ]);
    }
}
