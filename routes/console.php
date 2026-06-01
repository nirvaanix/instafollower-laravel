<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Instagram SaaS scheduled tasks
Schedule::command('instagram:sync-followers')->everyFifteenMinutes();
Schedule::command('instagram:refresh-tokens')->dailyAt('03:00');
