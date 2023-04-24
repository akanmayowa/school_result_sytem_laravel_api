<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NationalityRequest extends BaseRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'name' => 'required|max:255|string',
            'code' => 'required|max:255|string',
        ];
    }
}
