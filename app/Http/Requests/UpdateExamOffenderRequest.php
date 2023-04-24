<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamOffenderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
                'candidate_index' => 'required|exists:candidate_indexings,candidate_index',
                'course_header' => 'required|exists:course_headers,header_key',
                'exam_offence_id' => 'required|exists:exam_offences,id',
                'exam_date' => 'required',
                'registration_date' => 'required',
                'duration' => 'sometimes',
                'comment' => 'nullable',
                'school_code' => 'required'
        ];
    }
}
