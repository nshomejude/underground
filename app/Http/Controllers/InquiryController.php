<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfidentialInquiryRequest;
use Application\Engagement\Actions\SubmitConfidentialInquiry;
use Application\Engagement\DataTransferObjects\InquiryPayload;
use Domain\Engagement\ValueObjects\InterestArea;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * The "Start a confidential conversation" form: a classic server-rendered
 * lead-generation page for the Engagement context.
 */
final class InquiryController extends Controller
{
    public function __construct(private readonly SubmitConfidentialInquiry $submit) {}

    public function create(): View
    {
        return view('inquiries.create', [
            'interestAreas' => array_map(
                static fn (InterestArea $area): array => ['value' => $area->value, 'label' => $area->label()],
                InterestArea::cases(),
            ),
        ]);
    }

    public function store(StoreConfidentialInquiryRequest $request): RedirectResponse
    {
        $payload = InquiryPayload::fromArray($request->validated());

        try {
            $inquiry = ($this->submit)($payload);
        } catch (DomainException $exception) {
            return redirect()
                ->route('inquiries.create')
                ->withInput()
                ->withErrors(['brief' => $exception->getMessage()]);
        }

        return redirect()
            ->route('inquiries.create')
            ->with('reference', $inquiry->reference->value);
    }
}
