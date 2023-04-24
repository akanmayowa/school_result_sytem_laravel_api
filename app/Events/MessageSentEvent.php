<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;


    public function __construct(Message $message, User $user)
    {
        $this->message = $message;
        $this->user = $user;
    }


    public function broadcastOn()
    {
                return new PrivateChannel('message-channel'.$this->user->id);
    }

    public function broadcastWith()
    {
        $this->message->load(['userMessage']);
        return [
            'message' => array_merge($this->message->toArray(), [
                'selfMessage' => false
            ])
        ];
    }
}
