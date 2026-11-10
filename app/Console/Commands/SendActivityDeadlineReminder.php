<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Notifications\DueDateActivity;
use Illuminate\Console\Command;
use Notification;

class SendActivityDeadlineReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:send-activity-deadline-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aviso a los responsables de que las actividades vencen mañana.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $activities = Activity::with('users', 'project') // ? relaciones del modelo Activity
            ->whereDate('due_date', now()->addDay()->toDateString())
            ->whereIn('status', ['Disponible', 'En progreso']) // ? solo advertirá a las actividades de estos estatus
            ->get();

        $this->info('Fecha buscada: ' . now()->addDay()->toDateString());
        $this->info('Actividades encontradas: ' . $activities->count());

        foreach ($activities as $activity) {
            Notification::send($activity->users, new DueDateActivity($activity));
        }

        $this->info("Actividades notificadas: {$activities->count()}");
        return self::SUCCESS;
    }
}
