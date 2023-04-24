<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponsesTrait
{
    public function successResponse(mixed $data = [], string $message = ''): JsonResponse
    {
        return response()->json(['success' => true,'data' => $data, 'message' => isset($data['message']) ? $data['message'] : $message]);
    }

    public function errorResponse(string $message = '', int $error_code = 401): JsonResponse
    {
        return response()->json([ 'success' => false, 'message' => $message], $error_code);
    }
}
