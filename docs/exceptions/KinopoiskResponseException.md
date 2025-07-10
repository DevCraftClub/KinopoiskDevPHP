# Класс KinopoiskResponseException

## Описание

`KinopoiskResponseException` - специализированное исключение для обработки ошибок ответов API. Автоматически извлекает информацию об ошибке из класса ответа.

## Пространство имен

```php
namespace KinopoiskDev\Exceptions;
```

## Объявление класса

```php
class KinopoiskResponseException extends Exception
```

## Методы

### __construct()

Конструктор исключения с поддержкой классов ответов.

```php
public function __construct(
    string $rspnsCls = '',
    ?Exception $previous = NULL,
)
```

**Параметры:**
- `$rspnsCls` - Полное имя класса ответа с информацией об ошибке
- `$previous` - Предыдущее исключение для цепочки

**Особенности:**
- Если передан класс ответа, создается его экземпляр
- Из экземпляра извлекаются поля `error`, `message` и `statusCode`
- Сообщение формируется в формате: `{error}: {message}`

## Примеры использования

### Использование с классом ответа

```php
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use KinopoiskDev\Responses\Errors\NotFoundErrorResponseDto;

// Класс ответа об ошибке
class NotFoundErrorResponseDto
{
    public string $error = 'NOT_FOUND';
    public string $message = 'Запрашиваемый ресурс не найден';
    public int $statusCode = 404;
}

// Выбрасывание исключения
throw new KinopoiskResponseException(NotFoundErrorResponseDto::class);
// Результат: Exception с сообщением "NOT_FOUND: Запрашиваемый ресурс не найден" и кодом 404
```

### Обработка различных типов ошибок

```php
use KinopoiskDev\Exceptions\KinopoiskResponseException;

class ApiHandler
{
    public function handleResponse(int $statusCode): void
    {
        $errorClass = match ($statusCode) {
            401 => UnauthorizedErrorResponseDto::class,
            403 => ForbiddenErrorResponseDto::class,
            404 => NotFoundErrorResponseDto::class,
            default => null
        };
        
        if ($errorClass !== null) {
            throw new KinopoiskResponseException($errorClass);
        }
    }
}
```

### Создание классов ошибок

```php
// Базовый класс ошибки
abstract class ErrorResponseDto
{
    public string $error;
    public string $message;
    public int $statusCode;
}

// Ошибка авторизации
class UnauthorizedErrorResponseDto extends ErrorResponseDto
{
    public string $error = 'UNAUTHORIZED';
    public string $message = 'Необходима авторизация';
    public int $statusCode = 401;
}

// Ошибка доступа
class ForbiddenErrorResponseDto extends ErrorResponseDto
{
    public string $error = 'FORBIDDEN';
    public string $message = 'Доступ запрещен';
    public int $statusCode = 403;
}

// Ошибка лимита
class RateLimitErrorResponseDto extends ErrorResponseDto
{
    public string $error = 'RATE_LIMIT_EXCEEDED';
    public string $message = 'Превышен лимит запросов';
    public int $statusCode = 429;
}
```

### Использование в HTTP клиенте

```php
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    private array $errorMappings = [
        401 => UnauthorizedErrorResponseDto::class,
        403 => ForbiddenErrorResponseDto::class,
        404 => NotFoundErrorResponseDto::class,
        429 => RateLimitErrorResponseDto::class,
    ];
    
    public function handleResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        
        if ($statusCode >= 400) {
            $errorClass = $this->errorMappings[$statusCode] ?? null;
            
            if ($errorClass) {
                throw new KinopoiskResponseException($errorClass);
            }
            
            throw new KinopoiskResponseException();
        }
        
        return json_decode($response->getBody()->getContents(), true);
    }
}
```

### Обработка исключения

```php
use KinopoiskDev\Exceptions\KinopoiskResponseException;

try {
    $data = $apiClient->getMovie(123);
} catch (KinopoiskResponseException $e) {
    // Получаем детали ошибки
    $errorMessage = $e->getMessage(); // "NOT_FOUND: Фильм не найден"
    $errorCode = $e->getCode();       // 404
    
    // Логирование
    error_log("API Error: {$errorMessage} (Code: {$errorCode})");
    
    // Возврат ошибки клиенту
    return response()->json([
        'success' => false,
        'error' => [
            'message' => $errorMessage,
            'code' => $errorCode
        ]
    ], $errorCode);
}
```

### Расширение функциональности

```php
use KinopoiskDev\Exceptions\KinopoiskResponseException;

class ExtendedResponseException extends KinopoiskResponseException
{
    private ?string $errorClass = null;
    
    public function __construct(string $rspnsCls = '', ?Exception $previous = null)
    {
        $this->errorClass = $rspnsCls;
        parent::__construct($rspnsCls, $previous);
    }
    
    public function getErrorClass(): ?string
    {
        return $this->errorClass;
    }
    
    public function isRetryable(): bool
    {
        // Некоторые ошибки можно повторить
        return in_array($this->errorClass, [
            RateLimitErrorResponseDto::class,
            ServiceUnavailableErrorResponseDto::class,
        ]);
    }
    
    public function getRetryAfter(): ?int
    {
        if ($this->errorClass === RateLimitErrorResponseDto::class) {
            return 60; // Повторить через 60 секунд
        }
        return null;
    }
}
```

## Особенности

1. **Динамическое создание** - Создает экземпляр класса ошибки по имени
2. **Автоматическое извлечение** - Извлекает данные об ошибке из класса ответа
3. **Форматированное сообщение** - Формирует читаемое сообщение об ошибке
4. **Код состояния** - Использует HTTP код состояния как код исключения

## Связанные классы

- [KinopoiskDevException](KinopoiskDevException.md) - Базовый класс исключений
- [UnauthorizedErrorResponseDto](../responses/errors/UnauthorizedErrorResponseDto.md) - Ошибка 401
- [ForbiddenErrorResponseDto](../responses/errors/ForbiddenErrorResponseDto.md) - Ошибка 403
- [NotFoundErrorResponseDto](../responses/errors/NotFoundErrorResponseDto.md) - Ошибка 404
- [Kinopoisk](../Kinopoisk.md) - Использует для обработки ошибок API

## Требования

- PHP 8.3+
- Классы ответов должны иметь публичные свойства: `error`, `message`, `statusCode`