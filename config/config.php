<?php
declare(strict_types=1);

const APP_NAME = 'Startseite';
const APP_BASE_PATH = __DIR__ . '/..';
const APP_ALLOW_REGISTRATION = true;
const APP_ALLOW_DEBUG_IMPERSONATION = true;
const APP_DEFAULT_OWNER_EMAIL = 'nikolay@stoykow.de';

function envValue(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

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
