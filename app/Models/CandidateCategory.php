<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'category','description','id'
    ];
}
