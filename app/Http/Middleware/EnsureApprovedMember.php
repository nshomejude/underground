<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\MemberAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backs the `member.approved` alias: the network area is for signed-in members
 * with an approved membership and a verified email address. Everyone else is
 * sent back to their dashboard with an explanation.
 */
final class EnsureApprovedMember
{
    public function __construct(private readonly MemberAccess $access) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $this->access->canUseNetwork($user)) {
            return redirect()->route('account.show')->with(
                'status',
                'The member network is open to approved members with a verified email address.',
            );
        }

        return $next($request);
    }
}
