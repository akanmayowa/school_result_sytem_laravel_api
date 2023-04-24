<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamOffender extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_index',
        'course_header',
        'registration_date',
        'exam_date',
        'exam_offence_id',
        'duration',
        'comment',
        'school_code',
        'status',
    ];


    public function getModelNamespace()
    {
        return 'App\Models\ExamOffender';
    }

    public function candidateIndexForExamOffender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CandidateIndexing::class, 'candidate_index', 'candidate_index');
    }

    public function candidateIndexForExamOffenders(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CandidateIndexing::class, 'school_code', 'school_code');
    }

    public function examOffence()
    {
        return $this->hasMany(ExamOffence::class, 'id', 'exam_offence_id');
    }

    public function singleOffence()
    {
        return $this->hasOne(ExamOffence::class, 'id', 'exam_offence_id');
    }

    public function schoolForOffenders()
    {
        return $this->belongsTo(TrainingSchool::class, 'school_code', 'school_code');
    }

    public function courseHeaderForExamOffender()
    {
        return $this->belongsTo(CourseHeader::class, 'course_header', 'header_key');
    }

}
