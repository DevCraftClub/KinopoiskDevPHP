# Класс Kinopoisk

## Описание

`Kinopoisk` - главный класс для работы с API Kinopoisk.dev. Предоставляет базовую функциональность для выполнения HTTP запросов к API, обработки ответов, кэширования и управления ошибками. Использует современные PHP 8.3 возможности и архитектурные паттерны.

## Пространство имен

```php
namespace KinopoiskDev;
```

## Объявление класса

```php
final readonly class Kinopoisk extends Helper
```

## Константы

| Константа | Тип | Значение | Описание |
|-----------|-----|----------|----------|
| `BASE_URL` | `string` | `'https://api.kinopoisk.dev'` | Базовый URL API |
| `API_VERSION` | `string` | `'v1.4'` | Версия API по умолчанию |
| `APP_VERSION` | `string` | `'2.0.0'` | Версия клиента |
| `DEFAULT_TIMEOUT` | `int` | `30` | Таймаут запросов в секундах |
| `CACHE_TTL` | `int` | `3600` | Время жизни кэша (1 час) |

## Свойства

| Свойство | Тип | Модификаторы | Описание |
|----------|-----|--------------|----------|
| `$httpClient` | `HttpClientInterface` | `private readonly` | HTTP клиент для выполнения запросов |
| `$apiToken` | `string` | `private readonly` | Токен авторизации API (чувствительные данные) |
| `$cache` | `CacheInterface` | `private readonly` | Сервис кэширования |
| `$validator` | `ValidationService` | `private readonly` | Сервис валидации |
| `$logger` | `?LoggerInterface` | `private readonly` | Логгер (опциональный) |
| `$useCache` | `bool` | `private readonly` | Флаг использования кэширования |

## Методы

### __construct()

Конструктор клиента API Kinopoisk.

```php
public function __construct(
    ?string $apiToken = null,
    ?HttpClientInterface $httpClient = null,
    ?CacheInterface $cache = null,
    ?LoggerInterface $logger = null,
    private readonly bool $useCache = false,
)
```

**Параметры:**
- `$apiToken` - Токен авторизации API (если не указан, берется из $_ENV['KINOPOISK_TOKEN'])
- `$httpClient` - HTTP клиент (если не указан, создается по умолчанию)
- `$cache` - Сервис кэширования (если не указан, используется FilesystemAdapter)
- `$logger` - Логгер для записи событий
- `$useCache` - Флаг включения кэширования

**Исключения:**
- `ValidationException` - При отсутствии или неверном формате токена
- `KinopoiskDevException` - При ошибке инициализации

### makeRequest()

Выполняет HTTP запрос к API с поддержкой кэширования.

```php
public function makeRequest(
    string $method,
    string $endpoint,
    array $queryParams = [],
    ?string $apiVersion = null,
): ResponseInterface
```

**Параметры:**
- `$method` - HTTP метод (GET, POST, PUT, DELETE, PATCH)
- `$endpoint` - Конечная точка API
- `$queryParams` - Параметры запроса
- `$apiVersion` - Версия API (по умолчанию v1.4)

**Возвращает:** `ResponseInterface` - Ответ от API

**Исключения:**
- `KinopoiskDevException` - При ошибках запроса
- `ValidationException` - При неверном методе или endpoint

### parseResponse()

Обрабатывает ответ от API с валидацией.

```php
public function parseResponse(ResponseInterface $response): array
```

**Параметры:**
- `$response` - HTTP ответ

**Возвращает:** `array` - Декодированные данные

**Исключения:**
- `KinopoiskDevException` - При ошибках обработки
- `KinopoiskResponseException` - При ошибках API (401, 403, 404)

## Приватные методы

### validateAndSetApiToken()

Валидирует и устанавливает API токен.

```php
private function validateAndSetApiToken(?string $apiToken): void
```

### createDefaultHttpClient()

Создает HTTP клиент по умолчанию.

```php
private function createDefaultHttpClient(): HttpClientInterface
```

### executeHttpRequest()

