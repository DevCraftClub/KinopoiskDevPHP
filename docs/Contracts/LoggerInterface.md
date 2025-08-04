# LoggerInterface

**Описание:** Интерфейс для логирования
Определяет контракт для ведения журнала событий
с поддержкой различных уровней логирования. Основан на
стандартах PSR-3 Logger Interface для обеспечения совместимости
с различными системами логирования.

**С версии:** 1.0.0

**Версия:** 1.0.0

**Пример:**
```php
class CustomLogger implements LoggerInterface {
public function debug(string $message, array $context = []): void {
echo "[DEBUG] {$message}\n";
}
public function info(string $message, array $context = []): void {
echo "[INFO] {$message}\n";
}
// ... остальные методы
}
$kinopoisk = new Kinopoisk(apiToken: 'token', logger: new CustomLogger());
```

**См. также:**

* `https:`: //www.php-fig.org/psr/psr-3/ PSR-3 Logger Interface

## `debug()`

**Описание:** Записывает сообщение уровня DEBUG
Используется для детальной отладочной информации,
которая полезна при разработке и диагностике проблем.
Эти сообщения обычно не записываются в продакшене.

**Параметры:**

* `$message` (string): Сообщение для записи в лог

**Возвращает:** `void` 

**Пример:**
```php
$logger->debug('HTTP request started', [
'method' => 'GET',
'url' => '/api/movie/123',
'user_id' => 456
]);
```

## `info()`

**Описание:** Записывает информационное сообщение
Используется для записи общей информации о работе приложения,
такой как инициализация, успешные операции, статистика.

**Параметры:**

* `$message` (string): Сообщение для записи в лог

**Возвращает:** `void` 

**Пример:**
```php
$logger->info('Movie retrieved successfully', [
'movie_id' => 123,
'response_time' => 0.15
]);
```

## `warning()`

**Описание:** Записывает предупреждение
Используется для записи предупреждений, которые не являются
критическими ошибками, но требуют внимания. Например,
устаревшие API вызовы, неоптимальные запросы.

**Параметры:**

* `$message` (string): Сообщение для записи в лог

**Возвращает:** `void` 

**Пример:**
```php
$logger->warning('API rate limit approaching', [
'current_requests' => 95,
'limit' => 100,
'reset_time' => '2024-01-01T00:00:00Z'
]);
```

## `error()`

**Описание:** Записывает сообщение об ошибке
Используется для записи ошибок, которые не позволяют
выполнить запрошенную операцию, но не приводят к
полной остановке приложения.

**Параметры:**

* `$message` (string): Сообщение для записи в лог

**Возвращает:** `void` 

**Пример:**
```php
$logger->error('Failed to retrieve movie', [
'movie_id' => 123,
'error_code' => 404,
'error_message' => 'Movie not found'
]);
```

## `critical()`

**Описание:** Записывает критическое сообщение
Используется для записи критических ошибок, которые
могут привести к нестабильной работе приложения
или требуют немедленного вмешательства.

**Параметры:**

* `$message` (string): Сообщение для записи в лог

**Возвращает:** `void` 

**Пример:**
```php
$logger->critical('Database connection failed', [
'host' => 'localhost',
'port' => 3306,
'error' => 'Connection refused'
]);
```

