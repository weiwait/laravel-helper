<?php

use \Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\VarDumper\VarDumper;

/*
 * @return \App\Models\User | \App\User | null
 * */
function authUser() {
    return Auth::user();
}

function message(string $message, int $code = 1, int $status = 200, $jsonOption = JSON_UNESCAPED_UNICODE): JsonResponse {
    return response()->json([
        'message' => $message,
        'code' => $code,
        'status' => $status,
    ], 200, [], $jsonOption);
}

function error(string $message, int $code = 0, int $status = 400, $jsonOption = JSON_UNESCAPED_UNICODE): JsonResponse {
    return response()->json([
        'message' => $message,
        'code' => $code,
        'status' => $status,
    ], 200, [], $jsonOption);
}

function success($data, int $code = 2, int $status = 200, $jsonOption = JSON_UNESCAPED_UNICODE): JsonResponse {
    if (empty($data)) {
        $data = [];
    }

    if ($data instanceof JsonResource) {
        if ($data->resource instanceof  AbstractPaginator) {
            return response()->json([
                ...$data->resource->toArray(),
                'code' => $code,
                'status' => $status,
            ], 200, [], $jsonOption);
        }
    }

    return response()->json([
        'data' => $data,
        'code' => $code,
        'status' => $status,
    ], 200, [], $jsonOption);
}

function gg(...$vars)
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: *');
    http_response_code(500);

    foreach ($vars as $v) {
        VarDumper::dump($v);
    }

    exit(1);
}
