<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'code' => 'required|string|unique:states',
            'name' => 'required|string|unique:states'
        ];
    }
}
