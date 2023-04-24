<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\CandidateIndexingServices;
use Illuminate\Http\Request;

class CandidateIndexingController extends Controller
{
    public function __construct(CandidateIndexingServices $candidate_indexing_services){
        $this->candidate_indexing_services = $candidate_indexing_services;
    }

    public function index()
    {
        return $this->candidate_indexing_services->fetchAllCandidateIndexingVersion2();
    }
}
