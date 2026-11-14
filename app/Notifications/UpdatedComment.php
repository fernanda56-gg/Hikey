<?php

namespace App\Notifications;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdatedComment extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Activity $activity, private User $author)
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('Comentario editado')
            ->line("'{$this->author->name} {$this->author->last_name}' editó su comentario en la actividad
            '{$this->activity->name}'.")
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
            'author_name' => $this->author->name,
            'author_last_name' => $this->author->last_name,
            'user_id' => $this->activity->by_user_id
        ];
    }
}
