<?php
declare(strict_types=1);

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function validate(?string $token): bool
    {
        if (!isset($_SESSION['csrf_token']) || !is_string($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function fromRequest(): ?string
    {
        if (isset($_POST['_csrf'])) {
            return (string)$_POST['_csrf'];
        }

        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            return (string)$_SERVER['HTTP_X_CSRF_TOKEN'];
        }

        return null;
    }
}
