<?php

namespace App\Repositories;

use App\Helpers\Activities;
use App\Helpers\GeneralLogs;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\ScoreMarkerTwo;
use Illuminate\Support\Facades\Auth;

class ScoreMarkerTwoRepository extends BaseRepository
{


    public function __construct(ScoreMarkerTwo $model)
    {
        parent::__construct($model);
    }


    public function scoreMarkerWithCandidateIndexAndSubject($candidate_index)
    {
        return $this->model->where('candidate_index',$candidate_index)->where('course_header', request()->subject_code)->where( 'exam_id',request()->exam_date);
    }


    public function createScoreMarkerTwo($q1,$q2,$q3,$q4,$q5,$total_score,$status,$candidate_index)
    {
        return $this->model->create([
            'course_header' => request()->course_header,
            'course_key' => request()->subject_code,
            'candidate_index' => trim($candidate_index),
            'q1' => $q1,
            'q2' => $q2,
            'q3' => $q3,
            'q4' => $q4,
            'q5' => $q5,
            'school_code' => request()->school_code,
            'total_score' => $total_score,
            'exam_id' => request()->exam_date,
            'operator_id' => User::where('id', Auth::id())->first()->operator_id,
            'status' => $status,
            'new' => 1
        ]);
    }


    public function updateScoreMarkerTwo($q1,$q2,$q3,$q4,$q5,$total_score,$status,$candidate_index)
    {
        return $this->model->where('candidate_index', request()->candidate_index)->where('course_key', request()->subject_code)->where('exam_id', request()->exam_date)->update([
            'q1' => $q1,
            'q2' => $q2,
            'q3' => $q3,
            'q4' => $q4,
            'q5' => $q5,
            'school_code' => request()->school_code,
            'total_score' => $total_score,
            'operator_id' => User::where('id', Auth::id() ?? 1)->first()->operator_id,
            'status' => $status,
            'new' => 1
        ]);
    }



    public function updateScoreMarkerTwoV3($id){
        $marker_two = $this->model->find($id);
        $marker_two->total_score = request()->q1 + request()->q2 + request()->q3 + request()->q4 + request()->q5;
        $marker_two->q1 = request()->q1;
        $marker_two->q2 = request()->q2;
        $marker_two->q3 = request()->q3;
        $marker_two->q4 = request()->q4;
        $marker_two->q5 = request()->q5;
        $marker_two->operator_id = User::where('id', Auth::id() ?? 1)->first()->operator_id;
        $marker_two->new = 1;
        $marker_two->save();
        $score_array = ['q1' => request()->q1, 'q2' => request()->q2, 'q3' => request()->q3, 'q4' => request()->q4, 'q5' => request()->q5];
        Activities::scoreLog($score_array, trim($marker_two->candidate_index), Auth::id(), $marker_two->course_key, $marker_two->course_header, 'Score marker 2', $marker_two->exam_id);
        GeneralLogs::createLog( 'updated candidate\'s second marker score', $marker_two->candidate_index);
        return $this->successResponse("candidate Score Updated Successfully");

    }
}
