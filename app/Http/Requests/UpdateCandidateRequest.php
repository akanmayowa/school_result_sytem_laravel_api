<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'candidate_index' => 'nullable|string',
            'school_code' => 'nullable|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'candidate_category' => 'nullable|string',
            'years_of_experience' => 'nullable',
            'course_header' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'english' => 'nullable|string',
            'biology' => 'nullable|string',
            'health_science' => 'nullable|string',
            'chemistry' => 'nullable|string',
            'mathematics' => 'nullable|string',
            'geography' => 'nullable|string',
            'food_and_nutrition' => 'nullable|string',
            'accounting' => 'nullable|string',
            'commerce' => 'nullable|string',
            'physics' => 'nullable|string',
            'technical_drawing' => 'nullable|string',
            'economics' => 'nullable|string',
            'integrated_science' => 'nullable|string',
            'general_science' => 'nullable|string',
            'agric' => 'nullable|string',
            'nationality' => 'nullable|string',
            'yoruba' => 'nullable|string',
            'igbo' => 'nullable|string',
            'hausa' => 'nullable|string',
            'history' => 'nullable|string',
            'religious_knowledge' => 'nullable|string',
            'government' => 'nullable|string',
            'literature' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'exam_year' => 'nullable',
            'photo' => 'nullable|image|mimes:png,jpg,jpeg',
            'birth_certificate_upload' => 'nullable|image|mimes:png,jpg,jpeg|max:200',
            'marriage_certificate_upload' => 'nullable|image|mimes:png,jpg,jpeg|max:200',
        ];
    }
}
