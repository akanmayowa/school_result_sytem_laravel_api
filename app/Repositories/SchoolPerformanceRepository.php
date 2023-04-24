<?php

namespace App\Repositories;

use App\Models\SchoolPerformance;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class SchoolPerformanceRepository extends BaseRepository
{
    public function __construct(SchoolPerformance $model)
    {
        parent::__construct($model);
    }

    public function selectCourseHeaderAndSchoolName($splitted_year)
    {
        return $this->model->where('exam_id', 'like', '%' . $splitted_year)
            ->orWhere('school_code', request()->school_code)
            ->orderBy('school_code')
            ->get();
    }


    public function selectSchoolCodeAndCourseHeader($exam_id)
    {
        return $this->model->with(['candidateSchoolPerfomance' => function($query){
            $query->where('course_header', request()->input('course_header'));
            $query->select('candidate_index','first_name','middle_name','last_name');
        }])
                ->where('school_code',request()->input('school_code'))
                ->where('exam_id', 'like', '%' . $exam_id[1])
                ->where('malpractice', 0)
                ->get();
    }

}
