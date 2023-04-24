<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_id', 'candidate_index', 'q1', 'q2', 'q3',
        'q4', 'q5', 'course_header', 'course_key', 'marker_key', 'exam_date'
    ];
}
