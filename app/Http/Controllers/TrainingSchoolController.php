<?php

namespace App\Http\Controllers;
use App\Http\Requests\TrainingSchoolRequest;
use App\Services\TrainingSchoolServices;

class TrainingSchoolController extends Controller
{

    public ?TrainingSchoolServices $training_school_services = null;

    public function __construct(TrainingSchoolServices $training_school_services){
        $this->training_school_services = $training_school_services;
    }


    public function index_II()
    {
        return $this->training_school_services->fetchAllTrainingSchoolsWithStatusNotNull();
    }

    public function index()
    {
        return $this->training_school_services->fetchAllTrainingSchools();
    }

    public function store()
    {
        return $this->training_school_services->createTrainingSchool();
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update($id)
    {
        return $this->training_school_services->updateTrainingSchool($id);
    }

    public function destroy($id)
    {
            return $this->training_school_services->deleteTrainingSchool($id);
    }

    public function changeStatus($id)
    {
        return $this->training_school_services->changeTrainingSchoolStatus($id);
    }


    public function changeCanRegiterStatus()
    {
        return $this->training_school_services->changeCanRegiterStatus();
    }

    public function loginInSchoolDetails()
    {
        return $this->training_school_services->loginInSchoolDetails();
    }



}
