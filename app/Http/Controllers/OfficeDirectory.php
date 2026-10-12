<?php

declare(strict_types=1);

namespace App\Http\Controllers;

/**
 * Underground's departmental mailboxes, shared by the Contact and Global
 * Reach pages.
 *
 * The office network itself used to live here as a hardcoded all()
 * array. It is now Domain\Content\Entities\Office, seeded data exposed
 * through Application\Content\Queries\ListOffices — see that query (and
 * database/seeders/OfficeSeeder) for the office directory, and
 * App\Http\Controllers\Admin\OfficeAdminController for its admin CRUD.
 */
final class OfficeDirectory
{
    /**
     * @return array<string, string> label => mailbox
     */
    public static function departments(): array
    {
        return [
            'Partnerships' => 'partnerships@un-der.com',
            'Proposals' => 'proposals@un-der.com',
            'Projects' => 'projects@un-der.com',
            'Media' => 'media@un-der.com',
            'Policy' => 'policy@un-der.com',
            'Business' => 'business@un-der.com',
        ];
    }
}
