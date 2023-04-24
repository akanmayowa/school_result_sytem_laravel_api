<?php

namespace App\Repositories;
use App\Models\CertificateEvaluation;
use App\Traits\ResponsesTrait;



class CertificateEvaluationRepository extends BaseRepository
{
    use ResponsesTrait;
    public function __construct(CertificateEvaluation $model)
    {
        parent::__construct($model);
    }

    public function all(){
        return $this->model->all();
    }

    public function create(array $data){
        return $this->model->create($data);
    }


}
