<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseHeaderRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'header_key' => 'required|string|unique:course_headers',
            'description' => 'required|string',
            'cadre' => 'sometimes|nullable|string',
            'delete_status' => 'sometimes|integer',
//            'total_units' => 'required|integer',
//            'modules' => 'required|integer',
            'exam_date' => 'sometimes|string',
            'add_year' => 'required|string',
            'month' => 'required|string',
            'index_code' => 'required|string'
        ];
    }
}
