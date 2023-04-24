<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrintZonalMarkSheetDocumentRequest extends BaseRequest
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
            'course_key' => 'required|exists:course_modules,course_key'
        ];
    }
}
