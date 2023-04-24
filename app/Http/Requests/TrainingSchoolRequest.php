<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingSchoolRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'school_code' => 'required|string|unique:training_schools',
            'index_code' => 'required|string',
            'state_id' => 'required|integer',
            'school_name'  => 'required|string',
            'school_category_id' => 'required|integer',
            'contact' => 'required|string',
            'position' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email|unique:training_schools',
            'status' => 'required|integer',
            'password' => 'required|string',
       ];
    }
}
