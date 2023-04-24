<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State;
use App\Models\SchoolCategory;

class TrainingSchool extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_code',
        'index_code',
        'school_category_id',
        'state_id',
        'state_code',
        'school_name',
        'contact',
        'position',
        'phone',
        'email',
        'status',
        'password',
        'fax',
        'photo',
        'can_register'
    ];

    public function state()
    {
        return $this->hasOne(State::class,'id','state_id',);
    }

    public function schoolCategory()
    {
        return $this->belongsTo(SchoolCategory::class);
    }

    public function candidateIndexing()
    {
        return $this->hasMany(CandidateIndexing::class,'school_code','school_code');
    }

    public function userTrainingSchool()
    {
        return $this->hasOne(User::class, 'training_school_id', 'id');
    }

    public function candidateForTrainingSchool()
    {
        return $this->hasMany(Candidate::class,'school_code','school_code');
    }

    public function candidateWithIncourseScore()
    {
        return $this->hasMany(Candidate::class,'school_code','school_code');
    }


    public function examOffencesForCandidate()
    {
        return $this->hasMany(ExamOffender::class,'school_code','school_code');
    }

    public function scoreResultForCandidate()
    {
        return $this->hasMany(ScoreResult::class,'school_code','school_code');
    }


    public function scoreMarkerTwoForCandidateSchool(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ScoreMarkerTwo::class, 'school_code', 'school_code');
    }

    public function scoreMarkerOneForCandidateSchool()
    {
        return $this->belongsTo(ScoreMarkerOne::class, 'school_code', 'school_code');
    }

    public function CandidateIncourseForCandidateSchool()
    {
        return $this->belongsTo(CandidateIncourse::class, 'school_code', 'school_code');
    }

}
