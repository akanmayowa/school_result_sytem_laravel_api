<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\SchoolResitServices;
use Illuminate\Http\Request;

class SchoolResitController extends Controller
{

    public function __construct(SchoolResitServices $school_resit_services)
    {
        $this->school_resit_services = $school_resit_services;
    }


      public function index(): \Illuminate\Http\JsonResponse
      {
          return $this->school_resit_services->fetchAllSchoolResitV2();

      }
}
