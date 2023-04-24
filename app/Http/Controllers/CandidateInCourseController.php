<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidateIncourseRequest;
use App\Services\CandidateInCourseServices;
use Illuminate\Http\Request;

class CandidateInCourseController extends Controller
{
    public $candidate_in_course_services = null;

    public function __construct( CandidateInCourseServices $candidate_in_course_services){
        $this->candidate_in_course_services = $candidate_in_course_services;
    }

    public function index()
    {
        return $this->candidate_in_course_services->fetchAllCandidateIncourse();
    }


    public function store(CandidateIncourseRequest $request)
    {
        return $this->candidate_in_course_services->createCandidateInCourse($request->validated());
    }

}
