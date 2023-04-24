<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolResitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'candidate_index' => 'required',
            'exam_id' => 'required|min:4|max:4',
            'course_header' => 'required',
            'course_keys' => 'required|array',
            'school_code' => 'sometimes'
        ];
    }
}
