<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrintDocumentRequest extends BaseRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'exam_year' => 'required|digits:4|integer|min:1900',
            'school_code' => 'required',
            'course_header' => 'required|exists:course_headers,header_key',
            'print_type' => 'required|in:verified_candidates,unverified_candidates'
        ];
    }
}
