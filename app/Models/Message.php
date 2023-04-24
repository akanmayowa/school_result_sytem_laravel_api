<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $appends = ['selfMessage'];
    protected $fillable = [
        'id',
        'operator_id',
        'reciever_id',
        'school_code',
        'message',
        'status',
        'title'
    ];



    public function getSelfMessageAttribute()
    {
        return $this->operator_id === auth()->user()->operator_id;
    }

    public function userMessage()
    {
        return $this->belongsTo(User::class,'operator_id', 'operator_id');
    }

}
