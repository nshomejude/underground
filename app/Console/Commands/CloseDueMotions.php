<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\MotionService;
use Illuminate\Console\Command;

final class CloseDueMotions extends Command
{
    protected $signature = 'votes:close-due';

    protected $description = 'Close motions past their closing time, and send pending voting notifications';

    public function handle(MotionService $motions): int
    {
        $r = $motions->runScheduledPass();

        $this->info("Closed {$r['closed']}; opening notices {$r['opened']}; reminders {$r['reminders']}; outcome notices {$r['outcomes']}.");

        return self::SUCCESS;
    }
}
