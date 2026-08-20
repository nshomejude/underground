<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\Narrative;

interface NarrativeRepository
{
    public function current(): Narrative;
}
