<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamOffenderRequest extends BaseRequest
{


     public function authorize()
     {
        return true;
    }

    public function rules()
    {
        return [
            'candidate_index' => 'required|exists:candidate_indexings,candidate_index',
            'header_key' => 'required',
            'exam_offence_id' => 'required|exists:exam_offences,id',
            'exam_date' => 'required',
            'registration_date' => 'required',
            'duration' => 'required_if:exam_offence_id,5,',
            'comment' => 'nullable',
            'school_code' => 'required',
        ];
    }
}
