<?php

declare(strict_types=1);

namespace Application\Engagement\Actions;

use Application\Engagement\DataTransferObjects\InquiryPayload;
use Domain\Engagement\Entities\ConfidentialInquiry;
use Domain\Engagement\Events\InquirySubmitted;
use Domain\Engagement\Repositories\InquiryRepository;
use Illuminate\Contracts\Events\Dispatcher;

final readonly class SubmitConfidentialInquiry
{
    public function __construct(
        private InquiryRepository $inquiries,
        private Dispatcher $events,
    ) {}

    public function __invoke(InquiryPayload $payload): ConfidentialInquiry
    {
        $inquiry = ConfidentialInquiry::submit(
            name: $payload->name,
            organisation: $payload->organisation,
            email: $payload->email,
            phone: $payload->phone,
            country: $payload->country,
            interest: $payload->interest,
            brief: $payload->brief,
        );

        $this->inquiries->save($inquiry);

        $this->events->dispatch(new InquirySubmitted($inquiry));

        return $inquiry;
    }
}
