<?php

namespace App\Services;

use App\Helpers\DigitalOceanSpace;
use App\Models\TrainingSchool;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\ResponsesTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserProfileService
{
    use ResponsesTrait;

    public function fetchAuthenticatedUser()
    {
        $user = (new User())->where('id', auth()->user()->id)->first();
        return $this->successResponse($user,'Authenticated User Data Retrieved');
    }

    public function fetchUserById($id)
    {
        $user = (new User())->find($id);
        return $this->successResponse($user,'User Data Retrieved');
    }

    public function userProfileUpdate()
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make(request()->all(), [
                'email' => 'required|string',
                'phone' => 'sometimes|string',
                'name' => 'required|string',
                'photo' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($validator->fails()) return $this->errorResponse($validator->errors());
            $image = request()->file('photo');
            if ($image) {
                $public_id = DigitalOceanSpace::uploadImage('user_profile_picture_upload', $image);
                (new UserRepository(new User()))->update(array_merge($validator->validated(), ['photo' => $public_id]), auth()->user()->id);
                DB::commit();
                return $this->successResponse(auth()->user()->refresh(), 'User Profile Updated Successfully.');
            }
            if (empty($image)) {
                (new UserRepository(new User()))->update(array_merge($validator->validated()), auth()->user()->id);
                DB::commit();
                return $this->successResponse(auth()->user()->refresh(), 'User Profile Updated Successfully.');
            }
        }
        catch (\Throwable $exception) {
            DB::rollback();
            throw new Exception(''. $exception);
        }
    }

    public function userProfileImage()
    {
        DB::beginTransaction();
            try {
                $validator = Validator::make(request()->all(),['photo' => 'required|image:jpeg,png,jpg,gif,svg|max:2048',]);
                if($validator->fails()) return $this->errorResponse( $validator->errors());
                $image = request()->file('photo');
                $public_id = DigitalOceanSpace::uploadImage('user_profile_picture_upload', $image);
                (new UserRepository(new User()))->update(['photo' =>  $public_id ], auth()->user()->id);
        DB::commit();
                return $this->successResponse(auth()->user()->refresh(),'User Profile Image Updated Successfully.');
            }
        catch (\Throwable $exception) {
        DB::rollback();
                throw new Exception(''. $exception);
        }
    }

    public function userProfileUpdateForTrainingSchool()
    {
        DB::beginTransaction();
            try {
                $validator = Validator::make(request()->all(),[
                    'name' => 'required|string',
                    'email' => 'required|string',
                    'photo' => 'required|image:jpeg,png,jpg,gif,svg|max:2048',
                ]);
                if($validator->fails()) return $this->errorResponse( $validator->errors());
                $image = request()->file('photo');
                $public_id = DigitalOceanSpace::uploadImage('photo', $image);
                $photo = $public_id;
                (new UserRepository(new User()))->updateTrainingSchoolRelation(auth()->user()->id, $photo, request()->email,
                    request()->name,  new TrainingSchool());
            DB::commit();
                return $this->successResponse(auth()->user()->refresh(),'Training School User Profile Updated Successfully.');
            }
            catch (\Throwable $exception) {
        DB::rollback();
            throw new Exception(''. $exception);
            }

    }

    public function userProfileImageForTrainingSchool()
    {
        DB::beginTransaction();
            try {
                $validator = Validator::make(request()->all(),[ 'photo' => 'required|image:jpeg,png,jpg,gif,svg|max:2048',]);
                if($validator->fails()) return $this->errorResponse( $validator->errors());
                $image = request()->file('photo');
                $public_id = DigitalOceanSpace::uploadImage('photo', $image);
                $data = (new UserRepository(new User()))->updateTrainingSchoolRelationAndUserImage(auth()->user()->id,
                    $public_id, new TrainingSchool());
                DB::commit();
                return $this->successResponse($data,'Training School User Profile Image Updated Successfully.');
            }
            catch (\Throwable $exception) {
        DB::rollback();
            throw new Exception(''. $exception);
        }
    }


}


