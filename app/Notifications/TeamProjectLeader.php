<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamProjectLeader extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Project $project)
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
        ->subject('Asignación de lider de equipo')
            ->line("Has sido asignado como el lider de equipo para el proyecto '{$this->project->name}', para conocer más información haz click en el siguiente enlace.")
            ->action('Ver proyecto',
            route('projects.show', ['project' => $this->project]))
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
            'project_id' => $this->project->id,
            'user_id' => $this->project->by_user_id,
            'name' => $this->project->name,
        ];
    }
}
