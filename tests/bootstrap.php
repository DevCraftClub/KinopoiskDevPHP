<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables for testing
$envFile = __DIR__ . '/../.env.testing';
if (file_exists($envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..', '.env.testing');
    $dotenv->load();
    // Явно дублируем токен для совместимости
    if (isset($_ENV['KINOPOISK_API_TOKEN'])) {
        $_ENV['KINOPOISK_TOKEN'] = $_ENV['KINOPOISK_API_TOKEN'];
    }
} else {
    // Fallback to default test values if .env.testing doesn't exist
    $_ENV['KINOPOISK_API_TOKEN'] = $_ENV['KINOPOISK_API_TOKEN'] ?? 'TEST-TOKEN-1234-5678-9ABC-DEF0';
    $_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? 'testing';
    $_ENV['CACHE_DRIVER'] = $_ENV['CACHE_DRIVER'] ?? 'array';
    $_ENV['SESSION_DRIVER'] = $_ENV['SESSION_DRIVER'] ?? 'array';
    $_ENV['QUEUE_DRIVER'] = $_ENV['QUEUE_DRIVER'] ?? 'sync';
}

// Set default values for required environment variables
$_ENV['KINOPOISK_API_TOKEN'] = $_ENV['KINOPOISK_API_TOKEN'] ?? 'TEST-TOKEN-1234-5678-9ABC-DEF0';
$_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? 'testing';
$_ENV['CACHE_DRIVER'] = $_ENV['CACHE_DRIVER'] ?? 'array';
$_ENV['SESSION_DRIVER'] = $_ENV['SESSION_DRIVER'] ?? 'array';
$_ENV['QUEUE_DRIVER'] = $_ENV['QUEUE_DRIVER'] ?? 'sync'; 