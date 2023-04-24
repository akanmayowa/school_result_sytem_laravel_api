<?php

namespace App\Repositories;
use App\Models\Exam;

class ExamRepository extends BaseRepository
{
    public function __construct(Exam $model){

        Parent::__construct($model);
    }

    public function all()
    {
        return parent::all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }


    public function checkExamType($exam_type){
        if($exam_type == 1) {
            return $this->model->where('id', $exam_type)->first()->type;
        }
        elseif ($exam_type == 2) {
            return $this->model->where('id', $exam_type)->first()->type;
        }
        else{
            return $this->model->where('id', $exam_type)->first()->type;
        }
    }

    public function selectCandidateIndexAndRegIndex($candidate_index,$year):void {
         $this->model->where('status', 1)->where('candidate_index',$candidate_index)->where('registration_date', 'like', '%' . $year)->exists();
    }

}
