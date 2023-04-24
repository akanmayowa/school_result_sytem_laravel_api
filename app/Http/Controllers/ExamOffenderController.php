<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateExamOffenderRequest;
use App\Models\ExamOffender;
use Illuminate\Http\Request;
use App\Services\ExamOffenderServices;
use App\Http\Requests\StoreExamOffenderRequest;

class ExamOffenderController extends Controller
{
    public $examOffenderServices;

    public function __construct(ExamOffenderServices $examOffenderServices){
        $this->examOffenderServices = $examOffenderServices;
    }


    public function index()
    {
        return $this->examOffenderServices->fatchAllExamOffender();
    }

    public function index_II()
    {
        return $this->examOffenderServices->getExamOffender();
    }


    public function store(StoreExamOffenderRequest $request)
    {
        return $this->examOffenderServices->createExamOffender($request->validated());
    }


    public function update(UpdateExamOffenderRequest $request, $id)
    {
        return $this->examOffenderServices->updateExamOffencer($request->validated(), $id);
    }

    public function show($id)
    {
        return $this->examOffenderServices->fetchSingleExamOffender($id);
    }

    public function destroy($examOffenderId)
    {
        return $this->examOffenderServices->deleteExamOfender($examOffenderId);
    }

    public function reminder()
    {
        return $this->examOffenderServices->autoReminderForFormerOffender();
    }


}
