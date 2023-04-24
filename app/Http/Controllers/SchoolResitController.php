<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SchoolResitServices;
use App\Http\Requests\IndexSchoolResitRequest;
use App\Http\Requests\StoreSchoolResitRequest;

class SchoolResitController extends Controller
{
    public $schoolResitServices;

    public function __construct(SchoolResitServices $schoolResitServices)
    {
        $this->schoolResitServices = $schoolResitServices;
    }

    public function store(StoreSchoolResitRequest $request)
    {
        return $this->schoolResitServices->createSchoolResit($request->validated());
    }

    public function index()
    {
        return $this->schoolResitServices->fetchAllSchoolResit();
    }

    public function schoolResitcounter()
    {
        return $this->schoolResitServices->numberOfSchoolResitCounter();
    }

    public function indexV2(){
        return $this->schoolResitServices->getAllSchoolResit();
    }

    public function show(){
        return $this->schoolResitServices->fetchSingleSchoolResit();
    }

    public function delete(){
        return $this->schoolResitServices->deleteResit();
    }

}
