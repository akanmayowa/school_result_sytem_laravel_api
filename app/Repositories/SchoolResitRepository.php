<?php

namespace App\Repositories;

use App\Models\SchoolResit;
use Illuminate\Database\Eloquent\Model;

class SchoolResitRepository extends BaseRepository
{

    public function __construct(SchoolResit $model)
    {
        parent::__construct($model);
    }

    public function SchoolResitJoinedWithCandidateIndexing($year)
    {
        return $this->model->join('candidate_indexings', 'candidate_indexings.candidate_index', 'school_resits.candidate_index')
            ->where('school_resits.school_code', request()->input('school_code'))
            ->where('school_resits.resit_header', request()->input('course_header'))
            ->where('school_resits.exam_date', 'like', '%' . $year)
            ->get(['candidate_indexings.last_name', 'candidate_indexings.first_name', 'candidate_indexings.middle_name', 'school_resits.*']);
    }


    public function fetchCandidateIndexAndSchoolCode($resit_candidates,$year)
    {
        return $this->model->where([['candidate_index' ,$resit_candidates->candidate_index], ['school_code', request()->school_code],
                                    ['resit_header', request()->course_header], ['exam_date', 'like', '%' . $year]])->get(['subject_code']);
    }


}
