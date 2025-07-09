<?php

declare(strict_types=1);

namespace KinopoiskDev;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use KinopoiskDev\Attributes\Sensitive;
use KinopoiskDev\Contracts\{CacheInterface, HttpClientInterface, LoggerInterface};
use KinopoiskDev\Enums\HttpStatusCode;
use KinopoiskDev\Exceptions\{KinopoiskDevException, KinopoiskResponseException, ValidationException};
use KinopoiskDev\Responses\Errors\{ForbiddenErrorResponseDto, NotFoundErrorResponseDto, UnauthorizedErrorResponseDto};
use KinopoiskDev\Services\{CacheService, HttpService, ValidationService};
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
 * @package KinopoiskDev
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 2.0.0
 */
final readonly class Kinopoisk extends Helper {

	private const string BASE_URL = 'https://api.kinopoisk.dev';
	private const string API_VERSION = 'v1.4';
	private const string APP_VERSION = '2.0.0';
	private const int DEFAULT_TIMEOUT = 30;
	private const int CACHE_TTL = 3600; // 1 час

	#[Getter]
	private HttpClientInterface $httpClient;

	#[Getter, Sensitive]
	private string $apiToken;

	#[Getter]
	private CacheInterface $cache;

	#[Getter]
	private ValidationService $validator;

	#[Getter]
	private ?LoggerInterface $logger;

	/**
	 * Конструктор клиента API Kinopoisk
	 *
	 * @param   string|null             $apiToken    Токен авторизации API
	 * @param   HttpClientInterface|null $httpClient  HTTP клиент
	 * @param   CacheInterface|null     $cache       Сервис кэширования
	 * @param   LoggerInterface|null    $logger      Логгер
	 * @param   bool                    $useCache    Использовать кэширование
	 *
	 * @throws ValidationException При отсутствии токена
	 * @throws KinopoiskDevException При ошибке инициализации
	 */
	public function __construct(
		?string $apiToken = null,
		?HttpClientInterface $httpClient = null,
		?CacheInterface $cache = null,
		?LoggerInterface $logger = null,
		private readonly bool $useCache = false,
	) {
		parent::__construct();

		$this->validateAndSetApiToken($apiToken);
		$this->httpClient = $httpClient ?? $this->createDefaultHttpClient();
		$this->cache = $cache ?? new CacheService(new FilesystemAdapter());
		$this->validator = new ValidationService();
		$this->logger = $logger;

		$this->logger?->info('Kinopoisk client initialized', [
			'version' => self::APP_VERSION,
			'useCache' => $this->useCache,
		]);
	}

	/**
	 * Выполняет HTTP запрос к API с поддержкой кэширования
	 *
	 * @param   string       $method       HTTP метод
	 * @param   string       $endpoint     Конечная точка API
	 * @param   array        $queryParams  Параметры запроса
	 * @param   string|null  $apiVersion   Версия API
	 *
	 * @return ResponseInterface Ответ от API
	 * @throws KinopoiskDevException При ошибках запроса
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
				$this->logger?->debug('Cache hit for request', ['cacheKey' => $cacheKey]);
				return $cachedResponse;
			}
		}

		try {
			$response = $this->executeHttpRequest($method, $endpoint, $queryParams, $version);

			// Сохранение в кэш
			if ($this->useCache && $method === 'GET' && $response->getStatusCode() === 200) {
				$this->cache->set($cacheKey, $response, self::CACHE_TTL);
				$this->logger?->debug('Response cached', ['cacheKey' => $cacheKey]);
			}

			return $response;

		} catch (GuzzleException $e) {
			$this->logger?->error('HTTP request failed', [
				'method' => $method,
				'endpoint' => $endpoint,
				'error' => $e->getMessage(),
			]);

			throw new KinopoiskDevException(
				message: "Ошибка HTTP запроса: {$e->getMessage()}",
				code: $e->getCode(),
				previous: $e,
			);
		}
	}

	/**
	 * Обрабатывает ответ от API с валидацией
	 *
	 * @param   ResponseInterface $response HTTP ответ
	 *
	 * @return array Декодированные данные
	 * @throws KinopoiskDevException При ошибках обработки
	 * @throws KinopoiskResponseException При ошибках API
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

			$this->logger?->debug('Response parsed successfully', [
				'dataSize' => strlen($body),
				'hasData' => !empty($data),
			]);

			return $data;

		} catch (\JsonException $e) {
			$this->logger?->error('JSON parsing failed', [
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
	 * @param   string|null $apiToken Токен API
	 *
	 * @throws ValidationException При отсутствии токена
	 */
	private function validateAndSetApiToken(?string $apiToken): void {
		$token = $apiToken ?? $_ENV['KINOPOISK_TOKEN'] ?? null;

		if (empty($token)) {
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
	 * @return HttpClientInterface Экземпляр HTTP клиента
	 */
	private function createDefaultHttpClient(): HttpClientInterface {
		return new HttpService(new HttpClient([
			'base_uri' => self::BASE_URL,
			'timeout' => self::DEFAULT_TIMEOUT,
			'headers' => [
				'User-Agent' => "KinopoiskDev-PHP-Client/{self::APP_VERSION}",
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			],
		]));
	}

	/**
	 * Выполняет HTTP запрос
	 *
	 * @param   string $method       HTTP метод
	 * @param   string $endpoint     Конечная точка
	 * @param   array  $queryParams  Параметры запроса
	 * @param   string $version      Версия API
	 *
	 * @return ResponseInterface Ответ
	 * @throws GuzzleException При ошибке запроса
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
	 * @param   string $method       HTTP метод
	 * @param   string $endpoint     Конечная точка
	 * @param   array  $queryParams  Параметры запроса
	 * @param   string $version      Версия API
	 *
	 * @return string Ключ кэша
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
	 * @param   HttpStatusCode|null $statusCode    Статус код
	 * @param   int|null            $rawStatusCode Сырой статус код
	 *
	 * @throws KinopoiskResponseException При известных ошибках
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
	 * @param   string $method HTTP метод
	 *
	 * @throws ValidationException При неверном методе
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
	 * @param   string $endpoint Конечная точка
	 *
	 * @throws ValidationException При неверной точке
	 */
	private function validateEndpoint(string $endpoint): void {
		if (empty($endpoint) || !preg_match('/^[a-zA-Z0-9\/_-]+$/', $endpoint)) {
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
	 * @param   string $token Токен API
	 *
	 * @return bool True если токен валиден
	 */
	private function isValidApiToken(string $token): bool {
		// Проверка формата токена Kinopoisk.dev (например: ABC1DEF-2GH3IJK-4LM5NOP-6QR7STU)
		return preg_match('/^[A-Z0-9]{7}-[A-Z0-9]{7}-[A-Z0-9]{7}-[A-Z0-9]{7}$/', $token) === 1;
	}
}