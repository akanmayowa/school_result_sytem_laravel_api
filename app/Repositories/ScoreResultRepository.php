<?php

namespace App\Repositories;

use App\Models\ScoreResult;
use Illuminate\Database\Eloquent\Model;

class ScoreResultRepository extends BaseRepository
{
    public function __construct(ScoreResult $model)
    {
        parent::__construct($model);
    }



    public function selectCandidateIndexAndCourseKey($candidate_index,$year, $course_key)
    {
       return $this->model->where('candidate_index',$candidate_index)->where('year', 'like', '%' . $year)->where('course_key',$course_key);
    }


    public function selectCourseHeaderYearAndDescription($year)
    {
        return $this->model->with(['courseScoreResult' => function ($query) {
            $query->select('description');
        }])
            ->where('year', 'like', '%' . $year)
            ->where('course_header', request()->course_header)
            ->where('course_average', '>=', 40)
            ->orderBy('course_key')
            ->get();
//            ->groupBy('couse_key');
    }

    public function selectCourseHeaderYearAndDescriptionV2($year)
    {
        return $this->model
            ->where('year', 'like', '%' . $year)
            ->where('course_header', request()->course_header)
            ->where('course_average', '<', 40)
            ->orderBy('course_key')
            ->get();
//            ->groupBy('couse_key');
    }


//->with(['courseScoreResult' => function ($query) { $query->select('description'); }])

}
