<?php /** @noinspection ALL */

namespace App\Services;

use App\Http\Requests\CandidateCategoryRequest;
use App\Repositories\CandidateCategoryRepository;
use App\Traits\ResponsesTrait;
use App\Helpers\GeneralLogs;


class CandidateCategoryServices{

    use ResponsesTrait;
    public ? CandidateCategoryRepository $candidate_category_repository = null;

    public function __construct(CandidateCategoryRepository $candidate_category_repository){
        $this->candidate_category_repository = $candidate_category_repository;
    }

   public function fetchAllCandidateCategory(){
        $candidate_category = $this->candidate_category_repository->all();
        return $this->successResponse($candidate_category);
   }




   public function createCandidateCategory(array $request)
   {
        $candidate_category = $this->candidate_category_repository->create($request);
       GeneralLogs::Activities(" Candidate Category been created", $candidate_category->id);
       return $this->successResponse($candidate_category, ' candidate category created successfully! ');
    }

}
