<?php

namespace App\Services;

use App\Models\CandidateIndexing;
use App\Traits\ResponsesTrait;
use App\Repositories\CandidateIndexSearchRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CandidateIndexSearchServices
{
    use ResponsesTrait;

    public CandidateIndexSearchRepository|null $candidate_index_search_repository;
    public function __construct(CandidateIndexSearchRepository $candidate_index_search_repository)
    {
        $this->candidate_index_search_repository = $candidate_index_search_repository;
    }


    public function candidateIndexSearch(): JsonResponse
    {
        $validator = Validator::make(request()->all(), ['search' => 'required|string',]);
        if($validator->fails()) {
            return $this->errorResponse($validator->errors());
        }
        if(request()->has('search') && !empty(request()->input('search')))
        {
            $candidate_index_search = $this->candidate_index_search_repository->candidateIndexSearch();
            return $this->successResponse($candidate_index_search, "Candidate Index Search Result Returned successfully!");
        }
        return $this->successResponse("No data found");
    }

    public function fetchExamId(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'candidate_index' => 'required|exists:candidate_indexings|string',
            ]);

        if($validator->fails()){
            return $this->errorResponse($validator->errors());
        }

        $get_candidate_based_on_exam_id = (new CandidateIndexing())->where('candidate_index', $validator->validated())->get();
        if(count($get_candidate_based_on_exam_id) > 0){
            return response()->json(['data' => $get_candidate_based_on_exam_id,'message' => "Candidate Exam Id Retrieved Successfully"]);
        }
        return response()->json([ 'message' => "No Exam Id or Candidate Information Available"]);
    }



    public function fetchExamIdForSchools(): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'candidate_index' => 'required|exists:candidate_indexings|string',
        ]);

        if($validator->fails()){
            return $this->errorResponse($validator->errors());
        }

        $check = CandidateIndexing::select('school_code')->where('candidate_index', request()->candidate_index)->first();

        if($check->school_code != auth()->user()->operator_id){
            return $this->errorResponse("You Cant Edit Candidate Information");
        }

        $get_candidate_based_on_exam_id = (new CandidateIndexing())->where('candidate_index', $validator->validated())->get();
        if(count($get_candidate_based_on_exam_id) > 0){
            return response()->json(['data' => $get_candidate_based_on_exam_id,'message' => "Candidate Exam Id Retrieved Successfully"]);
        }
        return response()->json([ 'message' => "No Exam Id or Candidate Information Available"]);
    }

}
