<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule auto-update program status every hour
Schedule::command('program:update-status')->hourly();

// Schedule audit trail cleanup daily at midnight
Schedule::command('audit-trail:cleanup --days=30')->daily();
