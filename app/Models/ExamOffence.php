<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamOffence extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'punishment',
        'duration'
    ];


}
