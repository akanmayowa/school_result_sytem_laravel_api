<?php

namespace App\Http\Controllers;

use App\Http\Requests\NationalityRequest;
use App\Services\NationalityServices ;


class NationalityController extends Controller
{
    public ?NationalityServices $nationality_services = null;
    public function __construct(NationalityServices $nationality_services){
        $this->nationality_services = $nationality_services;
    }

    public function index()
    {
        return $this->nationality_services->fetchAllNationalities();
    }

    public function store(NationalityRequest $request){
            return $this->nationality_services->createNationality($request->validated());
    }
}
