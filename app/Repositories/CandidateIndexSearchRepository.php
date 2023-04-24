<?php

namespace App\Repositories;

use App\Models\CandidateIndexing;

class CandidateIndexSearchRepository
{

    public function candidateIndexSearchWithRelationship()
    {
        return CandidateIndexing::with(['TrainingSchool', 'CourseHeader'])
            ->where('school_code', 'LIKE', '%' . request()->input('search') . '%')
            ->orWhere('course_header', 'LIKE', '%' . request()->input('search') . '%')
            ->orWhere('first_name', 'LIKE', '%' . request()->input('search') . '%')
            ->orWhere('middle_name', 'LIKE', '%' . request()->input('search') . '%')
            ->orWhere('last_name', 'LIKE', '%' . request()->input('search') . '%')
            ->orWhere('candidate_index', 'LIKE', '%' . request()->input('search') . '%')
            ->orWhere('exam_id', 'LIKE', '%' . request()->input('search') . '%')
            ->paginate(10);
    }



}
