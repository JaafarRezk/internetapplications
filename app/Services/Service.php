<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

abstract class Service {

    public function response($success = false, $data = [], $message = null): JsonResponse
    {
        return response()->json(
            [
                "success" => $success,
                "data" => $data,
                "message" => $message,
            ]
        );
    }
}
