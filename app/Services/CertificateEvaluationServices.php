<?php

namespace App\Services;

use App\Http\Requests\CertificateEvaluationRequest;
use App\Repositories\CertificateEvaluationRepository;
use App\Traits\ResponsesTrait;


class CertificateEvaluationServices{

    use ResponsesTrait;
    public ?CertificateEvaluationRepository $certificate_evaluation_repository = null;
    public function __construct(CertificateEvaluationRepository $certificate_evaluation_repository){
        $this->certificate_evaluation_repository = $certificate_evaluation_repository;
    }

    public function fetchAllCertifucateEvaluation()
    {
        $certificate_evaluation = $this->certificate_evaluation_repository->all();
        return $this->successResponse($certificate_evaluation);
    }

    public function createCertificateEvaluation(CertificateEvaluationRequest $request)
    {
        $validated_data = $request->validated();
        $certificate_evaluation = $this->certificate_evaluation_repository->create($validated_data);
        return $this->successResponse($certificate_evaluation, 'Certificate Evaluation Created Successfully! ');
    }

}
