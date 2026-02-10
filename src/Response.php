<?php

class Response
{
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success(mixed $data = null, string $message = 'OK'): void
    {
        self::json(['ok' => true, 'message' => $message, 'data' => $data]);
    }

    public static function error(string $message, int $status = 400): void
    {
        self::json(['ok' => false, 'message' => $message], $status);
    }
}
