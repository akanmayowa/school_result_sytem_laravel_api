<?php /** @noinspection ALL */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidateIndexingRequest extends BaseRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
      return [
//          'candidate_index' => 'string',
          'school_code' => 'required|string|exists:training_schools,school_code',
          'first_name' => 'required|string',
          'title' => 'nullable|string',
          'middle_name' => 'nullable|string',
          'last_name' => 'required|string',
          'candidate_category' => 'required|string',
          'years_of_experience' => 'nullable|string',
          'course_header' => 'required|string|exists:course_headers,header_key',
          'marital_status' => 'nullable|string',
          'english' => 'nullable|string',
          'biology' => 'nullable|string',
          'health_science' => 'nullable|string',
          'chemistry' => 'nullable|string',
          'mathematics' => 'nullable|string',
          'geography' => 'nullable|string',
          'economics' => 'nullable|string',
          'food_and_nutrition' => 'nullable|string',
          'accounting' => 'nullable|string',
          'commerce' => 'nullable|string',
          'physics' => 'nullable|string',
          'technical_drawing' => 'nullable|string',
          'integrated_science' => 'nullable|string',
          'general_science' => 'nullable|string',
          'agric' => 'nullable|string',
          'seatings' => 'nullable|integer',
          'reg_nurse' => 'nullable|string',
          'reg_midwife' => 'nullable|string',

          'verify_birth_certificate' => 'nullable|integer',
          'verify_o_level' => 'nullable|integer',
          'verify_marriage_certificate' => 'nullable|integer',
          'verify_credentials' => 'nullable|integer',
          'certificate_$_75' => 'nullable|integer',
          'letter_of_reference' => 'nullable|integer',
          'on_course' => 'nullable|string',
          'degree_holder' => 'nullable|integer',
          'form_no' => 'nullable|string',
          'verify_status' => 'nullable|integer',
          'verify_status_2' => 'nullable|integer',
          'nationality' => 'required|string',
          'yoruba' => 'nullable|string',
          'igbo' => 'nullable|string',
          'hausa' => 'nullable|string',
          'history' => 'nullable|string',
          'religious_knowledge' => 'nullable|string',
          'government' => 'nullable|string',
          'literature' => 'nullable|string',
          'photo' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
          'birth_certificate_upload' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
          'marriage_certificate_upload' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
          'olevel_certificate_upload' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
          'olevel_2_certificate_upload' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
          'phn_certificate_upload' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
          'phn_2_certificate_upload' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
          'nd_certificate_upload' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
          'hnd_certificate_upload' => 'nullable|image:jpeg,png,jpg,gif,svg|max:2048',
          'gender' => 'required|string',
          'dont_det' => 'nullable|integer',
          'year_of_certificate_evaluated' => ' nullable|string',
          'year_of_certificate_evaluated_2' => 'nullable|string',
          'exam_number_1' => 'required|string',
          'exam_number_2' => 'nullable|string',
          'exam_month' => 'string',
          'exam_month_2' => 'nullable|string',
          'date_of_birth' => 'required|date',
          'certificate_evaluated' => 'sometimes'
      ];
    }
}

