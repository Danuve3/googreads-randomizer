<?php

class Auth
{
    public static function generateToken(string $password): string
    {
        return hash_hmac('sha256', $password, APP_SECRET);
    }

    public static function validatePassword(string $password): bool
    {
        return hash_equals(APP_PASSWORD, $password);
    }

    public static function validateToken(string $token): bool
    {
        $expected = self::generateToken(APP_PASSWORD);
        return hash_equals($expected, $token);
    }

    public static function requireAuth(): void
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            Response::error('Token requerido', 401);
        }
        if (!self::validateToken($matches[1])) {
            Response::error('Token inválido', 401);
        }
    }

    public static function login(string $password): array
    {
        if (!self::validatePassword($password)) {
            Response::error('Contraseña incorrecta', 401);
        }
        return ['token' => self::generateToken($password)];
    }
}
