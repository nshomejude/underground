<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Messaging: email members about messages left unread for 10 minutes.
Schedule::command('messages:notify-unread')->everyFiveMinutes()->withoutOverlapping();

// Verification retention: purge old rejected/expired/withdrawn files, expire lapsed approvals.
Schedule::command('verification:purge')->daily()->at('03:15')->withoutOverlapping();

// Voting: close due motions and send pending voting notifications.
Schedule::command('votes:close-due')->everyFiveMinutes()->withoutOverlapping();
