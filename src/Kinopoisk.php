<?php

declare(strict_types=1);

namespace KinopoiskDev;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use KinopoiskDev\Attributes\Sensitive;
use KinopoiskDev\Contracts\{CacheInterface, LoggerInterface};
use KinopoiskDev\Enums\HttpStatusCode;
use KinopoiskDev\Exceptions\{KinopoiskDevException, KinopoiskResponseException, ValidationException};
use KinopoiskDev\Responses\Errors\{ForbiddenErrorResponseDto, NotFoundErrorResponseDto, UnauthorizedErrorResponseDto};
use KinopoiskDev\Services\{CacheService, ValidationService};
use Lombok\{Getter, Helper};
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Главный класс для работы с API Kinopoisk.dev
 *
 * Предоставляет базовую функциональность для выполнения HTTP запросов к API,
 * обработки ответов, кэширования и управления ошибками. Использует современные
 * PHP 8.3 возможности и архитектурные паттерны.
 *
 * Основные возможности:
 * - Выполнение HTTP запросов к API Kinopoisk.dev
 * - Автоматическое кэширование ответов
 * - Валидация входных данных
 * - Обработка ошибок API
 * - Логирование операций
 *
 * @package KinopoiskDev
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * 
 * @see \KinopoiskDev\Http\MovieRequests Для работы с фильмами
 * @see \KinopoiskDev\Http\PersonRequests Для работы с персонами
 * @see \KinopoiskDev\Http\StudioRequests Для работы со студиями
 * @see \KinopoiskDev\Contracts\CacheInterface Интерфейс кэширования
 * @see \KinopoiskDev\Contracts\LoggerInterface Интерфейс логирования
 */
class Kinopoisk extends Helper {

	/** @var string Базовый URL API Kinopoisk.dev */
	private const string BASE_URL = 'https://api.kinopoisk.dev';
	
	/** @var string Версия API по умолчанию */
	private const string API_VERSION = 'v1.4';
	
	/** @var string Версия клиента */
	private const string APP_VERSION = '1.0.0';
	
	/** @var int Таймаут HTTP запросов по умолчанию в секундах */
	private const int DEFAULT_TIMEOUT = 30;
	
	/** @var int Время жизни кэша по умолчанию в секундах */
	private const int CACHE_TTL = 3600; // 1 час

	/** @var HttpClient HTTP клиент для выполнения запросов */
	#[Getter]
	private HttpClient $httpClient;

	/** @var string API токен для авторизации */
	#[Getter, Sensitive]
	private string $apiToken;

	/** @var CacheInterface Сервис кэширования */
	#[Getter]
	private CacheInterface $cache;

	/** @var LoggerInterface|null Логгер для записи событий */
	#[Getter]
	private ?LoggerInterface $logger;

	/**
	 * Конструктор клиента API Kinopoisk
	 *
	 * Инициализирует клиент API с указанными параметрами. Если параметры не переданы,
	 * используются значения по умолчанию. API токен может быть передан напрямую
	 * или получен из переменной окружения KINOPOISK_TOKEN.
	 * 
	 * @since   1.0.0
	 *
	 * @param   string|null             $apiToken    Токен авторизации API (если null, берется из $_ENV['KINOPOISK_TOKEN'])
	 * @param   HttpClient|null         $httpClient  HTTP клиент (если null, создается новый)
	 * @param   CacheInterface|null     $cache       Сервис кэширования (если null, создается FilesystemAdapter)
	 * @param   LoggerInterface|null    $logger      Логгер (если null, логирование отключено)
	 * @param   bool                    $useCache    Использовать кэширование (по умолчанию false)
	 *
	 * @throws ValidationException При отсутствии токена или неверном формате
	 * @throws KinopoiskDevException При ошибке инициализации компонентов
	 * 
	 * @example
	 * ```php
	 * // Минимальная инициализация
	 * $kinopoisk = new Kinopoisk();
	 * 
	 * // С кастомными параметрами
	 * $kinopoisk = new Kinopoisk(
	 *     apiToken: 'ABC1DEF-2GH3IJK-4LM5NOP-6QR7STU',
	 *     useCache: true
	 * );
	 * 
	 * // С кастомным HTTP клиентом и логгером
	 * $httpClient = new HttpClient(['timeout' => 60]);
	 * $logger = new CustomLogger();
	 * $kinopoisk = new Kinopoisk('your-api-token', $httpClient, null, $logger);
	 * ```
	 */
	public function __construct(
		?string $apiToken = null,
		?HttpClient $httpClient = null,
		?CacheInterface $cache = null,
		?LoggerInterface $logger = null,
		private bool $useCache = false,
	) {
		parent::__construct();

		$this->validateAndSetApiToken($apiToken);
		$this->httpClient = $httpClient ?? $this->createDefaultHttpClient();
		$this->cache = $cache ?? new CacheService(new FilesystemAdapter());
		$this->logger = $logger;

		$this->logger?->info('Kinopoisk client initialized', [
			'version' => self::APP_VERSION,
			'useCache' => $this->useCache,
		]);
	}

