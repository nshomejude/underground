<?php

declare(strict_types=1);

namespace Tests\Feature\Careers;

use Tests\TestCase;

final class CareersPageTest extends TestCase
{
    public function test_careers_page_renders_key_copy(): void
    {
        $response = $this->get(route('careers'));

        $response->assertOk();
        $response->assertSee('Work That Never Makes the Headlines.');
        $response->assertSee('Government &amp; Political Affairs', false);
        $response->assertSee('careers@underground.network');
        $response->assertSee(route('inquiries.create'), false);
    }
}
