<?php

namespace App\Repositories;
use App\Models\SchoolCategory;

class SchoolCategoryRepository extends BaseRepository{


    public function __construct(SchoolCategory $model) {
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
        return $this->model->findOrFail($id)->update($data);
    }
}


