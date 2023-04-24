<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\NationalityServices;
use Illuminate\Http\Request;

class NationalityController extends Controller
{
    public function __construct(NationalityServices $nationality_services){
        $this->nationality_services = $nationality_services;
    }


    public function index(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->nationality_services->fetchAllNationalities();
    }
}
