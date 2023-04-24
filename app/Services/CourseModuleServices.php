<?php

namespace App\Services;

use App\Helpers\GeneralLogs;
use App\Models\CourseModule;
use App\Traits\ResponsesTrait;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CourseModuleRepository;
use App\Http\Resources\CourseModuleResource as CourseModuleResource;

class CourseModuleServices
{
    use ResponsesTrait;
    public ?CourseModuleRepository $course_module_repository = null;

    public function __construct(CourseModuleRepository $course_module_repository){
        $this->course_module_repository = $course_module_repository;
        $this->courseModule = new CourseModule;
    }

    public function fetchAllCourseModulesV2(): \Illuminate\Http\JsonResponse
    {
        $courseModules = $this->course_module_repository->all();
        return $this->successResponse(CourseModuleResource::collection($courseModules));
    }


    public function fetchAllCourseModules(array $data)
    {
        $courseModules = $this->courseModule->query();

        if(isset($data['header_key']) && !empty($data['header_key'])){
            $courseModules = $courseModules->where('header_key', $data['header_key']);
        }

        return $this->successResponse(CourseModuleResource::collection($courseModules->orderByDesc('id')->get()));
    }

    public function createCourseModule(array $request)
    {
        $course_module = $this->course_module_repository->create($request);
        return $this->successResponse(new CourseModuleResource($course_module), 'Course Module Created Successfully !');
    }

    public function updateCourseModule(array $request,$id)
        {
            $update_course_header = $this->course_module_repository->update($request, $id);
            return $this->successResponse($update_course_header, 'Course Module Updated Successfully !');
        }

    public function changeDeleteStatus()
        {
                $course_module = $this->courseModule->where('id', request()->input('id'))->first();
                if(empty($course_module)){
                    return $this->errorResponse("Invalid Course Module Inputted");
                }

                if($course_module->delete_status == 'yes')
                {
                    $course_module->update(['delete_status' => 'no']);
                    return $this->successResponse($course_module,'Delete Status Changed to No Successfully');
                }

                if($course_module->delete_status == 'no')
                {
                    $course_module->update(['delete_status' => 'yes']);
                    return $this->successResponse($course_module,'Delete Status Changed to Yes Successfully');
                }
        }

    public function changePracticalStatus()
        {
            $course_module = $this->courseModule->where('id', request()->input('id'))->first();
            if(empty($course_module)){
                return $this->errorResponse("Invalid Course Module Inputted");
            }

            if($course_module->practical == 'yes')
            {
                $course_module->update(['practical' => 'no']);
                return $this->successResponse($course_module,'practical Changed to No Successfully');
            }

            if($course_module->practical == 'no')
            {
                $course_module->update(['practical' => 'yes']);
                return $this->successResponse($course_module,'practical Changed to Yes Successfully');
            }
        }

}
