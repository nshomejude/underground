<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultationRequestRequest;
use Application\Consultation\Actions\SubmitConsultationRequest;
use Application\Consultation\DataTransferObjects\ConsultationRequestPayload;
use Domain\Consultation\ValueObjects\BudgetRange;
use Domain\Consultation\ValueObjects\ConsultationSector;
use Domain\Consultation\ValueObjects\MaturityStage;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * The Underground Consultation Desk: a free qualification form for
 * non-members, reviewed by staff before any paid consultation is offered.
 * Sibling to App\Http\Controllers\InquiryController (the "Start a
 * confidential conversation" mandate form) — this one is lighter-weight, has
 * no cost, and never requests confidential/sensitive documents.
 */
final class ConsultationController extends Controller
{
    public function __construct(private readonly SubmitConsultationRequest $submit) {}

    public function create(): View
    {
        return view('consultations.create', [
            'sectors' => array_map(
                static fn (ConsultationSector $sector): array => ['value' => $sector->value, 'label' => $sector->label()],
                ConsultationSector::cases(),
            ),
            'maturityStages' => array_map(
                static fn (MaturityStage $stage): array => ['value' => $stage->value, 'label' => $stage->label()],
                MaturityStage::cases(),
            ),
            'budgetRanges' => array_map(
                static fn (BudgetRange $range): array => ['value' => $range->value, 'label' => $range->label()],
                BudgetRange::cases(),
            ),
        ]);
    }

    public function store(StoreConsultationRequestRequest $request): RedirectResponse
    {
        $payload = ConsultationRequestPayload::fromArray($request->validated());

        try {
            $consultation = ($this->submit)($payload);
        } catch (DomainException $exception) {
            return redirect()
                ->route('consultations.create')
                ->withInput()
                ->withErrors(['project_description' => $exception->getMessage()]);
        }

        return redirect()
            ->route('consultations.create')
            ->with('reference', $consultation->reference->value);
    }
}
