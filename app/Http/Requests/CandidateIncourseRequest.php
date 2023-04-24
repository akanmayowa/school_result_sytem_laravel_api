<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidateIncourseRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'candidate_index' => 'required|string',
            'course_header' => 'required|string',
            'school_code' => 'required|string',
            'first_semester_exam' => 'required|integer',
            'second_semester_exam' => 'required|integer',
            'third_semester_exam' => 'required|integer',
            'operator' => 'required|string',
            'total_score' => 'required|integer',
            'average_score' => 'required|integer',
            'exam_id' => 'required|integer',
            'new' =>  'required|integer',
        ];
    }
}
