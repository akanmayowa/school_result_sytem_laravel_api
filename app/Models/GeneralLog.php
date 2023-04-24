<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'auth_id',
        'target',
        'log_message',
        'model',
    ];
}
