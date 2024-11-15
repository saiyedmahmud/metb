<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ErrorTrait
{
    public function response($data, $code = ResponseAlias::HTTP_OK): JsonResponse
    {
        $converted = $this->arrayKeysToCamelCase($data);
        return response()->json($converted, $code);
    }

    public function arrayKeysToCamelCase($array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $key = Str::camel($key);
            if (is_array($value)) {
                $value = $this->arrayKeysToCamelCase($value);
            }
            $result[$key] = $value;
        }
        return $result;
    }

    public function success(string $message): JsonResponse
    {
        return response()->json(["message" => $message], ResponseAlias::HTTP_OK);
    }

    public function conflict(string $error): JsonResponse
    {
        return response()->json(["error" => $error], ResponseAlias::HTTP_CONFLICT);
    }

    public function badRequest(string $error): JsonResponse
    {
        return response()->json(["error" => $error], ResponseAlias::HTTP_BAD_REQUEST);
    }

    public function notFound($error): JsonResponse
    {
        return response()->json(["error" => $error], ResponseAlias::HTTP_NOT_FOUND);
    }

    public function unauthorized(string $error): JsonResponse
    {
        return response()->json(["error" => $error], ResponseAlias::HTTP_UNAUTHORIZED);
    }

    public function forbidden($err): JsonResponse
    {
        return response()->json(["error" => $err], ResponseAlias::HTTP_FORBIDDEN);
    }
}
