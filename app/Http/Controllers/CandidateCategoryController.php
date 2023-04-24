<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidateCategoryRequest;
use Illuminate\Http\Request;
use App\Services\CandidateCategoryServices;


class CandidateCategoryController extends Controller
{

    public ?CandidateCategoryServices $candidate_category_services = null;

    public function __construct(CandidateCategoryServices $candidate_category_services){
        $this->candidate_category_services = $candidate_category_services;
    }

    public function index(){
        return $this->candidate_category_services->fetchAllCandidateCategory();
    }

    public function store(CandidateCategoryRequest $request){
        return $this->candidate_category_services->createCandidateCategory($request->validated());
    }

}
