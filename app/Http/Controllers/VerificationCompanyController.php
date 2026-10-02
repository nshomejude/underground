<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\VerificationCompanyDetailsRequest;
use App\Http\Requests\VerificationCompanyDocumentsRequest;
use App\Models\CompanyVerification;
use App\Services\VerificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Company verification: consent, company details, documents, review, status.
 * Choice: any signed-in member with a verified email may submit (an approved
 * organisation membership is not required); it only adds a badge.
 */
final class VerificationCompanyController extends Controller
{
    public function __construct(private readonly VerificationService $service) {}

    public function start(Request $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.index');
        }
        abort_unless($this->service->active($request->user(), 'company') !== null || $this->service->canStart($request->user(), 'company'), 403);
        $this->service->startDraft($request->user(), 'company');

        return redirect()->route('verification.company.show');
    }

    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.index');
        }

        $row = $this->service->active($user, 'company');
        if ($row === null) {
            $latest = $this->service->latest($user, 'company');

            return $latest === null || $latest->status === 'withdrawn'
                ? redirect()->route('verification.index')
                : view('verification.company.status', $this->statusData($latest));
        }
        if (! $row->isEditable()) {
            return view('verification.company.status', $this->statusData($row));
        }

        $reached = $this->reachedStep($row);
        $step = max(1, min((int) $request->query('step', $reached), $reached));
        $data = ['row' => $row, 'step' => $step, 'service' => $this->service, 'files' => $this->service->filesOf($row)];
        if ($step === 4) {
            $data['checks'] = $this->service->preChecks($row);
            $data['missing'] = $this->service->missingCompanyDocuments($row);
        }

        return view('verification.company.flow', $data);
    }

    public function consent(Request $request): RedirectResponse
    {
        $request->validate(['consent' => ['accepted']], ['consent.accepted' => 'Please confirm you have read and agree to continue.']);
        $this->service->saveConsent($this->editable($request), $request->user());

        return redirect()->route('verification.company.show', ['step' => 2]);
    }

    public function details(VerificationCompanyDetailsRequest $request): RedirectResponse
    {
        $row = $this->editable($request);
        abort_if($row->consent_at === null, 403);
        $this->service->saveCompanyDetails($row, $request->validated());

        return $request->boolean('save_exit')
            ? redirect()->route('verification.index')->with('status', 'Draft saved. You can continue any time.')
            : redirect()->route('verification.company.show', ['step' => 3]);
    }

    public function documents(VerificationCompanyDocumentsRequest $request): RedirectResponse
    {
        $row = $this->editable($request);
        abort_if($row->registration_number === null || $row->company_name === '', 403);

        foreach (array_keys(config('network.company_document_types')) as $type) {
            if ($request->hasFile($type)) {
                $this->service->storeFile($row, $type, $request->file($type));
            }
        }

        $row->refresh();
        if ($this->service->missingCompanyDocuments($row) !== []) {
            return redirect()->route('verification.company.show', ['step' => 3])
                ->withErrors(['registration_certificate' => 'The registration certificate and proof of address are required.']);
        }
        $row->forceFill(['last_step' => max((int) $row->last_step, 4)])->save();

        return $request->boolean('save_exit')
            ? redirect()->route('verification.index')->with('status', 'Draft saved. You can continue any time.')
            : redirect()->route('verification.company.show', ['step' => 4]);
    }

    public function removeFile(Request $request, string $slot): RedirectResponse
    {
        abort_unless(array_key_exists($slot, config('network.company_document_types')), 404);
        $row = $this->editable($request);
        $this->service->removeFile($row, $slot);
        $row->forceFill(['last_step' => min((int) $row->last_step, 3)])->save();

        return redirect()->route('verification.company.show', ['step' => 3])->with('status', 'File removed.');
    }

    public function submit(Request $request): RedirectResponse
    {
        $request->validate(['checklist' => ['accepted']], ['checklist.accepted' => 'Please tick the confirmation to submit.']);
        $this->service->submit($this->editable($request), $request->user());

        return redirect()->route('verification.company.show')->with('status', 'Submitted. Our team will review your documents.');
    }

    public function withdraw(Request $request): RedirectResponse
    {
        $row = $this->service->active($request->user(), 'company');
        abort_if($row === null, 404);
        $this->service->withdraw($row, $request->user());

        return redirect()->route('verification.index')->with('status', 'Your submission was withdrawn and your files were deleted.');
    }

    private function editable(Request $request): CompanyVerification
    {
        abort_unless($request->user()->hasVerifiedEmail(), 403);
        $row = $this->service->active($request->user(), 'company');
        abort_if($row === null || ! $row->isEditable(), 403, 'There is no draft to edit.');

        return $row;
    }

    private function reachedStep(CompanyVerification $row): int
    {
        if ($row->consent_at === null) {
            return 1;
        }
        if ($row->registration_number === null || $row->last_step < 3) {
            return 2;
        }

        return $this->service->missingCompanyDocuments($row) === [] && $row->last_step >= 4 ? 4 : 3;
    }

    /** @return array<string, mixed> */
    private function statusData(CompanyVerification $row): array
    {
        return ['row' => $row, 'files' => $this->service->filesOf($row), 'service' => $this->service];
    }
}
