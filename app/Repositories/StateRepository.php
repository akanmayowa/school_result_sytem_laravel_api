<?php

namespace App\Repositories;
use App\Models\State;

class StateRepository extends BaseRepository{


    public function __construct(State $model) {

        parent::__construct($model);
    }

    public function all(){
        return $this->model->all();
    }

    public function create(array $data){
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->findOrFail($id)->update();
    }

    public function selectBasedOnStateCode($variable)
    {
        return $this->model->where('id', $variable)->first();
    }
}
