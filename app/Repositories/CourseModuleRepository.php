<?php
namespace App\Repositories;

use App\Models\CourseModule;

class CourseModuleRepository extends BaseRepository
{

    public function __construct(CourseModule $model)
    {
        parent::__construct($model);
    }

    public function all()
    {
        return parent::all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
         $record = $this->model->findOrFail($id);
         $record->update($data);
         return $record;
    }

    public function courseModuleWithCourseHeader()
    {
        return $this->model->where('header_key', request()->course_header)->first();
    }

    public function courseModuleWithCourseKey($subject)
    {
        return $this->model->where('course_key', request()->input($subject))->get();
    }

    public function courseModuleWithCourseHeaderV2()
    {
        return $this->model->where('header_key', request()->course_header)->first();
    }




}


