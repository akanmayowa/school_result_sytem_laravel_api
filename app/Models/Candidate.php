<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
    	'candidate_index',
        'school_code',
        'course_header',
        'registration_type',
        'exam_id',
        'exam_date',
        'reg_status'
    ];

    public function candidateIndexForCandidate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CandidateIndexing::class, 'candidate_index', 'candidate_index');
    }

    public function candidateIndexing()
    {
        return $this->belongsTo(CandidateIndexing::class, 'candidate_index', 'candidate_index');
    }

    public function trainingSchoolCandidate()
    {
        return $this->belongsTo(TrainingSchool::class, 'school_code', 'school_code')->withDefault();;
    }

    public function scoreMarkerOneForCandidate(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ScoreMarkerOne::class, 'candidate_index', 'candidate_index');
    }

    public function CandidateIncourseForCandidate(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CandidateIncourse::class, 'candidate_index', 'candidate_index');
    }


    public function scoreMarkerTwoForCandidate(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ScoreMarkerTwo::class, 'candidate_index', 'candidate_index');
    }


    public function courseHeaders()
    {
        return $this->belongsTo(CourseHeader::class, 'resit_header', 'header_key');
    }


    public function courseModules()
    {
        return $this->belongsTo(CourseModule::class, 'resit_header', 'header_key');
    }

    public function schoolResits()
    {
        return $this->hasMany(SchoolResit::class, 'candidate_index', 'candidate_index');
    }


}



