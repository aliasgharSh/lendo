<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse($message, $data = null, $statusCode = 200)
    {
        return response()->json([
            "success"=> true,
            "massage" => $message,
            "data" => $data,
        ],$statusCode);
    }
    protected function errorResponse($message,$statusCode=400)
    {
        return response()->json([
            "success"=> false,
            "massage" => $message,
        ],$statusCode);
    }
}
