<?php

declare(strict_types=1);

namespace Interfaces\Http\Api\V1\Controllers;

use Application\Content\Queries\ListCapabilities;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * The public contract for browsing the disciplines Underground sells.
 */
final class CapabilityController extends Controller
{
    public function __construct(private readonly ListCapabilities $capabilities) {}

    public function index(): JsonResponse
    {
        $capabilities = ($this->capabilities)();

        return response()->json([
            'data' => array_map(static fn ($capability): array => $capability->toArray(), $capabilities),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $capability = $this->capabilities->bySlug($slug);
        } catch (DomainException) {
            $capability = null;
        }

        if ($capability === null) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['data' => $capability->toArray()]);
    }
}
