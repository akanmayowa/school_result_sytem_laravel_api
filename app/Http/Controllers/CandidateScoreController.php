<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\CandidateScoreServices;

class CandidateScoreController extends Controller
{
    public ?CandidateScoreServices $candidate_score_services = null;

    public function __construct(CandidateScoreServices $candidate_score_services ){
        $this->candidate_score_services = $candidate_score_services;
    }


    public function scoreEntry()
    {
          return $this->candidate_score_services->scoreEntry();
    }


    public function store()
    {
        return $this->candidate_score_services->createCandidateScoreEntry();
    }

    public function update()
    {
        return $this->candidate_score_services->updateCandidateScoresEntry();
    }

    public function candidateScore()
    {
        return $this->candidate_score_services->TotalCandidateScore();
    }

    public function indexForScoreMarkerOne()
    {
        return $this->candidate_score_services->fetchAllCandidateScoreForScoreMarkerOne();
    }

    public function indexForResult()
    {
        return $this->candidate_score_services->fetchAllCandidateScoreForResult();
    }

    public function indexForCourseHeaderNotDeleted(): JsonResponse
    {
        return $this->candidate_score_services->fetchAllCourseHeaderNotDeleted();
    }


    public function indexForCandidateExamScore()
    {
        return $this->candidate_score_services->getCandidateExamScore();
    }




}
