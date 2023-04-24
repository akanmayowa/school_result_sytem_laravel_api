<?php

namespace App\Repositories;

use Exception;
use App\Models\ExamOffence;
use Illuminate\Support\Facades\Schema;

class ExamOffenceRepository extends BaseRepository{

    public function __construct(ExamOffence $model) {
        parent::__construct($model);
    }


    public function all(){
        return $this->model->get();
    }


    public function create(array $data){
        return $this->model->create($data);
    }


    public function update(array $data, $id)
    {
        return $this->model->findOrFail($id)->update();
    }


    public function checkModelColumnsExist($columns)
    {
        $table = $this->model->getTable();
        $tableColumns = Schema::getColumnListing($table);

        foreach($columns as $column){
            if(!in_array($column, $tableColumns)){
                throw new Exception("{$column} column not found in {$table} table");
            }
        }
    }


    public function query(){
        return $this->model->query();
    }

     public function whereFirst($array){

        $this->checkModelColumnsExist(array_keys($array));

        return $this->model->where($array)->first();
    }


    public function show($id)
    {
        return $this->model->find($id);
    }


}
