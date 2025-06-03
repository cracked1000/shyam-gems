<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1($this->user->getEmailForVerification())]
        );

        return (new MailMessage)
            ->subject('Verify Your Shyam Gems Account')
            ->line('Welcome to Shyam Gems! Please click the button below to verify your email address.')
            ->action('Verify Email', $verificationUrl)
            ->line('If you didn’t create an account, no further action is required.');
    }
}