<?php

namespace App\Services;

use App\Models\Nationality;
use App\Repositories\BaseRepository;
use App\Http\Requests\NationalityRequest;
use App\Traits\ResponsesTrait;

class NationalityServices extends BaseRepository{

    use ResponsesTrait;
    public ? Nationality $nationality = null;

    public function __construct(Nationality $nationality)
    {
            $this->nationality = $nationality;
    }

    public function fetchAllNationalities()
    {
        return $this->nationality->all();
    }


    public function createNationality(array $array)
    {
         $nationality = $this->nationality->create($array);
         return $this->successResponse($nationality, "Nationality created successfully");
    }
}
