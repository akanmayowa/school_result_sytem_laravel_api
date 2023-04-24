<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResentTwoFactorAuthCodeNotification extends Notification implements ShouldQueue
{
   use InteractsWithQueue,  SerializesModels;

    use Queueable;
    protected $user;
    public function __construct(User $user)
    {
        $this->afterCommit();
        $this->user = $user;

    }

    public function via($notifiable)
    {
        return ['mail','database','broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Your two factor code is '.$notifiable->two_factor_code)
            ->action('Verify Here', route('auth-verify.index'))
            ->line('The code will expire in 10 minutes')
            ->line('If you have not tried to login, ignore this message.');
    }


    public function toArray($notifiable)
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,

        ];
    }


//    public function toDatabase($notifiable)
//    {
//        return [
//            'title' => "Resent Authentication Notification",
//            'body' => "Resent Two Authentication Code",
//            'id' => $this->user->id,
//            'name' => $this->user->name,
//            'email' => $this->user->email,
//        ];
//    }
}
