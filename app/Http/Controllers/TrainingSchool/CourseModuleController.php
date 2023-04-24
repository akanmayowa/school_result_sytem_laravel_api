<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\CourseModuleServices;

class CourseModuleController extends Controller
{
    public function __construct(CourseModuleServices $course_module_services){
        $this->course_module_services = $course_module_services;
    }


    public function indexV2(): \Illuminate\Http\JsonResponse
    {
        return $this->course_module_services->fetchAllCourseModulesV2();
    }



}
