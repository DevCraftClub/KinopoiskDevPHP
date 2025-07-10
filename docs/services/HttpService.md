# Класс HttpService

## Описание

`HttpService` - сервис для HTTP запросов, адаптер для Guzzle HTTP клиента, реализующий интерфейс `HttpClientInterface`. Обеспечивает единообразный способ выполнения HTTP запросов.

## Пространство имен

```php
namespace KinopoiskDev\Services;
```

## Объявление класса

```php
final readonly class HttpService implements HttpClientInterface
```

## Интерфейсы

- [HttpClientInterface](../contracts/HttpClientInterface.md) - Контракт для HTTP клиента

## Методы

### __construct()

Конструктор сервиса.

```php
public function __construct(
    private ClientInterface $client,
)
```

**Параметры:**
- `$client` - Guzzle HTTP клиент (PSR-18 совместимый)

### send()

Отправляет HTTP запрос.

```php
public function send(RequestInterface $request): ResponseInterface
```

**Параметры:**
- `$request` - HTTP запрос (PSR-7)

**Возвращает:** `ResponseInterface` - HTTP ответ (PSR-7)

**Исключения:**
- `\GuzzleHttp\Exception\GuzzleException` - При ошибке запроса

### get()

Выполняет GET запрос.

```php
public function get(string $uri, array $options = []): ResponseInterface
```

**Параметры:**
- `$uri` - URI для запроса
- `$options` - Опции запроса

**Возвращает:** `ResponseInterface` - HTTP ответ

### post()

Выполняет POST запрос.

```php
public function post(string $uri, array $options = []): ResponseInterface
```

**Параметры:**
- `$uri` - URI для запроса
- `$options` - Опции запроса

**Возвращает:** `ResponseInterface` - HTTP ответ

### put()

Выполняет PUT запрос.

```php
public function put(string $uri, array $options = []): ResponseInterface
```

**Параметры:**
- `$uri` - URI для запроса
- `$options` - Опции запроса

**Возвращает:** `ResponseInterface` - HTTP ответ

### delete()

Выполняет DELETE запрос.

```php
public function delete(string $uri, array $options = []): ResponseInterface
```

**Параметры:**
- `$uri` - URI для запроса
- `$options` - Опции запроса

**Возвращает:** `ResponseInterface` - HTTP ответ

## Примеры использования

### Базовое использование

```php
use KinopoiskDev\Services\HttpService;
use GuzzleHttp\Client;

// Создание сервиса
$guzzleClient = new Client([
    'base_uri' => 'https://api.example.com',
    'timeout' => 30,
]);

$httpService = new HttpService($guzzleClient);

// GET запрос
$response = $httpService->get('/users/123');
$data = json_decode($response->getBody()->getContents(), true);

// POST запрос
$response = $httpService->post('/users', [
    'json' => [
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]
]);
```

### С настройками Guzzle

```php
use KinopoiskDev\Services\HttpService;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

// Создание стека обработчиков
$stack = HandlerStack::create();

// Добавление middleware для логирования
$stack->push(Middleware::log(
    $logger,
    new \GuzzleHttp\MessageFormatter('{method} {uri} - {code}')
));

// Создание клиента с настройками
$client = new Client([
    'handler' => $stack,
    'base_uri' => 'https://api.kinopoisk.dev',
    'headers' => [
        'User-Agent' => 'MyApp/1.0',
        'Accept' => 'application/json',
    ],
    'timeout' => 30,
    'connect_timeout' => 5,
]);

$httpService = new HttpService($client);
```

### Использование с PSR-7 запросами

```php
use KinopoiskDev\Services\HttpService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

$httpService = new HttpService(new Client());

// Создание PSR-7 запроса
$request = new Request('GET', 'https://api.example.com/data', [
    'Authorization' => 'Bearer token123',
    'Accept' => 'application/json',
]);

// Отправка запроса
$response = $httpService->send($request);
```

### Обработка ошибок

```php
use KinopoiskDev\Services\HttpService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$httpService = new HttpService(new Client());

try {
    $response = $httpService->get('https://api.example.com/data');
    $data = json_decode($response->getBody()->getContents(), true);
} catch (GuzzleException $e) {
    // Обработка ошибок Guzzle
    if ($e instanceof \GuzzleHttp\Exception\ClientException) {
        // 4xx ошибки
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $error = $response->getBody()->getContents();
    } elseif ($e instanceof \GuzzleHttp\Exception\ServerException) {
        // 5xx ошибки
        error_log('Server error: ' . $e->getMessage());
    } elseif ($e instanceof \GuzzleHttp\Exception\ConnectException) {
        // Ошибки соединения
        error_log('Connection error: ' . $e->getMessage());
    }
}
```

### С retry политикой

```php
use KinopoiskDev\Services\HttpService;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

$stack = HandlerStack::create();

// Добавление retry middleware
$stack->push(Middleware::retry(
    function ($retries, $request, $response, $exception) {
        // Повторить если timeout или 5xx ошибка
        if ($exception instanceof \GuzzleHttp\Exception\ConnectException) {
            return true;
        }
        
        if ($response && $response->getStatusCode() >= 500) {
            return true;
        }
        
        return false;
    },
    function ($retries) {
        // Задержка между попытками (экспоненциальная)
        return 1000 * pow(2, $retries);
    }
));

$client = new Client(['handler' => $stack]);
$httpService = new HttpService($client);
```

### Для тестирования

```php
use KinopoiskDev\Services\HttpService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

// Создание mock handler
$mock = new MockHandler([
    new Response(200, ['Content-Type' => 'application/json'], '{"id": 1}'),
    new Response(404, [], 'Not found'),
    new Response(500, [], 'Server error'),
]);

$handlerStack = HandlerStack::create($mock);
$client = new Client(['handler' => $handlerStack]);

$httpService = new HttpService($client);

// Первый запрос вернет 200
$response1 = $httpService->get('/test');

// Второй запрос вернет 404
$response2 = $httpService->get('/test');

// Третий запрос вернет 500
$response3 = $httpService->get('/test');
```

## Особенности

1. **Final readonly класс** - Не может быть расширен, свойства неизменяемы
2. **Адаптер паттерн** - Адаптирует Guzzle клиент к собственному интерфейсу
3. **PSR совместимость** - Работает с PSR-7 запросами и ответами
4. **Простота** - Минимальная обертка над Guzzle

## Связанные классы

- [HttpClientInterface](../contracts/HttpClientInterface.md) - Интерфейс, который реализует сервис
- [Kinopoisk](../Kinopoisk.md) - Использует сервис для HTTP запросов
- `GuzzleHttp\ClientInterface` - Интерфейс Guzzle клиента
- `Psr\Http\Message\RequestInterface` - PSR-7 интерфейс запроса
- `Psr\Http\Message\ResponseInterface` - PSR-7 интерфейс ответа

## Требования

- PHP 8.3+
- Guzzle HTTP Client 7.0+
- PSR-7 HTTP Message interfaces