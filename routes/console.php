<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('invoices:mark-overdue')->hourly();
Schedule::command('invoices:send-schedule-reminders')->dailyAt('08:00');
Schedule::command('expenses:bump-period')
    ->dailyAt('00:01')
    ->timezone(config('app.timezone'));
