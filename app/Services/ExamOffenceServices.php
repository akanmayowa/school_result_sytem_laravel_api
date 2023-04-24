<?php

namespace App\Services;

use App\Traits\ResponsesTrait;
use App\Http\Resources\ExamOffenceResource;
use App\Repositories\ExamOffenceRepository;
use App\Http\Resources\CourseHeaderResource;


class ExamOffenceServices{

    use ResponsesTrait;

    public $examOffenceRepository;

    public function __construct(ExamOffenceRepository $examOffenceRepository){
        $this->examOffenceRepository = $examOffenceRepository;
    }


    public function createExamOffence(array $data)
    {
        if($this->examOffenceRepository->whereFirst([
            'description' => $data['description'],
            'punishment' => $data['punishment']
        ])){
            return $this->errorResponse('Record already exist', 409);
        }

        $examOffence = $this->examOffenceRepository->create($data);

        if(!$examOffence){
            return $this->errorResponse('Unable to create record at this time, please try again', 500);
        }

        return $this->successResponse(new ExamOffenceResource($examOffence), 'Exam offence created');
    }


    public function fetchAllExamOffence(array $data){
        $examOffences = $this->examOffenceRepository->all();
        return  $this->successResponse(ExamOffenceResource::collection($examOffences));
    }


    public function showExamOffence($examOffenceId){
        $examOffence = $this->examOffenceRepository->show($examOffenceId);
        return  $this->successResponse(ExamOffenceResource::collection($examOffence));
    }


    public function updateExamOffence($examOffenceId, array $data){
        if(!$examOffence = $this->examOffenceRepository->show($examOffenceId)){
            return $this->errorResponse('No record found', 404);
        }
        $examOffence->update($data);
        return  $this->successResponse(ExamOffenceResource::collection($examOffence));
    }



}
