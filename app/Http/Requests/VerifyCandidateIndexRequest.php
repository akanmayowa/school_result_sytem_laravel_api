<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyCandidateIndexRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
 public function rules(): array
 {
        return [
            'candidate_index' => 'required|exists:candidate_indexings,candidate_index',
        ];
    }
}
