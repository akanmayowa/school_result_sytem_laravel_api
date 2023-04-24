<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use App\Repositories\GradeRepository;
use App\Traits\ResponsesTrait;

class GradeServices extends BaseRepository
{

    use ResponsesTrait;
    public ?GradeRepository $grade_repository = null;

    public function __construct(GradeRepository $grade_repository){
        $this->grade_repository = $grade_repository;
    }

    public function FetchAllGrade()
    {
       $grade = $this->grade_repository->all();
       return $this->successResponse($grade);
    }


    public function createGrade(array $request)
    {
        $grade = $this->grade_repository->create($request);
        return $this->successResponse($grade, " Grade created successfully! ");
    }
}
