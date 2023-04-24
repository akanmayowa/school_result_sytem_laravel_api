<?php

namespace App\Http\Controllers;
use App\Http\Requests\CourseModuleRequest;
use Illuminate\Http\Request;
use App\Services\CourseModuleServices;


class CourseModuleController extends Controller
{
    public ?CourseModuleServices $course_module_services = null;

    public function __construct(CourseModuleServices $course_module_services)
    {
        $this->course_module_services = $course_module_services;
    }

    public function index(Request $request)
    {
        return $this->course_module_services->fetchAllCourseModules($request->all());
    }

    public function store(CourseModuleRequest $request){
        return $this->course_module_services->createCourseModule($request->validated());
    }

    public function update(CourseModuleRequest $request,$id){
        return $this->course_module_services->updateCourseModule($request->validated(),$id);
    }

    public function deleteStatus(){
        return $this->course_module_services->changeDeleteStatus();
    }

    public function practicalStatus()
    {
        return $this->course_module_services->changePracticalStatus();
    }

}
