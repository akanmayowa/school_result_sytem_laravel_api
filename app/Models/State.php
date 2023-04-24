<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use League\CommonMark\Extension\Attributes\Node\Attributes;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name'
    ];

    public function trainingSchool()
    {
        return $this->belongsTo(TrainingSchool::class, 'state_id');
    }

}
