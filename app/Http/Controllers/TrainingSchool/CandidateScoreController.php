<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\CandidateScoreServices;
use Illuminate\Http\Request;

class CandidateScoreController extends Controller
{

    public function __construct(CandidateScoreServices $candidate_score_services){
        $this->candidate_score_services = $candidate_score_services;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        return $this->candidate_score_services->TotalCandidateScoreVersion2();
    }

}
