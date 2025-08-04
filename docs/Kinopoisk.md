# Kinopoisk

**Описание:** Главный класс для работы с API Kinopoisk.dev
Предоставляет базовую функциональность для выполнения HTTP запросов к API,
обработки ответов, кэширования и управления ошибками. Использует современные
PHP 8.3 возможности и архитектурные паттерны.
Основные возможности:
- Выполнение HTTP запросов к API Kinopoisk.dev
- Автоматическое кэширование ответов
- Валидация входных данных
- Обработка ошибок API
- Логирование операций

**С версии:** 1.0.0

**Версия:** 1.0.0

**См. также:**

* `\KinopoiskDev\Http\MovieRequests`: Для работы с фильмами
* `\KinopoiskDev\Http\PersonRequests`: Для работы с персонами
* `\KinopoiskDev\Http\StudioRequests`: Для работы со студиями
* `\KinopoiskDev\Contracts\CacheInterface`: Интерфейс кэширования
* `\KinopoiskDev\Contracts\LoggerInterface`: Интерфейс логирования

## `__construct()`

**Описание:** Конструктор клиента API Kinopoisk
Инициализирует клиент API с указанными параметрами. Если параметры не переданы,
используются значения по умолчанию. API токен может быть передан напрямую
или получен из переменной окружения KINOPOISK_TOKEN.

**С версии:** 1.0.0

**Параметры:**

* `$apiToken` (string|null): Токен авторизации API (если null, берется из $_ENV['KINOPOISK_TOKEN'])
* `$httpClient` (HttpClient|null): HTTP клиент (если null, создается новый)
* `$cache` (CacheInterface|null): Сервис кэширования (если null, создается FilesystemAdapter)
* `$logger` (LoggerInterface|null): Логгер (если null, логирование отключено)
* `$useCache` (bool): Использовать кэширование (по умолчанию false)
* `$responseValidator` (ValidationService|null): Валидатор ответов API (если null, валидация отключена)

**Исключения:**

* `ValidationException`: При отсутствии токена или неверном формате
* `KinopoiskDevException`: При ошибке инициализации компонентов

**Пример:**
```php
// Минимальная инициализация
$kinopoisk = new Kinopoisk();
// С кастомными параметрами
$kinopoisk = new Kinopoisk(
apiToken: 'ABC1DEF-2GH3IJK-4LM5NOP-6QR7STU',
useCache: true
);
// С кастомным HTTP клиентом и логгером
$httpClient = new HttpClient(['timeout' => 60]);
$logger = new CustomLogger();
$kinopoisk = new Kinopoisk('your-api-token', $httpClient, null, $logger);
```

## `validateAndSetApiToken()`

**Описание:** Валидирует и устанавливает API токен
Проверяет наличие и формат API токена. Если токен не передан,
пытается получить его из переменной окружения KINOPOISK_TOKEN.
Валидирует формат токена Kinopoisk.dev.

**С версии:** 1.0.0

**Параметры:**

* `$apiToken` (string|null): Токен API для валидации

**Исключения:**

* `ValidationException`: При отсутствии токена или неверном формате

## `createDefaultHttpClient()`

**Описание:** Создает HTTP клиент по умолчанию
Создает экземпляр GuzzleHttp\Client с базовыми настройками
для работы с API Kinopoisk.dev.

**С версии:** 1.0.0

**Возвращает:** `HttpClient` Экземпляр HTTP клиента с настроенными параметрами

## `makeRequestWithValidation()`

**Описание:** Выполняет HTTP запрос к API с валидацией ответа
Расширенная версия makeRequest, которая также выполняет валидацию
ответа с эталонными данными, если включена валидация.

**Параметры:**

* `$method` (string): HTTP метод
* `$endpoint` (string): Конечная точка API
* `$apiVersion` (string|null): Версия API

**Возвращает:** `array<string, mixed>` Декодированные данные ответа

**Исключения:**

* `KinopoiskDevException`: При ошибках запроса или валидации

## `buildFullUrl()`

**Описание:** Строит полный URL для запроса

**Параметры:**

* `$method` (string): HTTP метод
* `$endpoint` (string): Конечная точка
* `$version` (string): Версия API

**Возвращает:** `string` Полный URL

## `makeRequest()`

