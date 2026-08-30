<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\Event;
use Domain\Content\Repositories\EventRepository;

final readonly class ListEvents
{
    public function __construct(private EventRepository $events) {}

    /** @return list<Event> */
    public function __invoke(): array
    {
        return $this->events->all();
    }
}
