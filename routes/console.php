<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// * comando para mandar la notificación de actividades
Schedule::command('activities:send-activity-deadline-reminder')
    ->dailyAt('08:00')
    ->timezone('America\Mexico_city');
