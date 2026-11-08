<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Activity;
use App\Models\User;

class CompleteActivity extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Activity $activity, public User $sender)
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
        ->subject('Actividad completada')
            ->line("{$this->sender->name} {$this->sender->last_name} marco como completada la actividad '{$this->activity->name}' espera a la siguiente asignación.")
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
            'sender_name' => $this->sender->name,
            'sender_last_name' => $this->sender->last_name,
            'user_id' => $this->activity->by_user_id
        ];
    }
}
