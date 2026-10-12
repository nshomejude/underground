<?php

declare(strict_types=1);

namespace Application\Consultation\Actions;

use Application\Consultation\DataTransferObjects\ConsultationRequestPayload;
use Domain\Consultation\Entities\ConsultationRequest;
use Domain\Consultation\Events\ConsultationRequestSubmitted;
use Domain\Consultation\Repositories\ConsultationRequestRepository;
use Illuminate\Contracts\Events\Dispatcher;

final readonly class SubmitConsultationRequest
{
    public function __construct(
        private ConsultationRequestRepository $requests,
        private Dispatcher $events,
    ) {}

    public function __invoke(ConsultationRequestPayload $payload): ConsultationRequest
    {
        $request = ConsultationRequest::submit(
            name: $payload->name,
            organisation: $payload->organisation,
            role: $payload->role,
            email: $payload->email,
            country: $payload->country,
            sector: $payload->sector,
            maturityStage: $payload->maturityStage,
            projectDescription: $payload->projectDescription,
            needDescription: $payload->needDescription,
            budgetRange: $payload->budgetRange,
            hasSupportingDocuments: $payload->hasSupportingDocuments,
        );

        $this->requests->save($request);

        $this->events->dispatch(new ConsultationRequestSubmitted($request));

        return $request;
    }
}
