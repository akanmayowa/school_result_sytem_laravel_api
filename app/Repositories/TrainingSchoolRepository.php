<?php /** @noinspection ALL */

namespace App\Repositories;
use App\Models\TrainingSchool;

class TrainingSchoolRepository extends BaseRepository
{
    public function __construct(TrainingSchool $model)
    {
        parent::__construct($model);
    }

    public function all()
    {
        return parent::all();
    }


    public function create(array $data)
    {
        return parent::create($data);
    }


    public function update(array $data, $id)
    {
       return parent::findOrFail($id)->update($data);
    }


    public function delete($id)
    {
        return parent::findOrFail($id)->delete();
    }


    public function show($id)
    {
        return $this->model->findOrFail($id);
    }

    public function selectTrainingschoolwithSchoolCode($filter)
    {
        return $this->model->where(function($query) use($filter) {
            foreach($filter as $value) {
                $query->orWhere('school_code', $value);
            }})->get('school_code');
    }

    public function selectIndexCodeAndSchoolCodeUsingQuery($term)
    {
        return $this->model->query()->where('index_code', $term->index_code)->get('school_code');
    }

    public function selectState()
    {
        return $this->model->with('state')->first();
    }

    public function selectSchoolCode()
    {
        return $this->model->where('school_code',request()->school_code)->first();
    }

    public function selectStateWithSchoolCode()
    {
        return $this->model->with('state')->where('school_code', request()->input('school_code'))->first();
    }

    public function selectStateAndSchoolCode()
    {
        return $this->model->with('state')
            ->where('school_code',  request()->school_code)
            ->first(['school_code', 'school_name', 'state_code']);
    }

    public function get()
    {
        return $this->model->get();
    }



}
