<?php

namespace App\Models;

use App\Models\CandidateIndexing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolResit extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_index',
        'subject_code',
        'school_code',
        'resit_header',
        'batch',
        'exam_date',
        'resit_reg_status',
        'old_exam_date'
    ];

    /**
     * Get the candidateindexing that owns the SchoolResit
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function candidateIndexing()
    {
        return $this->belongsTo(CandidateIndexing::class, 'candidate_index', 'candidate_index');
    }

    public function candidateIndexForResit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CandidateIndexing::class, 'candidate_index', 'candidate_index');
    }

    public function courseHeaders()
    {
        return $this->belongsTo(CourseHeader::class, 'resit_header', 'header_key');
    }


    public function courseModules()
    {
        return $this->belongsTo(CourseModule::class, 'subject_code', 'course_key');
    }

}
