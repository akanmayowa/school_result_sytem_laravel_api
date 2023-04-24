<?php

namespace App\Services;

use App\Repositories\ExamRepository;
use App\Traits\ResponsesTrait;
use Illuminate\Support\Facades\Validator;

class ExamServices
{
    use ResponsesTrait;
    public ? ExamRepository $exam_repository = null;
    public function __construct(ExamRepository $exam_repository)
    {
        $this->exam_repository =  $exam_repository;
    }

    public function  fetchAllExamType(){
        $exam = $this->exam_repository->all();
        return $this->successResponse($exam, "Exam Data Retrieved Successfully");
    }

    public function createExamType()
    {
        $validator = Validator::make(request()->all(), [
           'type' => 'required|string',
        ]);
        $exam =  $this->exam_repository->create($validator->validated());
        return $this->successResponse($exam, "Exam Type created Successfully");
    }


}
