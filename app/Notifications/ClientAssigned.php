<?php

namespace App\Notifications;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientAssigned extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Client $client, private Project $project)
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
            ->subject('Cliente asignado para ' . $this->project->name)
            ->line("Se ha asignado al cliente {$this->client->name} para el proyecto '{$this->project->name}', para mas información entra al siguiente enlace.")
            ->action('Abrir enlace',
            route('projects.show', ['project' => $this->project])
            )
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
            'name' => $this->client->name,
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'client_id' => $this->client->id,
        ];
    }
}
