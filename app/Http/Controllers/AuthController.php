<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use App\Services\UserServices;

class AuthController extends Controller
{
      public ?UserServices $user_services = null;
      public function __construct(UserServices $user_services)
        {
            $this->user_services = $user_services;
            $this->middleware('auth:api', ['except' => ['login', 'register','resetTwoFactorAuthenticationCode']]);
        }

      public function logout()
        {
            return $this->user_services->logout();
        }

      public function register()
        {
            return $this->user_services->userRegistration();
        }

      public function refresh()
        {
            return $this->user_services->createNewToken(auth()->refresh());
        }

      public function login()
        {
            return $this->user_services->userLogin();
        }

        public function resetTwoFactorAuthenticationCode()
        {
            return $this->user_services->resendingTwoFactorCodeAuthToUserEmail();
        }




}



