<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('response')) {
    function response($data = [], $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers, JSON_PRETTY_PRINT);
    }
}

if (!function_exists('abort')) {
    function abort($code, $message = '', array $headers = [])
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message ?: 'Error ' . $code]);
        exit();
    }
}