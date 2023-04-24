<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreMarkerOne extends Model
{
    use HasFactory;

    protected $fillable = [
    	'course_header',
        'course_key',
        'exam_id',
        'candidate_index',
        'q1',
        'q2',
        'q3',
        'q4',
        'q5',
        'total_score',
        'operator_id',
        'school_code',
        'status',
        'new'
    ];


    public function CourseModuleForScoreMarkerOne(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(  CourseModule::class, 'course_key', 'course_key');
    }

    public function courseModules(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(  CourseModule::class, 'course_key', 'course_key');
    }

    public function school()
    {
        return $this->belongsTo(TrainingSchool::class, 'school_code', 'school_code');
    }
}
