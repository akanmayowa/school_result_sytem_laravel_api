<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrintCourseRegStatisticsDocumentRequest extends BaseRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'exam_year' => 'required',
            'course_header' => 'required|exists:course_headers,header_key',
        ];
    }
}
