<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send notifications for scheduled tasks daily at 00:01
Schedule::command('tasks:send-scheduled-notifications')->dailyAt('00:01');

// Daily Report WhatsApp Reminder
// Senin-Jumat jam 16:00
Schedule::command('daily-report:send-reminders')
    ->weekdays()
    ->at('16:00')
    ->timezone('Asia/Jakarta');

// Sabtu jam 12:30
Schedule::command('daily-report:send-reminders')
    ->saturdays()
    ->at('12:30')
    ->timezone('Asia/Jakarta');