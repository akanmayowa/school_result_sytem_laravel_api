<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\CourseHeaderServices;

class CourseHeaderController extends Controller
{

    public function __construct(CourseHeaderServices $course_header_services)
    {
        $this->course_header_services = $course_header_services;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        return $this->course_header_services->fetchAllCourseHeader();
    }
}
