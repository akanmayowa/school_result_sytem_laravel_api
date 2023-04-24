<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgetPasswordNotification extends Notification  implements ShouldQueue
{
    use Queueable;

    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }


    public function via($notifiable)
    {
        return ['mail','database','broadcast'];
    }


    public function toMail($notifiable)
    {
        $url = url(route('password.reset', ['token' => $this->data['token'], 'email' => $this->data['email']], false));
        return (new MailMessage)
                        ->subject(__('Reset Your Password!'))
                        ->line(__('You are receiving this email because we received a password reset request for your account.'))
                        ->action(__('Reset Password'), $url)
                        ->line(__('This password reset link will expire in :count minutes.',
                            ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
                        ->line(__('If you did not request a password reset, no further action is required.'));
    }

    public function toArray($notifiable)
    {
        return [
            'email' =>  $this->data['email'],
             'token' => $this->data['token']
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'read_at' => Carbon::now(),
            'email' =>  $this->data['email'],
            'token' => $this->data['token'],
        ];
    }

}
