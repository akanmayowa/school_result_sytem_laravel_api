<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BroadCastNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;
    public $tile;
    public $body;
    public $actionType;


    public function __construct($title, $body, $actionType = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->actionType = $actionType;
    }


    public function via($notifiable)
    {
        return ['mail','database','broadcast'];
    }



    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'body' =>  $this->body,
            'id' => auth()->user()->operator_id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ];
    }


    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'action_type' => $this->actionType,
            'type' => get_class($this),
        ];
    }

}
