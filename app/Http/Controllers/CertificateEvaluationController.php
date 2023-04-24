<?php

namespace App\Http\Controllers;

use App\Http\Requests\CertificateEvaluationRequest;
use Illuminate\Http\Request;
use App\Services\CertificateEvaluationServices;


class CertificateEvaluationController extends Controller
{
    public ?CertificateEvaluationServices $certificate_evaluation_services = null;

    public function __construct(CertificateEvaluationServices $certificate_evaluation_services){
            $this->certificate_evaluation_services = $certificate_evaluation_services;
    }

    public function index(){
        return $this->certificate_evaluation_services->fetchAllCertifucateEvaluation();
    }

    public function store(CertificateEvaluationRequest $request){
        return $this->certificate_evaluation_services->createCertificateEvaluation($request);
    }

//
//    public function update($id){
//        return $this->certificate_evaluation_services->update();
//    }

    


}
