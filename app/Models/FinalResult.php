<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalResult extends Model
{
    use HasFactory;
    protected $fillable = [
        'school_code',
        'candidate_index',
        'course_header',
        'total_credit',
        'weighted_score',
        'gpa',
        'waheb70',
        'year',
    ];


    public function results()
    {
        return $this->hasMany(ScoreResult::class, 'candidate_index', 'candidate_index');
    }

    public function incourse()
    {
        return $this->hasOne(CandidateIncourse::class, 'candidate_index', 'candidate_index');
    }

    public function candidate()
    {
        return $this->belongsTo(CandidateIndexing::class, 'candidate_index', 'candidate_index');
    }

    public function offences()
    {
        return $this->hasMany(ExamOffender::class, 'candidate_index', 'candidate_index');
    }
}
