<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseModuleRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'course_key'  => 'required|string',
            'description'  => 'required|string',
            'credits'  => 'required|integer',
            'serial_number'  => 'required|integer',
            'delete_status'  => 'sometimes|boolean',
            'practical'  => 'sometimes|boolean',
            'header_key'  => 'required|string',
        ];
    }
}
