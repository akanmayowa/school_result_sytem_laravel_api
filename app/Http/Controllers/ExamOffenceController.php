<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExamOffenceServices;
use App\Http\Requests\StoreExamOffenceRequest;
use App\Http\Requests\UpdateExamOffenceRequest;

class ExamOffenceController extends Controller
{

    public $examOffenceServices;

    public function __construct(ExamOffenceServices $examOffenceServices){
        $this->examOffenceServices = $examOffenceServices;
    }


    public function store(StoreExamOffenceRequest $request)
    {
        return $this->examOffenceServices->createExamOffence($request->validated());
    }


    public function index(Request $request)
    {
        return $this->examOffenceServices->fetchAllExamOffence($request->only(['description', 'punishment']));
    }


    public function show($examOffenceId)
    {
        return $this->examOffenceServices->showExamOffence($examOffenceId);
    }


    public function update($examOffenceId, UpdateExamOffenceRequest $request)
    {
        return $this->examOffenceServices->updateExamOffence($examOffenceId, $request->only(['description', 'punishment']));
    }

    public function ndexFor(){
        return $this->examOffenceServices->fetchSingleExamOffender();
    }


}
