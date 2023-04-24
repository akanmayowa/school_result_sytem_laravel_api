<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Services\PasswordServices;
use Illuminate\Http\Request;

class PasswordController extends Controller
{

    public function changePassword()
    {
        return (new PasswordServices())->changeUserPassword();
    }

    public function forgetPassword()
    {
        return (new PasswordServices())->forgetUserPassword();
    }

    public function resetPassword()
    {
        return (new PasswordServices())->resettingPassword();
    }
}
