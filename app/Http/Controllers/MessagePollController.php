<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MessagePollController extends Controller
{
    public function __invoke(
        Request $request,
        Conversation $conversation,
        ConversationService $conversations,
        MessageService $messages,
    ): JsonResponse {
        $user = $request->user();
        $conversations->authorize($conversation, $user);

        $new = $messages->newerThan($conversation, max(0, (int) $request->query('after', 0)));
        $messages->markRead($conversation, $user);

        return response()->json([
            'messages' => $new->map(fn ($m) => MessageService::payload($m, $user))->values(),
            'seen_id' => $messages->lastSeenId($conversation, $user),
            'unread' => MessageService::unreadCount($user),
        ])->header('Cache-Control', 'no-store');
    }
}
