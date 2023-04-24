<?php

namespace App\Events;

use App\Models\Message;
use App\Models\TrainingSchool;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSentToSelectedTrainingSchoolEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ? Message $message;
    public  ? TrainingSchool $training_school;

    public function __construct(Message $message, TrainingSchool $training_school)
    {
        $this->message = $message;
        $this->training_school = $training_school;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('message-channel'. $this->training_school->school_code);
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
