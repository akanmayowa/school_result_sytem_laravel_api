<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCandidateIndexingRequest;
use App\Services\CandidateIndexingServices;
use App\Http\Requests\CandidateIndexingRequest;
use App\Http\Requests\VerifyCandidateIndexRequest;
use Illuminate\Http\JsonResponse;

class CandidateIndexingController extends Controller
{
    public ?CandidateIndexingServices $candidate_indexing_services = null;

    public function __construct(CandidateIndexingServices $candidate_indexing_services){
            $this->candidate_indexing_services = $candidate_indexing_services;
    }

    public function index()
    {
       return $this->candidate_indexing_services->fetchAllCandidateIndexing(request('q'));
    }

    public function index_II()
    {
        return $this->candidate_indexing_services->getCandidate();
    }

    public function index_III()
    {
        return $this->candidate_indexing_services->searchByNameAndCandidateIndexAndCourseHeader();
    }

    public function index_IIII()
    {
        return $this->candidate_indexing_services->searchByNameAndCandidateIndexAndCourseHeaderForTrainingSchool();
    }


    public function store(CandidateIndexingRequest $request)
    {
        return $this->candidate_indexing_services->createCandidateIndexing($request->validated());
    }


    public function store_II(CandidateIndexingRequest $request)
    {
        return $this->candidate_indexing_services->createCandidateIndexingForSchools($request->validated());
    }




    public function verifyCandidateIndex()
    {
       return $this->candidate_indexing_services->verifyCandidateIndexForTrainingSchool();
    }

    public function update(UpdateCandidateIndexingRequest $request): JsonResponse
    {
        return $this->candidate_indexing_services->updateCandidateIndexing($request->validated());
    }

    public function show($id)
    {
        return $this->candidate_indexing_services->showSingleCandidateIndexDetail($id);
    }


    public function delete()
    {
        return $this->candidate_indexing_services->delete();
    }

}
