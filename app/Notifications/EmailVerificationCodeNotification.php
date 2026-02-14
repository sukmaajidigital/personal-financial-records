<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $code,
    ) {}

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
        return (new MailMessage)
            ->subject('Kode Verifikasi Email — ' . config('app.name'))
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Gunakan kode berikut untuk memverifikasi alamat email Anda:')
            ->line("**{$this->code}**")
            ->line('Kode ini berlaku selama 15 menit.')
            ->line('Jika Anda tidak membuat akun, abaikan email ini.')
            ->salutation('Salam, ' . config('app.name'));
    }
}
