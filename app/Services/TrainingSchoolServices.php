<?php /** @noinspection ALL */

namespace App\Services;

use App\Helpers\DigitalOceanSpace;
use App\Helpers\GeneralLogs;
use App\Http\Requests\TrainingSchoolRequest;
use App\Http\Resources\TrainingSchoolResource;
use App\Models\TrainingSchool;
use App\Models\User;
use App\Notifications\TrainingSchoolNotification;
use App\Repositories\TrainingSchoolRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponsesTrait;
use Illuminate\Support\Facades\Gate;

class TrainingSchoolServices{

      use ResponsesTrait;
      public ?TrainingSchoolRepository $training_school_repository = null;
      public ? UserRepository $user_repository = null;

    public function __construct(TrainingSchoolRepository $training_school_repository, UserRepository $user_repository){
            $this->training_school_repository = $training_school_repository;
            $this->user_repository = $user_repository;
            $this->user = new User();
      }

      public function fetchAllTrainingSchools()
      {
            $training_school = TrainingSchool::orderBy('school_code','asc')->get();
             return $this->successResponse(TrainingSchoolResource::collection($training_school),'TrainingSchool Retrieved Successfully.');
      }

    public function fetchAllTrainingSchoolsWithStatusNotNull()
    {
        $training_school = TrainingSchool::where('status', 1)->orderBy('school_code','asc')->get();
        return $this->successResponse(TrainingSchoolResource::collection($training_school),'TrainingSchool Retrieved Successfully.');
    }

    public function validatedData(){
         return Validator::make(request()->all(), [
              'index_code' => 'required|string|unique:training_schools,index_code',
              'state_id' => 'required|integer',
              'school_name'  => 'required|string|unique:training_schools,school_name',
              'school_category_id' => 'required|integer',
              'contact' => 'required|string',
              'position' => 'required|string',
              'phone' => 'required|string|unique:training_schools,phone',
              'email' => 'required|email|unique:training_schools,email',
              'school_code' => 'required|string|unique:training_schools,school_code|max:5',
              'status' => 'required|integer',
              'password' => 'required|string',
              'fax' => 'sometimes|string|unique:training_schools'
          ]);
      }
    /**
     * @throws Exception
     */
    public function createTrainingSchool()
        {
            DB::beginTransaction();
                try {
                    $validator = $this->validatedData();
                    if($validator->fails()){
                        return $this->errorResponse($validator->errors());
                    }
                    $check_if_school_code_exist = User::where('operator_id', request()->input('school_code'))->first();
                    if($check_if_school_code_exist){
                        return $this->errorResponse('School Operator ID Already Exist');
                    }
                    $check_if_school_name_exist = User::where('name', request()->input('school_name'))->first();
                    if(($check_if_school_name_exist))
                    {
                        return $this->errorResponse('School Name Already Used');
                    }

                    $training_school = TrainingSchool::create(array_merge($validator->validated(), ['password' => bcrypt(request()->password)]));
                    $training_school->userTrainingSchool()->create(
                                            ['operator_id' => request()->input('school_code'),
                                            'name' => request()->input('school_name'),
                                            'email' => request()->input('email'),
                                            'password' => bcrypt(request()->input('password')),
                                            'user_status' => \App\Enums\UserStatus::Active,
                                            'user_role' => 'training_school_admin',
                                            'user_id' => $training_school->id,
                    ]);
                    auth()->user()->notify(new TrainingSchoolNotification($training_school));
                    DB::commit();
                    $data = ['training_school' => $training_school ];
                    return $this->successResponse($data, 'Training School Created Successfully.');
                }
                catch ( \Exception $exception) {
                DB::rollback();
                    throw $exception;
                }
        }


        public function deleteTrainingSchool($id)
        {
            if(Gate::allows('isAdmin')){
                $this->training_school_repository->delete($id);
                return $this->successResponse('TrainingSchool successfully deleted');
            }else{
                return $this->errorResponse(' You have been restricted from performing such action');
            }
        }


