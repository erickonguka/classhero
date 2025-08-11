<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends BaseVerifyEmail
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        
        // Choose template based on user role
        $template = match($notifiable->role) {
            'admin' => 'emails.verify-email-admin',
            'teacher' => 'emails.verify-email-teacher',
            default => 'emails.verify-email'
        };
        
        $subject = match($notifiable->role) {
            'admin' => 'Verify Your Admin Account - ClassHero',
            'teacher' => 'Verify Your Teacher Account - ClassHero',
            default => 'Verify Your Email Address - ClassHero'
        };

        return (new MailMessage)
            ->subject($subject)
            ->view($template, [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
            ]);
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}