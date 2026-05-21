<?php
declare(strict_types=1);

class Database
{
    private static ?PDO $pdo = null;
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        $config = [
            'host' => defined('DB_HOST') ? DB_HOST : 'localhost',
            'name' => defined('DB_NAME') ? DB_NAME : 'medicine_shop',
            'user' => defined('DB_USER') ? DB_USER : 'root',
            'pass' => defined('DB_PASS') ? DB_PASS : '',
            'charset' => 'utf8mb4',
        ];

        return self::connection($config);
    }

    public static function connection(array $config): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['name'],
            $config['charset']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        self::$pdo = new PDO($dsn, $config['user'], $config['pass'], $options);

        return self::$pdo;
    }
}
