<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
 use App\Models\CandidateIndexing;

class CandidateIndexingRepository extends BaseRepository
{
    public function __construct(CandidateIndexing $model)
    {
        parent::__construct($model);
    }

    public function all()
    {
        return parent::all();
    }

    public function create(array $data)
    {
        return parent::create($data);
    }

    public function show($id){
        return $this->model->find($id);
    }

    public function candidateIndexWithCourseHeaderV1($exam_id)
    {
       return $this->model->with(['inCourse' => function ($query) use ($exam_id) {$query->where('exam_id', $exam_id);
        }])
            ->where('course_header', request()->course_header)
            ->where('school_code', request()->school_code)
            ->get(['first_name', 'last_name', 'candidate_index'])
                       ->groupBy('candidate_index')
           ;
    }

    public function candidateIndexWithCourseHeaderV2(){
        return $this->model->where('course_header', request()->course_header)
            ->where('school_code', request()->school_code)
            ->get(['candidate_index', 'first_name', 'last_name'])
            ->groupBy('candidate_index');
    }


    public function candidateIndexingLeftJoinSchoolResist(string $year_splitting){
       return $this->model->leftJoin('school_resits', 'candidate_indexings.candidate_index', '=' ,'school_resits.candidate_index')
            ->where('candidate_indexings.school_code', request()->school_code)
            ->where('candidate_indexings.course_header', request()->course_header)
            ->where('candidate_indexings.exam_id', 'like', '%' . $year_splitting)
            ->where('candidate_indexings.visible', 1)
            ->where('candidate_indexings.validate', 'yes')
            ->orderBy('candidate_indexings.candidate_index', 'asc')
            ->get(['candidate_indexings.candidate_index','candidate_indexings.first_name', 'candidate_indexings.last_name',
                'candidate_indexings.middle_name', 'candidate_indexings.course_header',
                'candidate_indexings.candidate_index', 'school_resits.candidate_index as indexes'])
                    ->groupBy('candidate_indexings.candidate_index');

    }

    public function CandidateIndexingWithSchoolCodeAndCourseHeader(string $year_splitting)
    {
       return $this->model->where('school_code','=' ,request()->school_code)
            ->where('course_header', '=', request()->course_header)
            ->where('exam_id', 'like', '%' . $year_splitting)
            ->where('visible', '=' ,1)
            ->orderBy('candidate_index', 'asc')
            ->get(['first_name', 'last_name', 'middle_name', 'course_header', 'candidate_index'])
            ->groupBy('candidate_index');
    }

    public function selectCourseHeaderCandidateIndexAndSchoolCode($year)
    {
        return $this->model->with('school')
            ->where('course_header',request()->course_header)
            ->where('exam_id', 'like', '%' . $year)
            ->get(['id', 'candidate_index', 'exam_id', 'school_code', 'course_header'])
            ->sortBy('school_code')
            ->groupBy('school_code');
    }


    public function selectCandidateIndexingWithScoreMarkerOneAndScoreMarkerTwo($splitted_year)
    {
        return $this->model
            ->join('scores_marker_one','scores_marker_one.candidate_index','candidate_indexings.candidate_index')
            ->join('score_marker_two','scores_marker_two.candidate_index','candidate_indexings.candidate_index')
            ->where('scores_marker_one.course_key', request()->subject)
            ->where('scores_marker_one.school_code', request()->school_code)
            ->where('scores_marker_one.exam_id', 'like', '%' , $splitted_year)
            ->select('candidate_indexings.school_code', 'candidate_indexings.candidate_index',
                'scores_marker_one.q1', 'scores_marker_one.q2', 'scores_marker_one.q3', 'scores_marker_one.q4', 'scores_marker_one.q5',
                'score_marker_two.q1', 'score_marker_two.q2', 'score_marker_two.q3', 'score_marker_two.q4', 'score_marker_two.q5',
                'score_marker_one.operator_id', 'score_marker_two.operator_id', 'candidate_indexings.exam_date', 'score_marker_one.status')
            ->get()
            ->orderBy('candidate_indexings.candidate_index')
            ->groupBy('candidate_indexings.candidate_index');
    }
}
