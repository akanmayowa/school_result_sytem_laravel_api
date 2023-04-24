<?php

namespace App\Notifications;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorAuthCodeNotification extends Notification
{
    use Queueable;
    private string $title;
    private string $body;

    public function __construct()
    {
    }

    public function via($notifiable)
    {
        return ['mail','database','broadcast'];
    }

    /**
     * @throws Exception
     */
    public function toMail($notifiable)
    {
            return (new MailMessage)
                ->line('Your two factor code is '.$notifiable->two_factor_code)
                ->line('The code will expire in 10 minutes')
                ->line('If you have not tried to login, ignore this message.');
    }


    public function toArray($notifiable)
    {
        return [
            'title' => "User Just Logged In",
            'body' => "Log In Notification",
            'id' => auth()->user()->user_id,
            'two_factor_code' =>  $notifiable->two_factor_code,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ];
    }


//    public function toDatabase($notifiable)
//    {
//        return [
//            'title' => "User Just Logged In",
//            'body' => "Log In Notification",
//            'id' => auth()->user()->user_id,
//            'two_factor_code' =>  $notifiable->two_factor_code,
//            'name' => auth()->user()->name,
//            'email' => auth()->user()->email,
//        ];
//    }
}
