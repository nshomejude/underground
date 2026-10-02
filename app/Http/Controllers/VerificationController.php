<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\VerificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/** The verification hub: where the member sees both verifications and their next action. */
final class VerificationController extends Controller
{
    public function index(Request $request, VerificationService $service): View
    {
        $user = $request->user();

        return view('verification.index', [
            'user' => $user,
            'emailVerified' => $user->hasVerifiedEmail(),
            'identity' => $service->latest($user, 'identity'),
            'company' => $service->latest($user, 'company'),
            'status' => $service->statusFor($user),
            'canStartIdentity' => $service->canStart($user, 'identity'),
            'canStartCompany' => $service->canStart($user, 'company'),
        ]);
    }
}
