<?php

namespace App\Notifications;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeleteActivity extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Activity $activity)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('Actividad eliminada')
            ->line("Se ha eliminado la actividad '{$this->activity->name}', regresa al tablón de actividades para seguir creando nuevas actividades.")
            ->action('Ver tablón de actividades',
            route('activities-board.show', ['project' => $this->activity->project_id]))
            ->line('Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'activity_id' => $this->activity->id,
            'name' => $this->activity->name,
            'project_id' => $this->activity->project_id,
            'user_id' => $this->activity->by_user_id
        ];
    }
}
