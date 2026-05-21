<?php
declare(strict_types=1);

class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public static function role(): ?string
    {
        return isset($_SESSION['role']) ? (string)$_SESSION['role'] : null;
    }

    public static function isCustomer(): bool
    {
        return self::check() && self::role() === 'customer';
    }
}