Выполняет HTTP запрос.

```php
private function executeHttpRequest(
    string $method,
    string $endpoint,
    array $queryParams,
    string $version,
): ResponseInterface
```

### generateCacheKey()

Генерирует ключ для кэширования.

```php
private function generateCacheKey(
    string $method,
    string $endpoint,
    array $queryParams,
    string $version,
): string
```

### handleErrorStatusCode()

Обрабатывает ошибочные статус коды.

```php
private function handleErrorStatusCode(
    ?HttpStatusCode $statusCode,
    ?int $rawStatusCode = null,
): void
```

### validateHttpMethod()

Валидирует HTTP метод.

```php
private function validateHttpMethod(string $method): void
```

### validateEndpoint()

Валидирует конечную точку API.

```php
private function validateEndpoint(string $endpoint): void
```

### isValidApiToken()

Проверяет валидность API токена.

```php
private function isValidApiToken(string $token): bool
```

## Примеры использования

### Базовая инициализация

```php
use KinopoiskDev\Kinopoisk;

// С токеном напрямую
$kinopoisk = new Kinopoisk('ABC1DEF-2GH3IJK-4LM5NOP-6QR7STU');

// С токеном из переменной окружения
$kinopoisk = new Kinopoisk();

// С кэшированием
$kinopoisk = new Kinopoisk(
    apiToken: 'ABC1DEF-2GH3IJK-4LM5NOP-6QR7STU',
    useCache: true
);
```

### Выполнение запроса

```php
// GET запрос
$response = $kinopoisk->makeRequest('GET', 'movie', [
    'page' => 1,
    'limit' => 10
]);

// Обработка ответа
$data = $kinopoisk->parseResponse($response);
```

### С пользовательским HTTP клиентом и логгером

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('kinopoisk');
$logger->pushHandler(new StreamHandler('kinopoisk.log'));

$kinopoisk = new Kinopoisk(
    apiToken: 'YOUR_TOKEN',
    httpClient: new CustomHttpClient(),
    logger: $logger,
    useCache: true
);
```

## Обработка ошибок

```php
try {
    $response = $kinopoisk->makeRequest('GET', 'movie/123');
    $data = $kinopoisk->parseResponse($response);
} catch (ValidationException $e) {
    // Ошибка валидации параметров
    echo "Ошибка валидации: " . $e->getMessage();
} catch (KinopoiskResponseException $e) {
    // Ошибка от API (401, 403, 404)
    echo "Ошибка API: " . $e->getErrorClass();
} catch (KinopoiskDevException $e) {
    // Общая ошибка
    echo "Ошибка: " . $e->getMessage();
}
```

## Особенности реализации

1. **Readonly класс** - Все свойства класса неизменяемы после инициализации
2. **Автоматическое кэширование** - GET запросы кэшируются автоматически при включенной опции
3. **Валидация токена** - Проверяется формат токена (4 блока по 7 символов)
4. **Логирование** - Поддержка PSR-3 совместимых логгеров
5. **Обработка ошибок** - Автоматическое преобразование HTTP ошибок в исключения

## Связанные классы

- [HttpClientInterface](contracts/HttpClientInterface.md) - Интерфейс HTTP клиента
- [CacheInterface](contracts/CacheInterface.md) - Интерфейс кэширования
- [LoggerInterface](contracts/LoggerInterface.md) - Интерфейс логгера
- [ValidationService](services/ValidationService.md) - Сервис валидации
- [HttpStatusCode](enums/HttpStatusCode.md) - Перечисление HTTP статус кодов
- [KinopoiskDevException](exceptions/KinopoiskDevException.md) - Базовое исключение
- [KinopoiskResponseException](exceptions/KinopoiskResponseException.md) - Исключение ответа API
- [ValidationException](exceptions/ValidationException.md) - Исключение валидации

## Требования

- PHP 8.3+
- Guzzle HTTP Client
- PSR-3 Logger (опционально)
- PSR-6/PSR-16 Cache (опционально)