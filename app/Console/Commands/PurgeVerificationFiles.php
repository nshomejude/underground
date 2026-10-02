<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\VerificationService;
use Illuminate\Console\Command;

final class PurgeVerificationFiles extends Command
{
    protected $signature = 'verification:purge';

    protected $description = 'Delete verification files past retention and mark lapsed approvals as expired';

    public function handle(VerificationService $service): int
    {
        $r = $service->purge();
        $this->info("Expired {$r['expired']}, purged {$r['purged']}, stripped {$r['stripped']}.");

        return self::SUCCESS;
    }
}
