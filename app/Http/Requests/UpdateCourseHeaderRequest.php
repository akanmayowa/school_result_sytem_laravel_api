<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseHeaderRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'header_key' => 'sometimes|string',
            'description' => 'sometimes|string',
            'cadre' => 'sometimes|nullable|string',
            'delete_status' => 'sometimes|integer',
            'add_year' => 'sometimes',
            'month' => 'sometimes|string',
            'index_code' => 'sometimes|string'
        ];
    }
}
