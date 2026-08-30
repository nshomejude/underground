<?php

declare(strict_types=1);

namespace Interfaces\Http\Api\V1\Controllers;

use Application\Engagement\Actions\SubmitConfidentialInquiry;
use Application\Engagement\DataTransferObjects\InquiryPayload;
use Application\Engagement\Queries\TrackInquiry;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Interfaces\Http\Api\V1\Requests\SubmitInquiryRequest;

/**
 * The Engagement context's public contract: submit a confidential inquiry
 * and track it by its reference.
 */
final class InquiryController extends Controller
{
    public function __construct(
        private readonly SubmitConfidentialInquiry $submit,
        private readonly TrackInquiry $track,
    ) {}

    public function store(SubmitInquiryRequest $request): JsonResponse
    {
        $payload = InquiryPayload::fromArray($request->validated());

        try {
            $inquiry = ($this->submit)($payload);
        } catch (DomainException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json(['data' => $inquiry->toArray()], 201);
    }

    public function show(string $reference): JsonResponse
    {
        try {
            $inquiry = ($this->track)($reference);
        } catch (DomainException) {
            $inquiry = null;
        }

        if ($inquiry === null) {
            return response()->json(['message' => 'Confidential inquiry not found.'], 404);
        }

        return response()->json(['data' => $inquiry->toArray()]);
    }
}
