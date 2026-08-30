<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListEvents;
use Illuminate\Contracts\View\View;

/**
 * Invitation-only forums the firm convenes or hosts. Status (past vs
 * upcoming) is derived from the current date rather than hardcoded, so
 * the page stays correct as time passes without a code change. Content
 * is admin-editable — see Admin\EventAdminController.
 */
final class EventController extends Controller
{
    public function __construct(private readonly ListEvents $events) {}

    public function index(): View
    {
        return view('events.index', [
            'events' => array_map(
                static fn ($event) => $event->toArray(),
                ($this->events)(),
            ),
        ]);
    }
}
