<?php
namespace App\Services;

use App\Repositories\StateRepository;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponsesTrait;
use App\Http\Resources\StateResource;


class StateServices{

    use ResponsesTrait;
    public ?StateRepository $state_repository = null;

    public function __construct(StateRepository $state_repository)
    {
        $this->state_repository = $state_repository;
    }

    public function fetchAllState()
    {
        $state = $this->state_repository->all();
        return $this->successResponse(StateResource::collection($state), 'State Retrieved Successfully.');
    }

    public function createState(array $request)
    {
        $state =  $this->state_repository->create($request);
        return $this->successResponse(new StateResource($state), 'State Created Successfully.');
    }


}
