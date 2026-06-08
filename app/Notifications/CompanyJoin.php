<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyJoin extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Company $company)
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
        ->subject('Bienvenido a ' . $this->company->name)
            ->line("Has sido añadido a '{$this->company->name}'. Para conocer más detalles sobre la empresa entra al siguiente enlace.")
            ->action('Abrir enlace',
            route('companies.show', ['company' => $this->company])
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
            //
            'company_id' => $this->company->id,
            'name' => $this->company->name,
        ];
    }
}
