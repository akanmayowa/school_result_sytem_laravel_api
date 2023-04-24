<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CandidateIncourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_index','course_header','school_code','first_semester_score',
        'second_semester_score','third_semester_score','operator_id',
        'total_score','average_score','exam_id','new'
    ];


    public function CourseModuleForCandidateInCourse(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(  CourseHeader::class, 'header_key', 'course_header');
    }


    public function trainingSchoolCandidateIncourse()
    {
        return $this->belongsTo(TrainingSchool::class, 'school_code', 'school_code');
    }



}
