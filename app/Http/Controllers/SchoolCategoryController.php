<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolCategoryRequest;
use App\Services\SchoolCategoryServices;


class SchoolCategoryController extends Controller
{

    public ?SchoolCategoryServices $school_category_services = null;

    public function __construct(SchoolCategoryServices $school_category_services){
            $this->school_category_services = $school_category_services;
    }

    public function index()
    {
        return $this->school_category_services->fetchAllSchoolCategory();
    }

    public function store(SchoolCategoryRequest $request)
    {
        return $this->school_category_services->createSchoolCategory($request->validated());
    }
}
