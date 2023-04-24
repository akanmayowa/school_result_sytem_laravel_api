<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|string',
            'phone_number' => 'required|string',
            'name' => 'required|string',
             'photo' => 'required|image:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
