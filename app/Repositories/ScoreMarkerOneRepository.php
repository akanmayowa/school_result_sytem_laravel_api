<?php

namespace App\Repositories;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponsesTrait;
use App\Models\User;
use App\Models\ScoreMarkerOne;
use App\Helpers\GeneralLogs;
use App\Helpers\Activities;

class ScoreMarkerOneRepository extends BaseRepository
{

    use ResponsesTrait;
    public function __construct(ScoreMarkerOne $model){
        parent::__construct($model);
    }

    public function getScoreMarker($candidate_index){
        return $this->model->where([['candidate_index', $candidate_index],['course_key', request()->subject_code]]);
    }


    public function updateScoreMarkerOne($q1,$q2,$q3,$q4,$q5,$total_score,$status)
    {
        return $this->model->update([
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

    public function createScoreMarkerOne($q1,$q2,$q3,$q4,$q5,$total_score,$status,$candidate_index){
        return $this->model->create([
            'course_header' => request()->course_header,
            'course_key' => request()->subject_code,
            'exam_id' => request()->exam_date,
            'candidate_index' => ($candidate_index),
            'q1' => $q1,
            'q2' => $q2,
            'q3' => $q3,
            'q4' => $q4,
            'q5' => $q5,
            'school_code' => request()->school_code,
            'total_score' => $total_score,
            'operator_id' => User::where('id', Auth::id())->first()->operator_id,
            'status' => $status,
            'new' => 1
        ]);
    }

    public function updateScoreMarkerOneV2($id)
    {
        $marker_one = $this->model->find($id);
        $marker_one->total_score = request()->q1 + request()->q2 + request()->q3 + request()->q4 + request()->q5;
        $marker_one->q1 = request()->q1;
        $marker_one->q2 = request()->q2;
        $marker_one->q3 = request()->q3;
        $marker_one->q4 = request()->q4;
        $marker_one->q5 = request()->q5;
        $marker_one->operator_id = User::where('id', Auth::id())->first()->operator_id;
        $marker_one->new = 1;
        $marker_one->save();
        $score_array = ['q1' => request()->q1, 'q2' => request()->q2, 'q3' => request()->q3, 'q4' => request()->q4, 'q5' => request()->q5];
        Activities::scoreLog($score_array, trim($marker_one->candidate_index), Auth::id(), $marker_one->course_key, $marker_one->course_header, 'Score marker 1', $marker_one->exam_id);
        GeneralLogs::createLog('updated candidate\'s first marker score', $marker_one->candidate_index);
        return $this->successResponse("candidate Score Updated Successfully");
    }
}
