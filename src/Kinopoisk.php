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
	private const APP_VERSION = '1.0.0';

	private HttpClient $httpClient;
	private string $apiToken;

	/**
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public function __construct(?string $apiToken = NULL, ?HttpClient $httpClient = NULL) {
		parent::__construct();

		$this->setApiToken($apiToken);

		if (!$this->getApiToken()) {
			throw new KinopoiskDevException('Ключ API не установлен!', 401);
		}

		$this->httpClient = $httpClient ?? new HttpClient([
			'base_uri' => self::BASE_URL . '/' . self::API_VERSION,
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
	protected function makeRequest(string $method, string $endpoint, array $queryParams = []): ResponseInterface {
		try {
			$url = $endpoint;

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