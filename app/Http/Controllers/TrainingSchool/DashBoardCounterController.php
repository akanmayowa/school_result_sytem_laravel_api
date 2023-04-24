<?php

namespace App\Http\Controllers\TrainingSchool;

use App\Http\Controllers\Controller;
use App\Services\DashBoardCounterServices;

class DashBoardCounterController extends Controller
{


    public function __construct(DashBoardCounterServices $dashboard_counter_services){
        $this->dashboard_counter_services = $dashboard_counter_services;
    }

    public function index(){
        return $this->dashboard_counter_services->getRecordTotalCountAndSum();
    }


    public function indexV2(){
            return $this->dashboard_counter_services->getRecordTotalCountAndSumV2();
        }

}
