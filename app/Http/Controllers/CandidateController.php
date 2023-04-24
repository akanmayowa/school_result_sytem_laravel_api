<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CandidateServices;
use App\Http\Requests\StoreCandidateRequest;
use App\Http\Requests\UpdateCandidateRequest;

class CandidateController extends Controller
{
    public $candidate_services;
    public function __construct(CandidateServices $candidate_services)
    {
        $this->candidate_services = $candidate_services;
    }

    public function index()
    {
        return $this->candidate_services->fetchAllCandidate();
    }

    public function store(StoreCandidateRequest $request)
    {
       return $this->candidate_services->createCandidate($request->validated());
    }

    public function update($candidateId, UpdateCandidateRequest $request)
    {
       return $this->candidate_services->updateCandidate($candidateId, $request->all());
    }

    public function indexVersionBeta($school_code): \Illuminate\Http\JsonResponse
    {
        return $this->candidate_services->fetchAllCandidateThatAreRegisteredAndIndexed($school_code);
    }

    public function show($id)
    {
        return $this->candidate_services->showSingleCandidateDetail($id);
    }

    public function store_II()
    {
        return $this->candidate_services->createCandidateForTrainingSchool();
    }

}
