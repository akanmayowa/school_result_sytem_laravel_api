<?php

namespace App\Services;

use App\Notifications\TwoFactorAuthCodeNotification;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponsesTrait;
use App\Models\User;

class TrainingSchoolAuthServices
{
    use ResponsesTrait;

    public ?UserRepository $user_repository = null;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }


    public function trainingSchoolUserLogin()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors());
        }
        $data = [
            'email' => request()->email,
            'password' => request()->password
        ];
        if (auth()->attempt($data)) {
            auth()->user()->generateTwoFactorAuthCode();
            auth()->user()->notify(new TwoFactorAuthCodeNotification());
            return $this->createNewToken(auth()->attempt($data));
        } else {
            return $this->errorResponse(" error login");
        }
    }

    public function userLoginAccessType()
    {
        if (auth()->user()->user_role == 'super_admin') {
            return $this->successResponse('Super Admin User: login successfully hurray!');
        } else if (auth()->user()->user_role == 'admin') {
            return $this->successResponse('Admin User: login successfully hurray!');
        } else if (auth()->user()->user_role == 'school_admin') {
            return $this->successResponse('School Admin User: login successfully hurray!');
        } else if (auth()->user()->user_role == 'training_school_admin') {
            return $this->successResponse('School Admin User: login successfully hurray!');
        } else {
            return $this->successResponse('Normal User: login successfully hurray!');
        }
    }


    public function logout()
    {
        auth()->logout();
        return $this->successResponse('User successfully logged out');
    }


    public function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 3600,
            'user' => auth()->user(),
            'user_role' => $this->userLoginAccessType(),
            "message" => "Two factor authentication verification successfully"
        ]);
    }


    public function resendTwoFactorCode()
    {
        auth()->user()->generateTwoFactorAuthCode();
        auth()->user()->notify(new TwoFactorAuthCodeNotification());
        return back()->with('success', 'The two factor code has been sent again');
    }


    public function verifyTwoFactorCodeForTrainingSchoolUser()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'two_factor_code' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors());
        }
        return $this->trainingSchoolUserVerification();
    }

    public function trainingSchoolUserVerification()
    {
        $user = User::where([['email', request()->email], ['two_factor_code', request()->two_factor_code]])->first();
        if ($user) {
            auth()->login($user, true);
            $user->resetTwoFactorAuthCode();
            $token = auth()->login($user);
            return $this->createNewToken($token);
        } else {
            return response(["status" => 401, 'message' => 'Invalid']);
        }
    }
}
