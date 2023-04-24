<?php

namespace App\Services;

use App\Helpers\GeneralLogs;
use App\Repositories\CandidateInCourseRepository;
use App\Traits\ResponsesTrait;
use App\Http\Requests\CandidateInCourseRequest;

class CandidateInCourseServices
{
    use ResponsesTrait;
    public ? CandidateInCourseRepository $candidate_in_course_repository = null;
    public function __construct(CandidateInCourseRepository $candidate_in_course_repository){
        $this->candidate_in_course_repository = $candidate_in_course_repository;
    }

    public function fetchAllCandidateIncourse()
    {
        $candidate_in_course = $this->candidate_in_course_repository->all();
        return $this->successResponse($candidate_in_course, "CandidateInCourseServices retrieved successfully");
    }



    public function createCandidateInCourse(array $request)
    {
        $candidate_in_course = $this->candidate_in_course_repository->create($request);
        return $this->successResponse($candidate_in_course, "CandidateInCourseServices Created Successfully");
    }


}