	/**
	 * Выполняет HTTP запрос к API с поддержкой кэширования
	 *
	 * Основной метод для выполнения запросов к API Kinopoisk.dev. Поддерживает
	 * автоматическое кэширование GET запросов и обработку различных HTTP методов.
	 * Валидирует входные параметры перед выполнением запроса.
	 * 
	 * @since   1.0.0
	 *
	 * @param   string                   $method       HTTP метод (GET, POST, PUT, DELETE, PATCH)
	 * @param   string                   $endpoint     Конечная точка API (без версии)
	 * @param   array<string, mixed>     $queryParams  Параметры запроса для добавления в URL
	 * @param   string|null              $apiVersion   Версия API (если null, используется API_VERSION)
	 *
	 * @return ResponseInterface Ответ от API
	 * @throws KinopoiskDevException При ошибках валидации или HTTP запроса
	 * @throws ValidationException При неверных параметрах запроса
	 * 
	 * @example
	 * ```php
	 * // Простой GET запрос
	 * $response = $kinopoisk->makeRequest('GET', 'movie/123');
	 * 
	 * // GET запрос с параметрами
	 * $response = $kinopoisk->makeRequest('GET', 'movie', [
	 *     'page' => 1,
	 *     'limit' => 10
	 * ]);
	 * 
	 * // Запрос к другой версии API
	 * $response = $kinopoisk->makeRequest('GET', 'movie/123', [], 'v1.3');
	 * ```
	 */
	public function makeRequest(
		string $method,
		string $endpoint,
		array $queryParams = [],
		?string $apiVersion = null,
	): ResponseInterface {
		$this->validateHttpMethod($method);
		$this->validateEndpoint($endpoint);

		$version = $apiVersion ?? self::API_VERSION;
		$cacheKey = $this->generateCacheKey($method, $endpoint, $queryParams, $version);

		// Попытка получить из кэша
		if ($this->useCache && $method === 'GET') {
			$cachedResponse = $this->cache->get($cacheKey);
			if ($cachedResponse !== null) {
				$this->logger?->debug('Получаем запрос из кэша', ['cacheKey' => $cacheKey]);
				return $cachedResponse;
			}
		}

		try {
			$response = $this->executeHttpRequest($method, $endpoint, $queryParams, $version);

			// Сохранение в кэш
			if ($this->useCache && $method === 'GET' && $response->getStatusCode() === 200) {
				$this->cache->set($cacheKey, $response, self::CACHE_TTL);
				$this->logger?->debug('Запрос сохранен в кэш', ['cacheKey' => $cacheKey]);
			}

			return $response;

		} catch (GuzzleException $e) {
			$this->logger?->error('HTTP запрос не выполнен', [
				'method' => $method,
				'endpoint' => $endpoint,
				'error' => $e->getMessage(),
			]);

			throw new KinopoiskDevException(
				message: "Ошибка HTTP запроса: {$e->getMessage()}",
				code: $e->getCode(),
				previous: $e instanceof \Exception ? $e : new \Exception($e->getMessage(), $e->getCode(), $e),
			);
		}
	}

