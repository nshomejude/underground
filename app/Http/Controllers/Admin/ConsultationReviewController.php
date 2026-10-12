<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Application\Consultation\Actions\TransitionConsultationStatus;
use Application\Consultation\Queries\ListConsultationRequests;
use Domain\Consultation\Repositories\ConsultationRequestRepository;
use Domain\Consultation\ValueObjects\ConsultationReference;
use Domain\Consultation\ValueObjects\ConsultationStatus;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The staff-facing review queue for consultation requests. Status moves
 * only through the legal states the aggregate defines (see
 * Domain\Consultation\ValueObjects\ConsultationStatus::allowedTransitions())
 * — an illegal move is rejected with a flash error rather than a 500.
 * Mirrors App\Http\Controllers\Admin\InquiryReviewController's exact
 * pattern.
 */
final class ConsultationReviewController extends Controller
{
    public function __construct(
        private readonly ListConsultationRequests $list,
        private readonly ConsultationRequestRepository $requests,
        private readonly TransitionConsultationStatus $transition,
    ) {}

    public function index(): View
    {
        return view('admin.consultations.index', [
            'consultations' => ($this->list)(),
        ]);
    }

    public function show(string $reference): View
    {
        $consultation = $this->requests->findByReference(ConsultationReference::fromString($reference));

        abort_if($consultation === null, 404);

        return view('admin.consultations.show', ['consultation' => $consultation]);
    }

    public function transition(Request $request, string $reference): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(ConsultationStatus::class)],
        ]);

        try {
            ($this->transition)($reference, ConsultationStatus::from($validated['status']));
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('status', sprintf(
            'Consultation request %s moved to "%s".',
            $reference,
            ConsultationStatus::from($validated['status'])->label(),
        ));
    }
}
