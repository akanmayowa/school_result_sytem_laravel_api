<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends BaseRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
             'candidate_index' => 'required|exists:candidate_indexings,candidate_index',
             'exam_id' => 'required|max:4|min:4',
             'course_header' => 'required'
        ];
    }
}
