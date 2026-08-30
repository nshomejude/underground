<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\Narrative;

interface NarrativeRepository
{
    public function current(): Narrative;

    /**
     * Overwrite the single narrative row with the given content. Narrative is
     * a singleton — there is exactly one authored copy — so there is no
     * create/list/delete here, only replacing the current one.
     */
    public function update(Narrative $narrative): void;
}
