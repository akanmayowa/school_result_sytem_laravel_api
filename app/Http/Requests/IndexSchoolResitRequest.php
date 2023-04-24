<?php /** @noinspection ALL */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexSchoolResitRequest extends BaseRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'subject_code' => 'nullable|string',
            'course_header' => 'nullable|string',
            'exam_date' => 'nullable|string',
        ];
    }
}
