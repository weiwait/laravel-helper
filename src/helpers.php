<?php

use \Illuminate\Http\JsonResponse;
use App\User;

function authUser(): User {
    return \Illuminate\Support\Facades\Auth::user();
}

function message(string $message, int $code = 1, int $status = 200): JsonResponse {
    return response()->json([
        'message' => $message,
        'code' => $code,
        'status' => $status,
    ], 200);
}

function error(string $message, int $code = 0, int $status = 400): JsonResponse {
    return response()->json([
        'message' => $message,
        'code' => $code,
        'status' => $status,
    ], 200);
}

function success($data, int $code = 2, int $status = 200): JsonResponse {
    if (empty($data)) {
        $data = [];
    }

    return response()->json([
        'data' => $data,
        'code' => $code,
        'status' => $status,
    ], 200);
}
