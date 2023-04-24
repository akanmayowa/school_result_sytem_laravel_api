<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\Nationality;

class NationalityRepository extends BaseRepository
{

    public function __construct(Nationality $model)
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

}
