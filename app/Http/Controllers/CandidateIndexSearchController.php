<?php

namespace App\Http\Controllers;

use App\Services\CandidateIndexSearchServices;
use Illuminate\Http\Request;


class CandidateIndexSearchController extends Controller
{
    public ? CandidateIndexSearchServices $candidate_index_search_services = null;

    public function __construct(CandidateIndexSearchServices $candidate_index_search_services)
    {
        $this->candidate_index_search_services = $candidate_index_search_services;
    }

    public function search()
    {
        return $this->candidate_index_search_services->candidateIndexSearch();
    }

    public function getExamId(){
        return $this->candidate_index_search_services->fetchExamId();
    }


    public function getExamIdForSchoools(){
        return $this->candidate_index_search_services->fetchExamIdForSchools();
    }


}
