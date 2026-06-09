<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiErrorResponse
{
    /**
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function message(string $message, int $status): JsonResponse
    {
        return response()->json([ "detail" => $message, ], $status);
    }
}