**Описание:** Выполняет HTTP запрос к API с поддержкой кэширования
Основной метод для выполнения запросов к API Kinopoisk.dev. Поддерживает
автоматическое кэширование GET запросов и обработку различных HTTP методов.
Валидирует входные параметры перед выполнением запроса.

**С версии:** 1.0.0

**Параметры:**

* `$method` (string): HTTP метод (GET, POST, PUT, DELETE, PATCH)
* `$endpoint` (string): Конечная точка API (без версии)
* `$apiVersion` (string|null): Версия API (если null, используется API_VERSION)

**Возвращает:** `ResponseInterface` Ответ от API

**Исключения:**

* `KinopoiskDevException`: При ошибках валидации или HTTP запроса
* `ValidationException`: При неверных параметрах запроса

**Пример:**
```php
// Простой GET запрос
$response = $kinopoisk->makeRequest('GET', 'movie/123');
// GET запрос с параметрами
$response = $kinopoisk->makeRequest('GET', 'movie', [
'page' => 1,
'limit' => 10
]);
// Запрос к другой версии API
$response = $kinopoisk->makeRequest('GET', 'movie/123', [], 'v1.3');
```

## `validateHttpMethod()`

**Описание:** Валидирует HTTP метод
Проверяет, что переданный HTTP метод поддерживается API.

**С версии:** 1.0.0

**Параметры:**

* `$method` (string): HTTP метод для валидации

**Исключения:**

* `ValidationException`: При неверном или неподдерживаемом методе

## `validateEndpoint()`

**Описание:** Валидирует конечную точку API
Проверяет формат и валидность конечной точки API.
Допускает только буквы, цифры, слеши, подчеркивания и дефисы.

**С версии:** 1.0.0

**Параметры:**

* `$endpoint` (string): Конечная точка для валидации

**Исключения:**

* `ValidationException`: При неверном формате конечной точки

## `generateCacheKey()`

**Описание:** Генерирует ключ для кэширования
Создает уникальный ключ кэша на основе параметров запроса.
Использует SHA256 хэш для обеспечения уникальности и безопасности.

**С версии:** 1.0.0

**Параметры:**

* `$method` (string): HTTP метод
* `$endpoint` (string): Конечная точка
* `$version` (string): Версия API

**Возвращает:** `string` Уникальный ключ кэша

## `executeHttpRequest()`

**Описание:** Выполняет HTTP запрос
Формирует полный URL запроса и выполняет его через HTTP клиент.
Добавляет API токен в заголовки запроса.

**С версии:** 1.0.0

**Параметры:**

* `$method` (string): HTTP метод
* `$endpoint` (string): Конечная точка
* `$version` (string): Версия API

**Возвращает:** `ResponseInterface` Ответ от сервера

**Исключения:**

* `GuzzleException`: При ошибке выполнения HTTP запроса

## `parseResponse()`

**Описание:** Обрабатывает ответ от API с валидацией
Парсит HTTP ответ от API, проверяет статус код и декодирует JSON.
Обрабатывает различные типы ошибок API и логирует результаты.
Если включена валидация ответов, сравнивает с эталонными данными.

**С версии:** 1.0.0

**Параметры:**

* `$response` (ResponseInterface): HTTP ответ от API
* `$requestUrl` (string|null): Полный URL запроса для валидации (опционально)

**Возвращает:** `array<string, mixed>` Декодированные данные ответа

**Исключения:**

* `KinopoiskDevException`: При ошибках обработки ответа
* `KinopoiskResponseException`: При ошибках API (401, 403, 404, 500)
* `\JsonException`: При ошибках парсинга JSON

**Пример:**
```php
$response = $kinopoisk->makeRequest('GET', 'movie/123');
$data = $kinopoisk->parseResponse($response);
$movie = Movie::fromArray($data);
```

## `handleErrorStatusCode()`

**Описание:** Обрабатывает ошибочные статус коды
Проверяет статус код ответа и выбрасывает соответствующие исключения
для известных ошибок API (401, 403, 404).

**С версии:** 1.0.0

**Параметры:**

* `$statusCode` (HttpStatusCode|null): Статус код как enum
* `$rawStatusCode` (int|null): Сырой статус код

**Исключения:**

* `KinopoiskResponseException`: При известных ошибках API

## `validateResponse()`

**Описание:** Валидирует ответ API с эталонными данными

**Параметры:**

* `$requestUrl` (string): URL запроса

