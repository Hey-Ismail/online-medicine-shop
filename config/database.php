<?php
declare(strict_types=1);

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'medicine_shop');
}

require_once dirname(__DIR__) . '/core/Database.php';

$pdo = Database::getInstance()->getConnection();
