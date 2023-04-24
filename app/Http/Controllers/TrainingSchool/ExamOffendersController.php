<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\ExamOffenderServices;

class ExamOffendersController extends Controller
{

    public function __construct(ExamOffenderServices $exam_offender_services){
        $this->exam_offender_services = $exam_offender_services;
    }


    public function index(): \Illuminate\Http\JsonResponse
    {
        return $this->exam_offender_services->fetchAllExamOffenderVersion2();
    }
}
