<?php
namespace App\Services;

use App\Helpers\GeneralLogs;
use App\Repositories\SchoolCategoryRepository;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponsesTrait;
use App\Http\Resources\SchoolCategoryResource;


class SchoolCategoryServices {
    use ResponsesTrait;
    public ?SchoolCategoryRepository $school_category_repository = null;

    public function __construct(SchoolCategoryRepository $school_category_repository)
    {
        $this->school_category_repository = $school_category_repository;
    }

    public function fetchAllSchoolCategory()
    {
        $school_category = $this->school_category_repository->all();
        return $this->successResponse(SchoolCategoryResource::collection($school_category), 'SchoolCategory Retrieved Successfully.');
    }


    public function createSchoolCategory(array $request)
    {

        $school_category = $this->school_category_repository->create($request);
        return $this->successResponse(new SchoolCategoryResource($school_category), 'School Category Created Successfully !');
    }
}
