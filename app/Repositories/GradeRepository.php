<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\Grade;


class GradeRepository extends BaseRepository
{

    public function __construct(Grade $model)
    {
        parent::__construct($model);
    }

    public function create(array $data)
    {
        return parent::create($data);
    }

    public function all()
    {
        return parent::all();
    }
}
