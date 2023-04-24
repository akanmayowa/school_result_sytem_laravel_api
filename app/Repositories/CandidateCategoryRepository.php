<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\CandidateCategory;

class CandidateCategoryRepository extends BaseRepository
{

    public function __construct(CandidateCategory $model)
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
