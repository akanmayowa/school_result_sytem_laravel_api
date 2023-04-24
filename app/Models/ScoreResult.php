<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_code',
        'candidate_index',
        'course_average',
        'course_key',
        'course_header',
        'course_unit',
        'year',
        'new'
    ];

    public function courseScoreResult()
    {
        return $this->belongsTo(CourseModule::class, 'course_key', 'course_key');
    }

    public function courseScoreResults()
    {
        return $this->hasMany(CourseModule::class, 'course_key', 'course_key');
    }

    public function scoreResultForSchool()
    {
        return $this->belongsTo(TrainingSchool::class,'school_code','school_code');
    }

    public function course()
    {
        return $this->belongsTo(CourseModule::class, 'course_key', 'course_key');
    }

}
