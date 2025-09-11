<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyEmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $notifiable->getCompanyEmailVerificationUrl();

        return (new MailMessage)
            ->subject('Verify Your Company Email Address')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('Please verify your company email address by clicking the button below.')
            ->line('This verification is required to ensure the security of your account and to enable certain features.')
            ->action('Verify Company Email Address', $verificationUrl)
            ->line('If you did not request this verification, no further action is required.')
            ->line('This verification link will expire in 60 minutes.')
            ->salutation('Best regards, ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'company_email_verification',
            'message' => 'Company email verification sent to ' . $notifiable->company_email,
        ];
    }
}
