<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest {

    protected function formatErrors(Validator $validator) {
        $errors = $validator->errors()->getMessages();
        $transformed = [];

        foreach ($errors as $field => $messages) {
            $transformed[$field] = $messages[0];
        }

        return $transformed;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = response()->json([
            'status' => false,
            'message' => $this->formatErrors($validator),
        ], 422);

        throw new HttpResponseException($response);
    }

}
