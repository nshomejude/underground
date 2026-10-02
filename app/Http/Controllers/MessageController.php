<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ConversationService;
use App\Services\MemberAccess;
use App\Services\MessageService;
use Application\Membership\Queries\ListMembershipTiers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class MessageController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversations,
        private readonly MessageService $messages,
        private readonly MemberAccess $access,
        private readonly ListMembershipTiers $tiers,
    ) {}

    public function index(Request $request): View
    {
        return view('messages.index', ['conversations' => $this->conversations->listFor($request->user())]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $viewer = $request->user();
        $this->conversations->authorize($conversation, $viewer);

        $this->messages->markRead($conversation, $viewer);

        /** @var User $other */
        $other = $conversation->link->otherParty($viewer)->loadMissing('profile');
        $slug = $this->access->tierSlug($other);

        return view('messages.show', [
            'conversations' => $this->conversations->listFor($viewer),
            'conversation' => $conversation,
            'other' => $other,
            'otherName' => ConversationService::nameOf($other),
            'tierName' => $slug !== null ? $this->tiers->bySlug($slug)?->name : null,
            'profileSlug' => $other->profile?->slug,
            'thread' => $this->messages->recent($conversation)->map(fn ($m) => MessageService::payload($m, $viewer))->all(),
            'seenId' => $this->messages->lastSeenId($conversation, $viewer),
        ]);
    }
}
