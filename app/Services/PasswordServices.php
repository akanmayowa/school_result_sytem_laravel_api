<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\ResponsesTrait;
use App\Repositories\UserRepository;
use App\Notifications\ForgetPasswordNotification;
use App\Models\User;
use App\Models\PasswordReset;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;

class PasswordServices
{
    public function __construct(){

    }

    use ResponsesTrait;
    public function changeUserPassword()
    {
        $validator = Validator::make(request()->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',        ]);

        if($validator->fails()){
            return $this->errorResponse( $validator->errors());
        }

        if(!Hash::check(request()->current_password, auth()->user()->password))
        {
            return $this->errorResponse("Current Password is incorrect");
        }


        if (Hash::check(request()->current_password , auth()->user()->password))
        {
            if (!Hash::check(request()->password , auth()->user()->password))
            {
                (new UserRepository(new User()))->changeUserPassword(auth()->user()->id,request()->password);
                return $this->successResponse("Password Changed Success!");
            }
        else{
            return $this->errorResponse("New Password Cant Be the Current Password");
        }
        }

    }


    public function forgetUserPassword()
    {
        $validator = Validator::make(request()->all(), ['email' => 'required|email', ]);
        if($validator->fails()){ return $this->errorResponse( $validator->errors()); }
        $token = Str::random(4);
        return (new UserRepository(new User()))->checkIfEmailExist(request()->email,$token);
    }


    public function resettingPassword()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);
        if($validator->fails()){ return $this->errorResponse( $validator->errors()); }
         return (new UserRepository(new User()))->passwordReset(request()->email, request()->token, request()->password);
    }

}
