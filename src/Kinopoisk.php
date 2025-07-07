<?php

namespace KinopoiskDev;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use KinopoiskDev\Exceptions\KinopoiskDevException;
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

	private HttpClient $httpClient;
	private string     $apiToken;
	private bool       $useCache = FALSE;

	/**
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public function __construct(?string $apiToken = NULL, ?HttpClient $httpClient = NULL, bool $useCache = FALSE) {
		parent::__construct();

		$this->setApiToken($apiToken);

		if (!$this->getApiToken()) {
			throw new KinopoiskDevException('Ключ API не установлен!', 401);
		}

		$this->setUseCache($useCache);

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
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	protected function makeRequest(string $method, string $endpoint, array $queryParams = [], ?string $apiVersion = NULL): ResponseInterface {
		try {
			$cache = null;
			$cacheItem = null;
			$version = $apiVersion ?? self::API_VERSION;
			$cacheKey = md5($method . $endpoint . json_encode($queryParams, JSON_THROW_ON_ERROR) . $version);

			if ($this->getUseCache()) {
				$cache = new FilesystemAdapter();
				$cacheItem = $cache->getItem($cacheKey);
				if ($cacheItem->isHit()) {
					return $cacheItem->get();
				}
			}

			try {
				$url     = "/{$version}/{$endpoint}";

				if (!empty($queryParams)) {
					$url .= '?' . http_build_query($queryParams);
				}

				$request = new Request($method, $url, [
					'X-API-KEY' => $this->apiToken,
				]);

				$result = $this->httpClient->send($request);

				if ($this->getUseCache()) {
					$cacheItem->set($result);
					$cache->save($cacheItem);
				}

				return $result;
			} catch (GuzzleException $e) {
				throw new KinopoiskDevException(
					"Запрос HTTP увенчался провалом: {$e->getMessage()}",
					$e->getCode(),
					$e,
				);
			}
		} catch (\Exception|\JsonException|InvalidArgumentException $e) {
			throw new KinopoiskDevException(
				"Проблемы с инициализацией кеша: {$e->getMessage()}",
				$e->getCode(),
				$e,
			);
		}
	}

	/**
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 * @throws \JsonException
	 */
	protected function parseResponse(ResponseInterface $response): array {
		$statusCode = $response->getStatusCode();
		$body       = $response->getBody()->getContents();

		if ($statusCode < 200 || $statusCode >= 300) {
			throw new KinopoiskDevException(
				"API вернуло код {$statusCode} ошибки: {$body}",
				$statusCode,
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

}