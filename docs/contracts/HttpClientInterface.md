# Интерфейс HttpClientInterface

## Описание

`HttpClientInterface` - интерфейс для HTTP клиента, определяющий контракт для выполнения HTTP запросов с поддержкой различных HTTP методов и конфигураций.

## Пространство имен

```php
namespace KinopoiskDev\Contracts;
```

## Объявление интерфейса

```php
interface HttpClientInterface
```

## Методы

### send()

Отправляет HTTP запрос.

```php
public function send(RequestInterface $request): ResponseInterface
```

**Параметры:**
- `$request` - HTTP запрос (PSR-7 RequestInterface)

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
- `$options` - Опции запроса (заголовки, параметры и т.д.)

**Возвращает:** `ResponseInterface` - HTTP ответ

**Исключения:**
- `\GuzzleHttp\Exception\GuzzleException` - При ошибке запроса

### post()

Выполняет POST запрос.

```php
public function post(string $uri, array $options = []): ResponseInterface
```

**Параметры:**
- `$uri` - URI для запроса
- `$options` - Опции запроса (включая тело запроса)

**Возвращает:** `ResponseInterface` - HTTP ответ

**Исключения:**
- `\GuzzleHttp\Exception\GuzzleException` - При ошибке запроса

### put()

Выполняет PUT запрос.

```php
public function put(string $uri, array $options = []): ResponseInterface
```

**Параметры:**
- `$uri` - URI для запроса
- `$options` - Опции запроса (включая тело запроса)

**Возвращает:** `ResponseInterface` - HTTP ответ

**Исключения:**
- `\GuzzleHttp\Exception\GuzzleException` - При ошибке запроса

### delete()

Выполняет DELETE запрос.

```php
public function delete(string $uri, array $options = []): ResponseInterface
```

**Параметры:**
- `$uri` - URI для запроса
- `$options` - Опции запроса

**Возвращает:** `ResponseInterface` - HTTP ответ

**Исключения:**
- `\GuzzleHttp\Exception\GuzzleException` - При ошибке запроса

## Примеры использования

### Реализация интерфейса

```php
use KinopoiskDev\Contracts\HttpClientInterface;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use GuzzleHttp\Client;

class MyHttpClient implements HttpClientInterface
{
    private Client $client;
    
    public function __construct()
    {
        $this->client = new Client();
    }
    
    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->client->send($request);
    }
    
    public function get(string $uri, array $options = []): ResponseInterface
    {
        return $this->client->get($uri, $options);
    }
    
    public function post(string $uri, array $options = []): ResponseInterface
    {
        return $this->client->post($uri, $options);
    }
    
    public function put(string $uri, array $options = []): ResponseInterface
    {
        return $this->client->put($uri, $options);
    }
    
    public function delete(string $uri, array $options = []): ResponseInterface
    {
        return $this->client->delete($uri, $options);
    }
}
```

### Использование в классе

```php
use KinopoiskDev\Contracts\HttpClientInterface;

class ApiService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {}
    
    public function fetchData(string $endpoint): array
    {
        $response = $this->httpClient->get('/api/' . $endpoint);
        return json_decode($response->getBody()->getContents(), true);
    }
}
```

### Создание мок-объекта для тестирования

```php
use PHPUnit\Framework\TestCase;
use KinopoiskDev\Contracts\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;

class ApiServiceTest extends TestCase
{
    public function testFetchData(): void
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getBody')->willReturn('{"data": "test"}');
        
        $mockClient = $this->createMock(HttpClientInterface::class);
        $mockClient->method('get')->willReturn($mockResponse);
        
        $service = new ApiService($mockClient);
        $result = $service->fetchData('test');
        
        $this->assertEquals(['data' => 'test'], $result);
    }
}
```

## Особенности

1. **PSR-7 совместимость** - Использует стандартные PSR-7 интерфейсы для запросов и ответов
2. **Гибкость** - Позволяет использовать любую реализацию HTTP клиента
3. **Тестируемость** - Легко мокировать для unit-тестов
4. **Расширяемость** - Можно добавлять дополнительную логику в реализации

## Связанные классы

- [HttpService](../services/HttpService.md) - Базовая реализация интерфейса
- [Kinopoisk](../Kinopoisk.md) - Использует интерфейс для HTTP запросов
- `Psr\Http\Message\RequestInterface` - PSR-7 интерфейс запроса
- `Psr\Http\Message\ResponseInterface` - PSR-7 интерфейс ответа

## Требования

- PHP 8.3+
- PSR-7 HTTP Message interfaces