<?php

namespace App\Repositories;

use App\Models\FinalResult;
use Illuminate\Database\Eloquent\Model;

class FinalResultRepository extends BaseRepository
{

    public function __construct(FinalResult $model)
    {
        parent::__construct($model);
    }

    public function selectFinalResultWithResult($value_two,$value_three)
    {
        return $this->model->whereHas('results', function($query) use ($value_three, $value_two)
        {
            $query->where('school_code', request()->input('school_code'));
            $query->where('course_header', request()->input('course_header'));
//            $query->where('year', '=' , $value_two->month . $value_three);
        })   ->orderBy('candidate_index')
            ->get()
            ->groupBy('candidate_index');
    }


    public function selectFinalResultWithCandidateIncourse($year)
    {
        return $this->model->whereHas('incourse', function($query) use ($year)
        {
            $query->where('school_code', request()->school_code);
            $query->orWhere('exam_id', 'like', '%' . $year);
            $query->orWhere('course_header', request()->input('course_header'));
            $query->orWhere('school_code', request()->input('school_code'));
            $query->groupBy('candidate_index');
//                 $query->orWhere('year', '=', $course_header_information->month . $year);
        })->orderBy('candidate_index')
            ->get();

    }




    public function selectFinalResultWithCandidateIncourseV1()
    {
        return $this->model->with('candidate','incourse')->get();
    }
}
