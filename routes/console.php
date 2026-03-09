<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
  $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// use Illuminate\Support\Facades\Schedule;
// Schedule::command(SendWeeklyReports::class)
//     ->weekly()               // Frequency: once a week
//     ->mondays()               // Constraint: on Monday
//     ->at('09:00')             // Constraint: at 9am
//     ->onOneServer()           // Server: run on only one server
//     ->withoutOverlapping()    // Safety: don't run if previous is still running
//     ->name('send-weekly-reports'); // Name for tracking (important for onOneServer)
