<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MessageReportRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

final class MessageReportController extends Controller
{
    public function __invoke(
        MessageReportRequest $request,
        Conversation $conversation,
        Message $message,
        ConversationService $conversations,
        MessageService $messages,
    ): JsonResponse|RedirectResponse {
        $user = $request->user();
        $conversations->authorize($conversation, $user);

        abort_unless($message->conversation_id === $conversation->id, 404);
        abort_if($message->sender_id === $user->id, 403, 'You cannot report your own message.');

        $messages->report($message, $user, $request->validated('reason'));

        $notice = 'Thank you. Your report has been sent to our team for review.';

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => $notice]);
        }

        return redirect()->route('messages.show', $conversation)->with('messaging_status', $notice);
    }
}
