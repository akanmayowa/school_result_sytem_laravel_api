<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolPerformance extends Model
{
    use HasFactory;

    protected $fillable=[
        'school_code', 'candidate_index', 'passed',
          'absent', 'no_incourse', 'failed', 'malpractice','exam_id', 'course_header', 'new'
    ];

    public function candidateSchoolPerfomance(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CandidateIndexing::class, 'candidate_index', 'candidate_index');
    }


}
