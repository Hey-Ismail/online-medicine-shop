<?php
declare(strict_types=1);

$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if ($baseUrl === '' || $baseUrl === '/') {
    $baseUrl = '';
}

$rootBasePath = preg_replace('#/public/shop$#', '', $baseUrl);
if ($rootBasePath === null || $rootBasePath === '/') {
    $rootBasePath = '';
}

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(__DIR__));
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', $baseUrl);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', $baseUrl);
}

if (!defined('ROOT_BASE_PATH')) {
    define('ROOT_BASE_PATH', $rootBasePath);
}

if (!defined('REMEMBER_COOKIE_NAME')) {
    define('REMEMBER_COOKIE_NAME', 'pharmacare_remember_token');
}

if (!defined('REMEMBER_COOKIE_LIFETIME')) {
    define('REMEMBER_COOKIE_LIFETIME', 60 * 60 * 24 * 30);
}

if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', 5 * 1024 * 1024);
}

if (!defined('ALLOWED_IMAGE_TYPES')) {
    define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
}

if (!defined('PROFILE_UPLOAD_DIR')) {
    define('PROFILE_UPLOAD_DIR', ROOT_DIR . '/public/uploads/profiles/');
}

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('require_login')) {
    function require_login(): void
    {
        if (!is_logged_in()) {
            redirect('login');
        }
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path = ''): void
    {
        $path = trim($path, '/');
        $location = BASE_PATH . ($path !== '' ? '/' . $path : '/');
        header('Location: ' . $location);
        exit;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('csrf_verify')) {
    function csrf_verify(): bool
    {
        $token = $_POST['_csrf'] ?? $_POST['csrf_token'] ?? '';
        return is_string($token) && hash_equals($_SESSION['_csrf_token'] ?? '', $token);
    }
}

if (!function_exists('set_flash')) {
    function set_flash(string $type, string $msg): void
    {
        $_SESSION['_flash'] = ['type' => $type, 'msg' => $msg];
    }
}

if (!function_exists('get_flash')) {
    function get_flash(): ?array
    {
        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
        return is_array($flash) ? $flash : null;
    }
}

if (!function_exists('set_old')) {
    function set_old(array $values): void
    {
        $_SESSION['_old'] = $values;
    }
}

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        return e($_SESSION['_old'][$key] ?? $default);
    }
}

if (!function_exists('clear_old')) {
    function clear_old(): void
    {
        unset($_SESSION['_old']);
    }
}

return [
    'base_url' => $baseUrl,
    'db' => [
        'host' => 'localhost',
        'name' => 'medicine_shop',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
];
