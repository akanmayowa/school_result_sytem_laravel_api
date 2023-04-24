<?php

namespace App\Services;

use App\Helpers\GeneralLogs;
use App\Models\CourseHeader;
use App\Repositories\CourseHeaderRepository;
use App\Traits\ResponsesTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CourseHeaderResource;


class CourseHeaderServices{

    use ResponsesTrait;
    public ?CourseHeaderRepository $course_headers_repository = null;

    public function __construct(CourseHeaderRepository $course_headers_repository){
            $this->course_headers_repository = $course_headers_repository;
            $this->course_headers = new CourseHeader();
    }

    public function fetchAllCourseHeader(){
        $course_headers = $this->course_headers->orderBy('header_key', 'asc')->get();
        return  $this->successResponse($course_headers, "Course Header Fetch Completed");
    }

    public function fetchAllCourseHeaderDeleteStatusNull(){
        $course_headers = $this->course_headers->where('delete_status', 'no')->get();
        return  $this->successResponse($course_headers, "Course Header Fetch Completed");
    }

    public function createCourseHeader(array $request)
    {
        $course_header_data  = $this->course_headers_repository->create($request);
        return $this->successResponse(new CourseHeaderResource($course_header_data), 'Course Header Created Successfully.');
    }

    public function updateCourseHeader(array $request, $id)
    {
        $course_header =  $this->course_headers->find($id);
        $course_header->update($request);
        return $this->successResponse($course_header, 'Course Module Updated Successfully !');
    }

    public function changeDeleteStatus()
    {
        $course_headers = $this->course_headers->where('id', request()->input('id'))->first();
        if(empty($course_headers)){
            return $this->errorResponse("Invalid Course Header Inputted");
        }

        if($course_headers->delete_status == 'yes')
        {
            $course_headers->update(['delete_status' => 'no']);
            return $this->successResponse($course_headers,'Delete Status Changed to No Successfully');
        }

        if($course_headers->delete_status == 'no')
        {
            $course_headers->update(['delete_status' => 'yes']);
            return $this->successResponse($course_headers,'Delete Status Changed to Yes Successfully');
        }
    }

    public function changePracticalStatus()
    {
        $course_headers = $this->course_headers->where('id', request()->input('id'))->first();
        if(empty($course_headers)){
            return $this->errorResponse("Invalid Course Header Inputted");
        }

        if($course_headers->practical == 'yes')
        {
            $course_headers->update(['practical' => 'no']);
            return $this->successResponse($course_headers,'practical Changed to No Successfully');
        }

        if($course_headers->practical == 'no')
        {
            $course_headers->update(['practical' => 'yes']);
            return $this->successResponse($course_headers,'practical Changed to Yes Successfully');
        }
    }


    public function totals()
    {
        $total_modules = CourseHeader::select('header_key')->withSum('courseModule', 'credits')
                                                                ->where('header_key', request()->course_headers)
                                                                ->withCount('courseModule')
                                                                ->get();

        $data = $total_modules->map(function($filter, $key) {
            return [
                'total_modules' => $filter->course_module_sum_credits,
                'total_unit' => $filter->course_module_count
            ];
        });

        return $this->successResponse($data,' Course Header Statistics');
    }

}
