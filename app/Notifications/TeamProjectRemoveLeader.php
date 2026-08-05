<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamProjectRemoveLeader extends Notification
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
        ->subject('Lider de equipo removido')
            ->line("Ha sido revocado tu rol como lider de equipo para el proyecto '{$this->project->name}', continuaras trabajando en el proyecto pero otro integrante tomara la posición de lider de equipo.")
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
