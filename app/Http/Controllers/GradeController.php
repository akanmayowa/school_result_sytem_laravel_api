<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Services\GradeServices;
use Illuminate\Http\Request;
use App\Services\NationalityServices;


class GradeController extends Controller
{
    public ?GradeServices $grade_services = null;
    public function __construct(GradeServices $grade_services){
        $this->grade_services = $grade_services;
    }

    public function index(){
        return $this->grade_services->fetchAllGrade();
    }

    public function store(GradeRequest $request){
        return $this->grade_services->createGrade($request->validated());
    }
}
