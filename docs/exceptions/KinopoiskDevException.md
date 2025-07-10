# Класс KinopoiskDevException

## Описание

`KinopoiskDevException` - базовый класс исключений для библиотеки Kinopoisk.dev. Является родительским классом для всех специфических исключений библиотеки.

## Пространство имен

```php
namespace KinopoiskDev\Exceptions;
```

## Объявление класса

```php
class KinopoiskDevException extends Exception
```

## Методы

### __construct()

Конструктор исключения.

```php
public function __construct(
    string $message = '',
    int $code = 0,
    ?Exception $previous = NULL,
)
```

**Параметры:**
- `$message` - Сообщение об ошибке
- `$code` - Код ошибки
- `$previous` - Предыдущее исключение для цепочки

## Примеры использования

### Выбрасывание базового исключения

```php
use KinopoiskDev\Exceptions\KinopoiskDevException;

class ApiClient
{
    public function fetchData(string $endpoint): array
    {
        if (!$this->isValidEndpoint($endpoint)) {
            throw new KinopoiskDevException(
                "Неверный endpoint: {$endpoint}",
                400
            );
        }
        
        try {
            return $this->makeRequest($endpoint);
        } catch (\Exception $e) {
            throw new KinopoiskDevException(
                "Ошибка при выполнении запроса",
                500,
                $e
            );
        }
    }
}
```

### Создание специализированных исключений

```php
use KinopoiskDev\Exceptions\KinopoiskDevException;

class NetworkException extends KinopoiskDevException
{
    public function __construct(string $url, \Exception $previous = null)
    {
        parent::__construct(
            "Сетевая ошибка при обращении к: {$url}",
            0,
            $previous
        );
    }
}

class TimeoutException extends KinopoiskDevException
{
    public function __construct(int $timeout, \Exception $previous = null)
    {
        parent::__construct(
            "Превышено время ожидания: {$timeout} секунд",
            408,
            $previous
        );
    }
}
```

### Обработка исключений

```php
use KinopoiskDev\Exceptions\KinopoiskDevException;

try {
    $client = new ApiClient();
    $data = $client->fetchData('/movies/123');
} catch (KinopoiskDevException $e) {
    // Обработка всех исключений библиотеки
    error_log("Ошибка Kinopoisk API: " . $e->getMessage());
    
    if ($e->getPrevious()) {
        error_log("Причина: " . $e->getPrevious()->getMessage());
    }
    
    // Возврат ошибки пользователю
    return [
        'error' => true,
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ];
} catch (\Exception $e) {
    // Обработка прочих исключений
    error_log("Неожиданная ошибка: " . $e->getMessage());
}
```

### Использование в сервисах

```php
use KinopoiskDev\Exceptions\KinopoiskDevException;

class MovieService
{
    private ApiClient $client;
    
    public function getMovie(int $id): ?Movie
    {
        if ($id <= 0) {
            throw new KinopoiskDevException(
                "ID фильма должен быть положительным числом",
                400
            );
        }
        
        try {
            $data = $this->client->get("/movie/{$id}");
            return new Movie($data);
        } catch (\JsonException $e) {
            throw new KinopoiskDevException(
                "Ошибка парсинга ответа API",
                500,
                $e
            );
        }
    }
}
```

## Особенности

1. **Наследование от Exception** - Стандартное PHP исключение
2. **Цепочка исключений** - Поддержка передачи предыдущего исключения
3. **Гибкость** - Базовый класс для создания специализированных исключений
4. **Простота** - Минимальная реализация без дополнительной логики

## Связанные классы

- [KinopoiskResponseException](KinopoiskResponseException.md) - Исключение для ошибок ответа API
- [ValidationException](ValidationException.md) - Исключение для ошибок валидации
- [Kinopoisk](../Kinopoisk.md) - Основной класс, выбрасывающий исключения
- `Exception` - Базовый класс PHP

## Требования

- PHP 8.3+