<?php

declare(strict_types=1);

namespace KinopoiskDev\Contracts;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

/**
 * Интерфейс для HTTP клиента
 *
 * Определяет контракт для выполнения HTTP запросов
 * с поддержкой различных HTTP методов и конфигураций.
 *
 * @package KinopoiskDev\Contracts
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
interface HttpClientInterface {

	/**
	 * Отправляет HTTP запрос
	 *
	 * @param   RequestInterface $request HTTP запрос
	 *
	 * @return ResponseInterface HTTP ответ
	 * @throws \GuzzleHttp\Exception\GuzzleException При ошибке запроса
	 */
	public function send(RequestInterface $request): ResponseInterface;

	/**
	 * Выполняет GET запрос
	 *
	 * @param   string $uri     URI для запроса
	 * @param   array  $options Опции запроса
	 *
	 * @return ResponseInterface HTTP ответ
	 * @throws \GuzzleHttp\Exception\GuzzleException При ошибке запроса
	 */
	public function get(string $uri, array $options = []): ResponseInterface;

	/**
	 * Выполняет POST запрос
	 *
	 * @param   string $uri     URI для запроса
	 * @param   array  $options Опции запроса
	 *
	 * @return ResponseInterface HTTP ответ
	 * @throws \GuzzleHttp\Exception\GuzzleException При ошибке запроса
	 */
	public function post(string $uri, array $options = []): ResponseInterface;

	/**
	 * Выполняет PUT запрос
	 *
	 * @param   string $uri     URI для запроса
	 * @param   array  $options Опции запроса
	 *
	 * @return ResponseInterface HTTP ответ
	 * @throws \GuzzleHttp\Exception\GuzzleException При ошибке запроса
	 */
	public function put(string $uri, array $options = []): ResponseInterface;

	/**
	 * Выполняет DELETE запрос
	 *
	 * @param   string $uri     URI для запроса
	 * @param   array  $options Опции запроса
	 *
	 * @return ResponseInterface HTTP ответ
	 * @throws \GuzzleHttp\Exception\GuzzleException При ошибке запроса
	 */
	public function delete(string $uri, array $options = []): ResponseInterface;
}