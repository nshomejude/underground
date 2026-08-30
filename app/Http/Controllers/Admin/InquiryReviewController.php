<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Application\Engagement\Actions\TransitionInquiryStatus;
use Application\Engagement\Queries\ListInquiries;
use Domain\Engagement\ValueObjects\InquiryStatus;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The staff-facing review queue for confidential inquiries. Status moves
 * only through the legal states the aggregate defines (see
 * Domain\Engagement\ValueObjects\InquiryStatus::allowedTransitions()) — an
 * illegal move (e.g. Declined straight to Engaged) is rejected with a flash
 * error rather than a 500.
 */
final class InquiryReviewController extends Controller
{
    public function __construct(
        private readonly ListInquiries $list,
        private readonly TransitionInquiryStatus $transition,
    ) {}

    public function index(): View
    {
        return view('admin.inquiries.index', [
            'inquiries' => ($this->list)(),
        ]);
    }

    public function transition(Request $request, string $reference): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(InquiryStatus::class)],
        ]);

        try {
            ($this->transition)($reference, InquiryStatus::from($validated['status']));
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('status', sprintf(
            'Inquiry %s moved to "%s".',
            $reference,
            InquiryStatus::from($validated['status'])->label(),
        ));
    }
}
