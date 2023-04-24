<?php
namespace App\Channels;

use Illuminate\Support\Str;


class CustomDatabaseNotificationChannel{

    public function send($notifiable, $notification)
    {
        $data = $notification->toDatabase($notifiable);

        return $notifiable->routeNotificationFor('database')->create([
                'id' => Str::uuid()->toString(),
                'title' => $data['title'],
                'action_type' => $data['action_type'],
                'type' => $data['type'],
                'body' => $data['body'],
                'notifiable_id'=> auth()->id(),
                'notifiable_type' => get_class($notification),
                'data' => $data,
                'read_at' => null,
        ]);
    }

}
