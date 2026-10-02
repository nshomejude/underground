<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/** Read-only moderation overview of member connections and requests. */
final class NetworkAdminController extends Controller
{
    public function index(Request $request): View
    {
        $status = in_array($request->query('status'), ['pending', 'accepted', 'declined', 'blocked'], true)
            ? $request->query('status') : null;

        $counts = Connection::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $connections = Connection::query()
            ->with(['requester', 'addressee'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.network.index', [
            'counts' => [
                'total' => (int) $counts->sum(),
                'pending' => (int) ($counts['pending'] ?? 0),
                'accepted' => (int) ($counts['accepted'] ?? 0),
                'declined' => (int) ($counts['declined'] ?? 0),
                'blocked' => (int) ($counts['blocked'] ?? 0),
            ],
            'collaborations' => Connection::query()->where('kind', 'collaborate')->count(),
            'last7' => Connection::query()->where('created_at', '>=', now()->subDays(7))->count(),
            'connections' => $connections,
            'status' => $status,
        ]);
    }

    public function show(Connection $connection): View
    {
        $connection->load(['requester.profile', 'addressee.profile', 'conversation']);

        return view('admin.network.show', ['connection' => $connection]);
    }
}
