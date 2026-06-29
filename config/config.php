<?php
declare(strict_types=1);

const APP_NAME = 'Startseite Demo';
const APP_BASE_PATH = __DIR__ . '/..';

function envValue(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function envBool(string $key, bool $default = false): bool
{
    $value = getenv($key);

    if ($value === false || $value === '') {
        return $default;
    }

    return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
}

define('APP_ALLOW_REGISTRATION', envBool('APP_ALLOW_REGISTRATION', true));
define('APP_ALLOW_DEBUG_IMPERSONATION', envBool('APP_ALLOW_DEBUG_IMPERSONATION', false));
define('APP_DEFAULT_OWNER_EMAIL', envValue('APP_DEFAULT_OWNER_EMAIL', 'demo@example.local'));
define('APP_PUBLIC_BASE_URL', rtrim(envValue('APP_PUBLIC_BASE_URL', 'http://localhost:28860'), '/'));

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $pdo = new PDO(
        sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            envValue('DB_HOST', 'db'),
            envValue('DB_NAME', 'startseite')
        ),
        envValue('DB_USER', 'startseite'),
        envValue('DB_PASS', ''),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    return $pdo;
}
