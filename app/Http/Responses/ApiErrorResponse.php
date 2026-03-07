<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiErrorResponse
{
    /**
     * @param string $detail
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function detail(string $detail, int $status): JsonResponse
    {
        return response()->json([ "detail" => $detail, ], $status);
    }
}
