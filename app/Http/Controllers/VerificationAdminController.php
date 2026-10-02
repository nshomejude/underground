<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\VerificationInfoRequest;
use App\Http\Requests\VerificationRejectRequest;
use App\Models\CompanyVerification;
use App\Models\IdentityVerification;
use App\Services\VerificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Staff review queue for identity and company verifications. */
final class VerificationAdminController extends Controller
{
    public function __construct(private readonly VerificationService $service) {}

    public function index(Request $request): View
    {
        $type = in_array($request->query('type'), ['identity', 'company'], true) ? $request->query('type') : null;
        $status = $request->query('status', 'pending');
        $search = trim((string) $request->query('q', ''));
        $order = $request->query('order') === 'newest' ? 'desc' : 'asc';

        $rows = collect();
        foreach (['identity' => IdentityVerification::class, 'company' => CompanyVerification::class] as $kind => $model) {
            if ($type !== null && $type !== $kind) {
                continue;
            }
            $query = $model::query()->with('user');
            match ($status) {
                'pending' => $query->whereIn('status', ['submitted', 'in_review']),
                'all' => $query->where('status', '!=', 'draft'),
                default => $query->where('status', $status),
            };
            if ($search !== '') {
                $query->where(function ($q) use ($search, $kind): void {
                    $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                    $kind === 'company'
                        ? $q->orWhere('company_name', 'like', "%{$search}%")->orWhere('registration_number', 'like', "%{$search}%")
                        : $q->orWhere('full_name_on_document', 'like', "%{$search}%");
                });
            }
            foreach ($query->get() as $row) {
                $rows->push(['kind' => $kind, 'row' => $row, 'at' => $row->submitted_at ?? $row->created_at]);
            }
        }
        $rows = $order === 'asc' ? $rows->sortBy('at')->values() : $rows->sortByDesc('at')->values();

        return view('admin.verifications.index', [
            'rows' => $rows, 'type' => $type, 'status' => $status, 'q' => $search, 'order' => $order === 'asc' ? 'oldest' : 'newest',
        ]);
    }

    public function show(Request $request, string $kind, int $id): View
    {
        $row = $this->find($kind, $id);
        if ($row->user_id !== $request->user()->id) {
            $this->service->markInReview($row, $request->user());
            $row->refresh();
        }

        return view('admin.verifications.show', [
            'kind' => $kind, 'row' => $row->load('user', 'reviewer'), 'files' => $this->service->filesOf($row),
            'hints' => $this->service->hints($row), 'events' => $row->events()->with('actor')->get(),
            'own' => $row->user_id === $request->user()->id,
            'reasons' => config('verification.rejection_reasons'),
            'dob' => $row instanceof IdentityVerification ? $row->dateOfBirth() : null,
        ]);
    }

    public function approve(Request $request, string $kind, int $id): RedirectResponse
    {
        $request->validate(['notes' => ['nullable', 'string', 'max:3000']]);
        $this->service->approve($this->find($kind, $id), $request->user(), $request->input('notes'));

        return redirect()->route('admin.verifications.index')->with('status', 'Verification approved and the member was notified.');
    }

    public function reject(VerificationRejectRequest $request, string $kind, int $id): RedirectResponse
    {
        $this->service->reject($this->find($kind, $id), $request->user(), $request->string('reason')->toString(), $request->input('message'), $request->input('notes'));

        return redirect()->route('admin.verifications.index')->with('status', 'Verification rejected and the member was notified.');
    }

    public function requestInfo(VerificationInfoRequest $request, string $kind, int $id): RedirectResponse
    {
        $this->service->requestInfo($this->find($kind, $id), $request->user(), $request->string('message')->toString());

        return redirect()->route('admin.verifications.show', [$kind, $id])->with('status', 'Request sent to the member.');
    }

    public function notes(Request $request, string $kind, int $id): RedirectResponse
    {
        $request->validate(['notes' => ['nullable', 'string', 'max:3000']]);
        $row = $this->find($kind, $id);
        abort_if($row->user_id === $request->user()->id, 403);
        $this->service->saveNotes($row, $request->user(), $request->input('notes'));

        return back()->with('status', 'Notes saved.');
    }

    private function find(string $kind, int $id): IdentityVerification|CompanyVerification
    {
        abort_unless(in_array($kind, ['identity', 'company'], true), 404);

        return $this->service->model($kind)::query()->where('status', '!=', 'draft')->findOrFail($id);
    }
}
