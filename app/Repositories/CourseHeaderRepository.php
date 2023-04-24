<?php /** @noinspection ALL */

namespace App\Repositories;

use App\Models\CourseHeader;
use Illuminate\Database\Eloquent\Model;

class CourseHeaderRepository extends BaseRepository
{
    public function __construct(CourseHeader $model)
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

        public function courseHeaderSelectedWithMonth(){
            return $this->model->where("header_key", request()->course_header)->first()->month;
        }

        public function courseHeaderSelectedWithTotalUnits()
        {
           return $this->model->where('header_key', request()->course_header)->first();
        }


        public function update(array $data, $id)
        {
            $record = $this->model->findorFail($id);
            $record->update($data);
            return $record;
        }

        public function selectCourseHeaderWithHeaderKey($filter)
        {
            return $this->model->where(function($query) use($filter) {
                foreach($filter as $value) {
                    $query->orWhere('header_key', $value);
                }})->get();
        }

        public function selectCourseHeader()
        {
            return $this->model->where('header_key', request()->course_header)->first();
        }


}
