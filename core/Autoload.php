<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $base = dirname(__DIR__);
    $paths = [
        $base . '/core/' . $class . '.php',
        $base . '/app/controllers/shop/' . $class . '.php',
        $base . '/app/controllers/shop/Api/' . $class . '.php',
        $base . '/app/models/shop/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});
