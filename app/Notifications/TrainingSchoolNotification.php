<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TrainingSchoolNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;
    use Notifiable;

    protected $training_school;

    public function __construct($training_school)
    {
        $this->afterCommit();
        $this->training_school = $training_school;
    }


    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }


    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('New Training School created at'."  ".$this->training_school->created_at )
                    ->line('Training School Name:'."  ".$this->training_school->school_name  )
                    ->line('Training School Code:'."  ".$this->training_school->school_code)
                    ->line('Training School created by:'."  ".auth()->user()->name);
    }



    public function toArray($notifiable)  ///specify what model property is displayed vis broadcast
    {
        return [
             'school_name' => $this->training_school->school_name,
              'school_code' => $this->training_school->school_code,
              'creation_date' => $this->training_school->created_at,
        ];
    }


    public function toDatabase($notifiable) ///specify what model property is stores in the database
    {
        return [
            'school_name' => $this->training_school->school_name,
            'school_code' => $this->training_school->school_code,
            'creation_at' => $this->training_school->created_at,
            'body' => $this->body,
            'title' => $this->title,
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
//            'message' => "$this->message (User $notifiable->id)"
            'school_name' => $this->training_school->school_name,
            'school_code' => $this->training_school->school_code,
            'creation_at' => $this->training_school->created_at,

        ]);
    }

}
