<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientEdited extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Client $client)
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
            ->line("La información del cliente '{$this->client->name}' ha sido modificada.")
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
            //
            'client_id' => $this->client->id,
            'name' => $this->client->name,
        ];
    }
}
