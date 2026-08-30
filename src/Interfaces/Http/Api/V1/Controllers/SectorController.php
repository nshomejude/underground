<?php

declare(strict_types=1);

namespace Interfaces\Http\Api\V1\Controllers;

use Application\Content\Queries\ListSectors;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * The public contract for browsing the verticals Underground operates in.
 */
final class SectorController extends Controller
{
    public function __construct(private readonly ListSectors $sectors) {}

    public function index(): JsonResponse
    {
        $sectors = ($this->sectors)();

        return response()->json([
            'data' => array_map(static fn ($sector): array => $sector->toArray(), $sectors),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $sector = $this->sectors->bySlug($slug);
        } catch (DomainException) {
            $sector = null;
        }

        if ($sector === null) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['data' => $sector->toArray()]);
    }
}
