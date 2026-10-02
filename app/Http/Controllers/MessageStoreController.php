<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MessageStoreRequest;
use App\Models\Conversation;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

final class MessageStoreController extends Controller
{
    public function __invoke(
        MessageStoreRequest $request,
        Conversation $conversation,
        ConversationService $conversations,
        MessageService $messages,
    ): JsonResponse|RedirectResponse {
        $user = $request->user();
        $conversations->authorize($conversation, $user);

        $message = $messages->send($conversation, $user, $request->validated('body'));

        if ($request->expectsJson()) {
            return response()->json(['message' => MessageService::payload($message, $user)], 201);
        }

        return redirect()->to(route('messages.show', $conversation).'#composer');
    }
}
