<?php
declare(strict_types=1);

// Autoload simple: funciona con o sin `composer install`.
spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
