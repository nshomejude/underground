<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRequest;
use Domain\Content\Entities\Event;
use Domain\Content\Repositories\EventRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for Event — the /events forum listing. See
 * Domain\Content\Entities\Event.
 */
final class EventAdminController extends Controller
{
    public function __construct(private readonly EventRepository $events) {}

    public function index(): View
    {
        return view('admin.events.index', ['events' => $this->events->all()]);
    }

    public function create(): View
    {
        return view('admin.events.create');
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->events->save(new Event(
            slug: Slug::fromString($data['slug']),
            name: $data['name'],
            date: Carbon::parse($data['date']),
            location: $data['location'],
            description: $data['description'],
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.events.index')->with('status', 'Event created.');
    }

    public function edit(string $event): View
    {
        $found = $this->events->findBySlug(Slug::fromString($event));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known event.', $event));
        }

        return view('admin.events.edit', ['event' => $found]);
    }

    public function update(string $event, EventRequest $request): RedirectResponse
    {
        $original = $this->events->findBySlug(Slug::fromString($event));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known event.', $event));
        }

        $data = $request->validated();

        $this->events->save(
            new Event(
                slug: Slug::fromString($data['slug']),
                name: $data['name'],
                date: Carbon::parse($data['date']),
                location: $data['location'],
                description: $data['description'],
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.events.index')->with('status', 'Event updated.');
    }

    public function destroy(string $event): RedirectResponse
    {
        $this->events->delete(Slug::fromString($event));

        return redirect()->route('admin.events.index')->with('status', 'Event removed.');
    }
}
