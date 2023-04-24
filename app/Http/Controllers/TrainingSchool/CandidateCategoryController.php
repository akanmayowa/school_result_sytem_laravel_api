<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\CandidateCategoryServices;

class CandidateCategoryController extends Controller
{
    public function __construct(CandidateCategoryServices $CandidateCategoryServices){
        $this->CandidateCategoryServices = $CandidateCategoryServices;
    }


    public function index(): \Illuminate\Http\JsonResponse
    {
        return $this->CandidateCategoryServices->fetchAllCandidateCategory();
    }

}
