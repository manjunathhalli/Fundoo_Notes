<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailToCollab extends Notification
{
    use Queueable;

    public $email;
    public $title;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $email, string $title)
    {
        $this->email = $email;
        $this->title = $title;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url("/api/auth/login");
        return (new MailMessage)
                    ->line('('.$this->email. ') shared a note with you.')
                    ->line('NOTES:-' . $this->title)
                    ->action('Open in Fundoo Note', url($url))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