    public function validatedDataForUpdate(){
        return Validator::make(request()->all(), [
            'school_code' => 'filled|string',
            'index_code' => 'filled|string',
            'state_id' => 'filled|integer',
            'school_name'  => 'filled|string',
            'school_category_id' => 'filled|integer',
            'contact' => 'filled|string',
            'position' => 'filled|string',
            'phone' => 'sometimes|string:unique:users',
            'email' => 'filled|email',
            'photo' => 'filled|image:jpeg,png,jpg,gif,svg|max:2048',
            'fax_number' => 'sometimes|string'
        ]);
    }

    public function updateTrainingSchool($id): \Illuminate\Http\JsonResponse
        {
            DB::beginTransaction();
            try {
                $validator = $this->validatedDataForUpdate();

            if($validator->fails()){
                return $this->errorResponse($validator->errors());
            }
            $training_school = TrainingSchool::where('id',$id)->first();

            if(!$training_school){
                return $this->errorResponse("Training school Id Doesnt exist");
            }
            $result = [
                'email' => request()->input('email') ?? $training_school->email,
                'school_code' => request()->input('school_code') ?? $training_school->school_code,
                'phone' => request()->input('phone') ?? $training_school->phone,
                'school_name' => request()->input('school_name') ?? $training_school->name,
                'state_id' => request()->input('state_id') ?? $training_school->state_id
            ];
                $image = request()->file('photo');
                if($image){
                    $public_id = DigitalOceanSpace::uploadImage('photo', $image);
                    $training_school->update(array_merge($validator->validated(),['photo' => $public_id]));
                    $selected_user = $this->user->where('training_school_id', $training_school->id)->first();
                    if($selected_user){
                        $datum = [
                            'operator_id' => $training_school->school_code,
                            'email' => $training_school->email,
                            'name' => $training_school->school_name,
                           'user_role' => 'training_school_admin',
                        ];
                        $selected_user->update($datum + ['photo' => $public_id]);
                    }
                }
                if(!$image){
                    $training_school->update(array_merge($validator->validated()));
                    $selected_user = $this->user->where('training_school_id', $training_school->id)->first();
                    if($selected_user){
                        $datum = [
                            'operator_id' => $training_school->school_code,
                            'email' => $training_school->email,
                            'name' => $training_school->school_name,
                            'user_role' => 'training_school_admin',
                        ];
                        $selected_user->update($datum);
                    }
                }
                DB::commit();
                return $this->successResponse($training_school, 'Training School Updated Successfully.');
            }
            catch (\Throwable $exception) {
                DB::rollback();
                throw new Exception(''. $exception);
            }
        }

   public function changeTrainingSchoolStatus($id)
        {
            $validator = Validator::make(request()->all(),['status' => 'required|integer|max:1|between:0,1']);
            if($validator->fails()){ return $this->errorResponse( $validator->errors()); }
            $training_school = $this->training_school_repository->show($id);
            $training_school->status = request()->status;
            $training_school->save();
            return $this->successResponse(new TrainingSchoolResource($training_school), 'Training School Status Change Successfully.');
        }

    public function changeCanRegiterStatus()
    {
        $schools = TrainingSchool::where('id', request()->input('id'))->first();

        if(empty($schools)){
            return $this->errorResponse("Invalid Training School Inputted");
        }

        if($schools->can_register == 1)
        {
            $schools->update(['can_register' => 0]);
            return $this->successResponse($schools->refresh(),'Training School Cant Register A Candidate');
        }

        if($schools->can_register == 0)
        {
            $schools->update(['can_register' => 1 ]);
            return $this->successResponse($schools->refresh(), 'Training School Can Register A Candidate');
        }
    }



    public function loginInSchoolDetails()
    {
        $training_school = TrainingSchool::where('school_code', auth()->user()->operator_id)->first();
        return $this->successResponse($training_school);
    }


}

