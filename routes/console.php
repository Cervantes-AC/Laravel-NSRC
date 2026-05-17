<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('attendance:sync-google-sheets')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('backup:run --type=database')
    ->weeklyOn(1, '2:00')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('backup:run --type=files')
    ->sundays()->at('3:00')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('backup:run --type=full')
    ->monthlyOn(1, '4:00')
    ->withoutOverlapping()
    ->runInBackground();
