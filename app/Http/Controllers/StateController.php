<?php

namespace App\Http\Controllers;


use App\Http\Requests\StateRequest;
use App\Services\StateServices;

class StateController extends Controller
{
    public ?StateServices $state_services = null;

    public function __construct(StateServices $state_services){
            $this->state_services = $state_services;
            $this->middleware('auth:api');
        }

    public function index()
    {
        return $this->state_services->fetchAllState();
    }


    public function store(StateRequest $request)
    {
        return $this->state_services->createState($request->validated());
    }

}
