<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\TwoFactorAuthCodeNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class TwoFactorAuthCodeNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public ? User $user = null;
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    public function handle()
    {
        $this->user->notify(new TwoFactorAuthCodeNotification());
        Log::info('Dispatched order ' . $this->user);
        throw new \Exception("I am throwing this exception", 1);
    }
}
