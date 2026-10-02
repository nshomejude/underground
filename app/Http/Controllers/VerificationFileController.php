<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\VerificationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Serves private verification files to the owner or to staff admins only. */
final class VerificationFileController extends Controller
{
    public function show(Request $request, VerificationService $service, string $kind, int $id, string $slot): Response
    {
        abort_unless(in_array($kind, ['identity', 'company'], true), 404);

        $row = $service->model($kind)::query()->findOrFail($id);
        $user = $request->user();
        abort_unless($user !== null && ($row->user_id === $user->id || $user->is_admin), 403);

        $file = $service->filesOf($row)[$slot] ?? null;
        abort_if($file === null || ! $service->disk()->exists($file['path']), 404);

        $inline = in_array($file['mime'], array_keys(VerificationService::MIME_EXTENSIONS), true);
        $name = preg_replace('/[^\w.-]+/', '_', $file['name']) ?: 'document';

        return response($service->disk()->get($file['path']), 200, [
            'Content-Type' => $file['mime'],
            'Content-Disposition' => ($inline ? 'inline' : 'attachment').'; filename="'.$name.'"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self'; style-src 'unsafe-inline'; sandbox",
        ]);
    }
}