	/**
	 * Обрабатывает ответ от API с валидацией
	 *
	 * Парсит HTTP ответ от API, проверяет статус код и декодирует JSON.
	 * Обрабатывает различные типы ошибок API и логирует результаты.
	 * 
	 * @since   1.0.0
	 *
	 * @param   ResponseInterface $response HTTP ответ от API
	 *
	 * @return array<string, mixed> Декодированные данные ответа
	 * @throws KinopoiskDevException При ошибках обработки ответа
	 * @throws KinopoiskResponseException При ошибках API (401, 403, 404, 500)
	 * @throws \JsonException При ошибках парсинга JSON
	 * 
	 * @example
	 * ```php
	 * $response = $kinopoisk->makeRequest('GET', 'movie/123');
	 * $data = $kinopoisk->parseResponse($response);
	 * $movie = Movie::fromArray($data);
	 * ```
	 */
	public function parseResponse(ResponseInterface $response): array {
		$statusCode = HttpStatusCode::tryFrom($response->getStatusCode());
		$this->handleErrorStatusCode($statusCode, $response->getStatusCode());

		if ($statusCode !== HttpStatusCode::OK) {
			throw new KinopoiskDevException(
				message: "Неожиданный статус код: {$response->getStatusCode()}",
				code: $response->getStatusCode(),
			);
		}

		$body = $response->getBody()->getContents();

		try {
			$data = json_decode(
				json: $body,
				associative: true,
				depth: 512,
				flags: JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING,
			);

			$this->logger?->debug('Ответ успешно обработан', [
				'dataSize' => strlen($body),
				'hasData' => !empty($data),
			]);

			return $data;

		} catch (\JsonException $e) {
			$this->logger?->error('Ошибка парсинга JSON', [
				'error' => $e->getMessage(),
				'body' => mb_substr($body, 0, 500),
			]);

			throw new KinopoiskDevException(
				message: "Ошибка парсинга JSON: {$e->getMessage()}",
				code: $e->getCode(),
				previous: $e,
			);
		}
	}

	/**
	 * Валидирует и устанавливает API токен
	 *
	 * Проверяет наличие и формат API токена. Если токен не передан,
	 * пытается получить его из переменной окружения KINOPOISK_TOKEN.
	 * Валидирует формат токена Kinopoisk.dev.
	 * 
	 * @since   1.0.0
	 *
	 * @param   string|null $apiToken Токен API для валидации
	 *
	 * @throws ValidationException При отсутствии токена или неверном формате
	 * 
	 * @internal Внутренний метод, используется только в конструкторе
	 */
	private function validateAndSetApiToken(?string $apiToken): void {
		$token = $apiToken ?? $_ENV['KINOPOISK_TOKEN'] ?? null;

		if (is_null($token)) {
			throw ValidationException::forField(
				field: 'apiToken',
				message: 'API токен обязателен для работы с сервисом',
			);
		}

		if (!$this->isValidApiToken($token)) {
			throw ValidationException::forField(
				field: 'apiToken',
				message: 'Неверный формат API токена',
				value: $token,
			);
		}

		$this->apiToken = $token;
	}

	/**
	 * Создает HTTP клиент по умолчанию
	 *
	 * Создает экземпляр GuzzleHttp\Client с базовыми настройками
	 * для работы с API Kinopoisk.dev.
	 * 
	 * @since   1.0.0
	 *
	 * @return HttpClient Экземпляр HTTP клиента с настроенными параметрами
	 * 
	 * @internal Внутренний метод, используется только в конструкторе
	 */
	private function createDefaultHttpClient(): HttpClient {
		return new HttpClient([
			'base_uri' => self::BASE_URL,
			'timeout' => self::DEFAULT_TIMEOUT,
			'headers' => [
				'User-Agent' => "KinopoiskDev-PHP-Client/{self::APP_VERSION}",
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			],
		]);
	}

	/**
	 * Выполняет HTTP запрос
	 *
	 * Формирует полный URL запроса и выполняет его через HTTP клиент.
	 * Добавляет API токен в заголовки запроса.
	 * 
	 * @since   1.0.0
	 *
	 * @param   string                   $method       HTTP метод
	 * @param   string                   $endpoint     Конечная точка
	 * @param   array<string, mixed>     $queryParams  Параметры запроса
	 * @param   string                   $version      Версия API
	 *
	 * @return ResponseInterface Ответ от сервера
	 * @throws GuzzleException При ошибке выполнения HTTP запроса
	 * 
	 * @internal Внутренний метод, используется только в makeRequest()
	 */
	private function executeHttpRequest(
		string $method,
		string $endpoint,
		array $queryParams,
		string $version,
	): ResponseInterface {
		$url = "/{$version}/{$endpoint}";

		if (!empty($queryParams)) {
			$url .= '?' . http_build_query($queryParams);
		}

		$request = new Request($method, $url, [
			'X-API-KEY' => $this->apiToken,
		]);

		$this->logger?->debug('Making HTTP request', [
			'method' => $method,
			'url' => $url,
			'paramsCount' => count($queryParams),
		]);

		return $this->httpClient->send($request);
	}

