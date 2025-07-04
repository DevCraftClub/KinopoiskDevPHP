<?php

namespace KinopoiskDev;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Models\Movie;
use Lombok\Getter;
use Lombok\Helper;
use Lombok\Setter;
use Dotenv;
use Psr\Http\Message\ResponseInterface;

#[Setter, Getter]
class Kinopoisk extends Helper {

	private const BASE_URL = 'https://api.kinopoisk.dev';

	private const API_VERSION = 'v1.4';

	private HttpClient $httpClient;
	private string $apiToken;

	/**
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public function __construct(?string $apiToken = NULL, ?HttpClient $httpClient = NULL) {
		parent::__construct();

		$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, dirname(__DIR__));
		$dotenv->safeLoad();

		$this->setApiToken($apiToken ?? getenv('KINOPOISK_API_TOKEN'));
		$envBaseUrl = getenv('KINOPOISK_API_URL');
		$envApiVersion = getenv('KINOPOISK_API_VERSION');

		if ($envBaseUrl) {
			$this->BASE_URL = $envBaseUrl;
		}

		if ($envApiVersion) {
			$this->API_VERSION = $envApiVersion;
		}

		if (!$this->apiToken) {
			throw new KinopoiskDevException('Ключ API не установлен!', 403);
		}

		$this->httpClient = $httpClient ?? new HttpClient([
			'base_uri' => self::BASE_URL,
			'timeout'  => 30,
			'headers'  => [
				'User-Agent'   => 'KinopoiskDev-PHP-Client/1.0',
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			],
		]);
	}

	/**
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	protected function makeRequest(string $method, string $endpoint, array $queryParams = []): ResponseInterface {
		try {
			$url = '/' . self::API_VERSION . $endpoint;

			if (!empty($queryParams)) {
				$url .= '?' . http_build_query($queryParams);
			}

			$request = new Request($method, $url, [
				'X-API-KEY' => $this->apiToken,
			]);

			return $this->httpClient->send($request);
		}
		catch (GuzzleException $e) {
			throw new KinopoiskDevException(
				"Запрос HTTP увенчался провалом: {$e->getMessage()}",
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