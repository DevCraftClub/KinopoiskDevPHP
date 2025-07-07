<?php

namespace KinopoiskDev;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use KinopoiskDev\Enums\HttpStatusCode;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use KinopoiskDev\Responses\Errors\ForbiddenErrorResponseDto;
use KinopoiskDev\Responses\Errors\NotFoundErrorResponseDto;
use KinopoiskDev\Responses\Errors\UnauthorizedErrorResponseDto;
use Lombok\Getter;
use Lombok\Helper;
use Lombok\Setter;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

#[Setter, Getter]
class Kinopoisk extends Helper {

	private const string BASE_URL    = 'https://api.kinopoisk.dev';

	private const string API_VERSION = 'v1.4';

	private const string APP_VERSION = '1.0.0';

	private readonly HttpClient $httpClient;
	private readonly string     $apiToken;
	private readonly bool       $useCache;

	/**
	 * Конструктор для инициализации клиента API Kinopoisk
	 *
	 * @param   string|null      $apiToken    Токен для авторизации в API
	 * @param   HttpClient|null  $httpClient  HTTP клиент для запросов
	 * @param   bool             $useCache    Использовать ли кэширование запросов
	 *
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException При отсутствии токена API
	 */
	public function __construct(?string $apiToken = NULL, ?HttpClient $httpClient = NULL, bool $useCache = FALSE) {
		parent::__construct();

		$this->setApiToken($apiToken);

		if (!$this->getApiToken()) {
			$this->handleErrorStatusCode(HttpStatusCode::UNAUTHORIZED);
		}

		$this->useCache = $useCache;

		$this->httpClient = $httpClient ?? new HttpClient([
			'base_uri' => self::BASE_URL,
			'timeout'  => 30,
			'headers'  => [
				'User-Agent'   => 'KinopoiskDev-PHP-Client/' . self::APP_VERSION,
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			],
		]);
	}

	/**
	 * Выполняет HTTP запрос к API с поддержкой кэширования
	 *
	 * @param   string       $method       HTTP метод запроса
	 * @param   string       $endpoint     Конечная точка API
	 * @param   array        $queryParams  Параметры запроса
	 * @param   string|null  $apiVersion   Версия API
	 *
	 * @return ResponseInterface Ответ от API
	 * @throws KinopoiskDevException При ошибках запроса или кэширования
	 */
	protected function makeRequest(string $method, string $endpoint, array $queryParams = [], ?string $apiVersion = NULL): ResponseInterface {
		try {
			$cache     = NULL;
			$cacheItem = NULL;
			$version   = $apiVersion ?? self::API_VERSION;
			$cacheKey  = md5($method . $endpoint . json_encode($queryParams, JSON_THROW_ON_ERROR) . $version);

			if ($this->useCache) {
				$cache     = new FilesystemAdapter();
				$cacheItem = $cache->getItem($cacheKey);
				if ($cacheItem->isHit()) {
					return $cacheItem->get();
				}
			}

			try {
				$url = "/{$version}/{$endpoint}";

				if (!empty($queryParams)) {
					$url .= '?' . http_build_query($queryParams);
				}

				$request = new Request($method, $url, [
					'X-API-KEY' => $this->apiToken,
				]);

				$result = $this->httpClient->send($request);

				if ($this->useCache && $cacheItem !== NULL && $cache !== NULL) {
					$cacheItem->set($result);
					$cache->save($cacheItem);
				}

				return $result;
			} catch (GuzzleException $e) {
				throw new KinopoiskDevException(
					'Запрос HTTP увенчался провалом: ' . $e->getMessage(),
					$e->getCode(),
					$e,
				);
			}
		} catch (\Exception|\JsonException|InvalidArgumentException $e) {
			throw new KinopoiskDevException(
				'Проблемы с инициализацией кеша: '.  $e->getMessage(),
				$e->getCode(),
				$e,
			);
		}
	}

	/**
	 * Обрабатывает ответ от API с проверкой статус кода
	 *
	 * @param   ResponseInterface  $response  HTTP ответ
	 *
	 * @return array Декодированные данные JSON
	 * @throws KinopoiskDevException|KinopoiskResponseException|\JsonException При ошибках API или парсинга
	 */
	protected function parseResponse(ResponseInterface $response): array {
		$statusCode = HttpStatusCode::tryFrom($response->getStatusCode());
		$body       = $response->getBody()->getContents();

		$this->handleErrorStatusCode($statusCode, $response->getStatusCode());

		if ($statusCode !== HttpStatusCode::OK) {
			throw new KinopoiskDevException(
				'Произошла ошибка при отправке запроса. Ответ вернул код статуса: ' . $response->getStatusCode(),
				$response->getStatusCode(),
			);
		}

		$data = json_decode($body, TRUE, 512, JSON_THROW_ON_ERROR);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new KinopoiskDevException(
				'Невозможно разобрать JSON данные: ' . json_last_error_msg(),
			);
		}

		return $data;
	}

	/**
	 * Обрабатывает ошибочные статус коды HTTP
	 *
	 * @param   HttpStatusCode|null  $statusCode     Enum статус кода
	 * @param   int|null             $rawStatusCode  Сырой статус код
	 *
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException При известных ошибках API
	 */
	private function handleErrorStatusCode(?HttpStatusCode $statusCode, ?int $rawStatusCode = NULL): void {
		match ($statusCode) {
			HttpStatusCode::UNAUTHORIZED => throw new KinopoiskResponseException(UnauthorizedErrorResponseDto::class),
			HttpStatusCode::FORBIDDEN    => throw new KinopoiskResponseException(ForbiddenErrorResponseDto::class),
			HttpStatusCode::NOT_FOUND    => throw new KinopoiskResponseException(NotFoundErrorResponseDto::class),
			default                      => NULL,
		};
	}

}