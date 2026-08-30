<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\SiteSetting;

interface SiteSettingRepository
{
    public function current(): SiteSetting;

    /**
     * Overwrite the single settings row with the given content. SiteSetting
     * is a singleton — there is exactly one row — so there is no
     * create/list/delete here, only replacing the current one.
     */
    public function update(SiteSetting $setting): void;
}
