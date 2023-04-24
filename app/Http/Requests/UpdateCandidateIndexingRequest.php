<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateIndexingRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules(): array
    {
        return [
//            'candidate_index' => 'required|string|exists:candidate_indexings',
            'school_code' => 'filled|string',
            'first_name' => 'required|string',
            'title' => 'filled|string',
            'middle_name' => 'filled|string',
            'last_name' => 'filled|string',
            'candidate_category' => 'filled|string',
            'years_of_experience' => 'filled|string',
            'course_header' => 'filled|string',
            'marital_status' => 'filled|string',
            'english' => 'filled|string',
            'biology' => 'filled|string',
            'health_science' => 'filled|string',
            'chemistry' => 'filled|string',
            'mathematics' => 'filled|string',
            'geography' => 'filled|string',
            'economics' => 'filled|string',
            'food_and_nutrition' => 'filled|string',
            'accounting' => 'filled|string',
            'commerce' => 'filled|string',
            'physics' => 'filled|string',
            'technical_drawing' => 'filled|string',
            'integrated_science' => 'filled|string',
            'general_science' => 'filled|string',
            'agric' => 'filled|string',
            'seatings' => 'filled|integer',
            'reg_nurse' => 'filled|string',
            'reg_midwife' => 'filled|string',

            'verify_birth_certificate' => 'filled|integer',
            'verify_o_level' => 'filled|integer',
            'verify_marriage_certificate' => 'filled|integer',
            'verify_credentials' => 'filled|integer',
            'certificate_$_75' => 'filled|integer',
            'letter_of_reference' => 'filled|integer',
            'on_course' => 'filled|string',
            'degree_holder' => 'filled|integer',
            'form_no' => 'filled|string',
            'verify_status' => 'filled|integer',
            'verify_status_2' => 'filled|integer',
            'nationality' => 'filled|string',
            'yoruba' => 'filled|string',
            'igbo' => 'filled|string',
            'hausa' => 'filled|string',
            'history' => 'filled|string',
            'religious_knowledge' => 'filled|string',
            'government' => 'filled|string',
            'literature' => 'filled|string',
            'photo' => 'sometimes|image:jpeg,png,jpg,gif,svg|max:2048',
            'birth_certificate_upload' => 'sometimes|image:jpeg,png,jpg,gif,svg|max:2048',
            'marriage_certificate_upload' => 'sometimes|image:jpeg,png,jpg,gif,svg|max:2048',
            'olevel_certificate_upload' => 'sometimes|image:jpeg,png,jpg,gif,svg|max:2048',
            'olevel_2_certificate_upload' => 'sometimes|image:jpeg,png,jpg,gif,svg|max:2048',
            'phn_certificate_upload' => 'sometimes|image:jpeg,png,jpg,gif,svg|max:2048',
            'phn_2_certificate_upload' => 'sometimes|image:jpeg,png,jpg,gif,svg|max:2048',
            'nd_certificate_upload' => 'sometimes|image:jpeg,png,jpg,gif,svg|max:2048',
            'hnd_certificate_upload' => 'sometimes|image:jpeg,png,jpg,gif,svg|max:2048',
            'gender' => 'filled|string',
            'dont_det' => 'filled|integer',
            'year_of_certificate_evaluated' => 'filled|string',
            'year_of_certificate_evaluated_2' => 'filled|string',
            'exam_number_1' => 'filled|string',
            'exam_number_2' => 'filled|string',
            'exam_month' => 'filled|string',
            'exam_month_2' => 'filled|string',
            'date_of_birth' => 'filled|date',
        ];
    }
}
