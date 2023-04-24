<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{

    public mixed $current_password;
    public mixed $new_password;

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ];
    }
}
