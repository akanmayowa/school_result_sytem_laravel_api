<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\CandidateServices;

class CandidateController extends Controller
{

    public function __construct(CandidateServices $candidate_services){
        $this->candidate_services = $candidate_services;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
       return $this->candidate_services->fetchAllCandidateVersion2();
    }


    public function indexVersion2($school_code): \Illuminate\Http\JsonResponse
    {
        return $this->candidate_services->trainingSchoolAndCandidateRelationship($school_code);
    }


    public function indexVersionBeta($school_code): \Illuminate\Http\JsonResponse
    {
        return $this->candidate_services->fetchAllCandidateThatAreRegisteredAndIndexed($school_code);
    }

}
