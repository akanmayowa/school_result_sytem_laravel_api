<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TwoFactorAuthentication;
use App\Notifications\TwoFactorAuthCode;
use Illuminate\Support\Facades\Auth;
use App\Services\UserServices;

class TwoFactorAuthController extends Controller
{
        public ?UserServices $user_services = null;

        public function __construct(UserServices $user_services)
        {
            $this->user_services = $user_services;
        }

        public function verifyTwoFactorCode()
        {
            return $this->user_services->verifyTwoFactorCode();
        }


        public function resendTwoFactorCode()
        {
            return $this->user_service->resendTwoFactorCode();
        }
}