	/**
	 * Генерирует ключ для кэширования
	 *
	 * Создает уникальный ключ кэша на основе параметров запроса.
	 * Использует SHA256 хэш для обеспечения уникальности и безопасности.
	 * 
	 * @since   1.0.0
	 *
	 * @param   string                   $method       HTTP метод
	 * @param   string                   $endpoint     Конечная точка
	 * @param   array<string, mixed>     $queryParams  Параметры запроса
	 * @param   string                   $version      Версия API
	 *
	 * @return string Уникальный ключ кэша
	 * 
	 * @internal Внутренний метод, используется только в makeRequest()
	 */
	private function generateCacheKey(
		string $method,
		string $endpoint,
		array $queryParams,
		string $version,
	): string {
		$data = [$method, $endpoint, $queryParams, $version];
		return 'kinopoisk_' . hash('sha256', serialize($data));
	}

	/**
	 * Обрабатывает ошибочные статус коды
	 *
	 * Проверяет статус код ответа и выбрасывает соответствующие исключения
	 * для известных ошибок API (401, 403, 404).
	 * 
	 * @since   1.0.0
	 *
	 * @param   HttpStatusCode|null $statusCode    Статус код как enum
	 * @param   int|null            $rawStatusCode Сырой статус код
	 *
	 * @throws KinopoiskResponseException При известных ошибках API
	 * 
	 * @internal Внутренний метод, используется только в parseResponse()
	 */
	private function handleErrorStatusCode(
		?HttpStatusCode $statusCode,
		?int $rawStatusCode = null,
	): void {
		$errorClass = match ($statusCode ?? HttpStatusCode::tryFrom($rawStatusCode ?? 0)) {
			HttpStatusCode::UNAUTHORIZED => UnauthorizedErrorResponseDto::class,
			HttpStatusCode::FORBIDDEN => ForbiddenErrorResponseDto::class,
			HttpStatusCode::NOT_FOUND => NotFoundErrorResponseDto::class,
			default => null,
		};

		if ($errorClass !== null) {
			$this->logger?->warning('API error response', [
				'statusCode' => $statusCode?->value ?? $rawStatusCode,
				'errorClass' => $errorClass,
			]);

			throw new KinopoiskResponseException($errorClass);
		}
	}

	/**
	 * Валидирует HTTP метод
	 *
	 * Проверяет, что переданный HTTP метод поддерживается API.
	 * 
	 * @since   1.0.0
	 *
	 * @param   string $method HTTP метод для валидации
	 *
	 * @throws ValidationException При неверном или неподдерживаемом методе
	 * 
	 * @internal Внутренний метод, используется только в makeRequest()
	 */
	private function validateHttpMethod(string $method): void {
		$allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];

		if (!in_array(strtoupper($method), $allowedMethods, true)) {
			throw ValidationException::forField(
				field: 'method',
				message: 'Неподдерживаемый HTTP метод',
				value: $method,
			);
		}
	}

	/**
	 * Валидирует конечную точку API
	 *
	 * Проверяет формат и валидность конечной точки API.
	 * Допускает только буквы, цифры, слеши, подчеркивания и дефисы.
	 * 
	 * @since   1.0.0
	 *
	 * @param   string $endpoint Конечная точка для валидации
	 *
	 * @throws ValidationException При неверном формате конечной точки
	 * 
	 * @internal Внутренний метод, используется только в makeRequest()
	 */
	private function validateEndpoint(string $endpoint): void {
		if (is_null($endpoint) || $endpoint === '' || !preg_match('/^[a-zA-Z0-9\/_-]+$/', $endpoint)) {
			throw ValidationException::forField(
				field: 'endpoint',
				message: 'Неверный формат конечной точки API',
				value: $endpoint,
			);
		}
	}

	/**
	 * Проверяет валидность API токена
	 *
	 * Валидирует формат токена Kinopoisk.dev. Токен должен соответствовать
	 * формату: 4 группы по 7 символов, разделенные дефисами.
	 *
	 * @param   string $token Токен API для проверки
	 *
	 * @return bool True если токен валиден, false в противном случае
	 * 
	 * @internal Внутренний метод, используется только в validateAndSetApiToken()
	 */
	private function isValidApiToken(string $token): bool {
		// Проверка формата токена Kinopoisk.dev (например: ABC1DEF-2GH3IJK-4LM5NOP-6QR7STU)
		// Используем более безопасное регулярное выражение с ограничением длины
		return preg_match('/^[A-Z0-9]{7}-[A-Z0-9]{7}-[A-Z0-9]{7}-[A-Z0-9]{7}$/', $token) === 1 
			&& strlen($token) === 31;
	}
}