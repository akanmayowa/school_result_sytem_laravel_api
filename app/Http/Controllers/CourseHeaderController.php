<?php

namespace App\Http\Controllers;
use App\Http\Requests\CourseHeaderRequest;
use App\Http\Requests\UpdateCourseHeaderRequest;
use Illuminate\Http\Request;

use App\Services\CourseHeaderServices;

class CourseHeaderController extends Controller
{
    public ?CourseHeaderServices $course_header_services = null;

    public function __construct(CourseHeaderServices $course_header_services)
    {
        $this->course_header_services = $course_header_services;
    }


    public function index()
    {
        return $this->course_header_services->fetchAllCourseHeader();
    }

    public function index_II()
    {
        return $this->course_header_services->fetchAllCourseHeaderDeleteStatusNull();
    }

    public function store(CourseHeaderRequest $request)
    {
        return $this->course_header_services->createCourseHeader($request->validated());
    }

    public function update(UpdateCourseHeaderRequest $request,$id)
    {
        return $this->course_header_services->updateCourseHeader($request->validated(),$id);
    }

    public function deleteStatus(){
        return $this->course_header_services->changeDeleteStatus();
    }

    public function practicalStatus()
    {
        return $this->course_header_services->changePracticalStatus();
    }

    public function totals()
    {
        return $this->course_header_services->totals();
    }
}
