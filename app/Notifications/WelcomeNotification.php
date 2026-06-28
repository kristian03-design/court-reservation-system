<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('dashboard');

        return (new MailMessage)
            ->subject('Welcome to CourtConnect!')
            ->view('emails.welcome', [
                'url' => $url,
                'name' => $notifiable->name,
            ]);
    }
}
