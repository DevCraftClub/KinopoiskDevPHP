<?php

declare(strict_types=1);

namespace KinopoiskDev\Services;

use GuzzleHttp\ClientInterface;
use KinopoiskDev\Contracts\HttpClientInterface;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

/**
 * Сервис для HTTP запросов
 *
 * Адаптер для Guzzle HTTP клиента, реализующий собственный интерфейс.
 * Обеспечивает единообразный способ выполнения HTTP запросов.
 *
 * @package KinopoiskDev\Services
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
final readonly class HttpService implements HttpClientInterface {

	/**
	 * @param   ClientInterface $client Guzzle HTTP клиент
	 */
	public function __construct(
		private ClientInterface $client,
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function send(RequestInterface $request): ResponseInterface {
		return $this->client->send($request);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(string $uri, array $options = []): ResponseInterface {
		return $this->client->get($uri, $options);
	}

	/**
	 * {@inheritDoc}
	 */
	public function post(string $uri, array $options = []): ResponseInterface {
		return $this->client->post($uri, $options);
	}

	/**
	 * {@inheritDoc}
	 */
	public function put(string $uri, array $options = []): ResponseInterface {
		return $this->client->put($uri, $options);
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(string $uri, array $options = []): ResponseInterface {
		return $this->client->delete($uri, $options);
	}
}