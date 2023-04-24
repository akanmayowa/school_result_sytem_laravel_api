<?php

namespace App\Repositories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Model;

class MessageRepository extends BaseRepository
{

    protected $message;

    public function __construct(Message $message)
    {
        parent::__construct($message);
    }

    public function get()
    {
       return $this->message->with(['user'])->get();
    }

    public function create(array $data)
    {
        return $this->message->create($data);
    }


}
