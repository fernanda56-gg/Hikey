<?php

namespace App\Notifications;

use App\Models\Activity;
use App\Models\ActivityItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActionCompleted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private ActivityItem $item, private User $user)
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
        ->subject('Acción completada')
            ->line("{$this->user->name}, ha completado la acción {$this->item->description}, regresa al tablón de actividades y continua con las demás acciones.")
            ->action('Ver tablón de actividades',
            route('activities-board.show', ['project' => $this->item->activity->project_id]))
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
            'activityItem_id' => $this->item->activity->id,
            'name' => $this->item->description,
            'activity_id' => $this->item->activity->project_id,
            'activity_name' => $this->item->activity->project->name,
            'user_id' => $this->item->activity->by_user_id
        ];
    }
}
