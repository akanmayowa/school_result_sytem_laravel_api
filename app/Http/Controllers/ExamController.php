<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Services\ExamServices;

class ExamController extends Controller
{
    public ? ExamServices $exam_services = null;

    public function __construct(ExamServices $exam_services )
    {
        $this->exam_services = $exam_services;

    }

    public function index() {
        return $this->exam_services->fetchAllExamType();
    }

    public function store()
    {
        return $this->exam_services->createExamType();
    }


}
