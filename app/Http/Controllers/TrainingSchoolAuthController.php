<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TrainingSchoolAuthServices;


class TrainingSchoolAuthController extends Controller
{
    public  ? TrainingSchoolAuthServices $training_school_auth_services = null;

    public function __construct(TrainingSchoolAuthServices $training_school_auth_services)
    {
         $this->training_school_auth_services = $training_school_auth_services;
    }

    public function login()
    {
        return $this->training_school_auth_services->trainingSchoolUserLogin();
    }

    public function verifyTwoFactorCode()
    {
        return $this->training_school_auth_services->verifyTwoFactorCodeForTrainingSchoolUser();
    }


}
