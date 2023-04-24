<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\TrainingSchoolServices;
use Illuminate\Http\Request;

class TrainingSchoolController extends Controller
{
    public function __construct(TrainingSchoolServices $trainingSchooolService){
        $this->trainingSchooolService = $trainingSchooolService;
    }


    public function index(): \Illuminate\Http\JsonResponse
    {
        return $this->trainingSchooolService->fetchAllTrainingSchools();
    }

    public function update($id): \Illuminate\Http\JsonResponse
    {
        return $this->trainingSchooolService->updateTrainingSchool($id);
    }


}
