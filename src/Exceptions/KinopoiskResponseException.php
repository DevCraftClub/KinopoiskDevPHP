<?php

declare(strict_types=1);

namespace KinopoiskDev\Exceptions;

use Exception;

/**
 * Исключение для ошибок ответов API Kinopoisk.dev
 *
 * Специализированное исключение для обработки ошибок HTTP ответов
 * от API Kinopoisk.dev. Автоматически извлекает информацию об ошибке
 * из объекта ответа API и формирует понятное сообщение об ошибке.
 *
 * @package KinopoiskDev\Exceptions
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Responses\Errors\UnauthorizedErrorResponseDto Для ошибки 401
 * @see     \KinopoiskDev\Responses\Errors\ForbiddenErrorResponseDto Для ошибки 403
 * @see     \KinopoiskDev\Responses\Errors\NotFoundErrorResponseDto Для ошибки 404
 *
 * @example
 * ```php
 * try {
 *     $movie = $kinopoisk->getMovieById(999999);
 * } catch (KinopoiskResponseException $e) {
 *     echo "Ошибка API: " . $e->getMessage();
 *     echo "Код: " . $e->getCode(); // 404
 * }
 * ```
 */
class KinopoiskResponseException extends Exception {

	/**
	 * Конструктор исключения ответа API
	 *
	 * Создает исключение на основе класса ответа API. Автоматически
	 * извлекает информацию об ошибке из объекта ответа и формирует
	 * сообщение об ошибке с кодом статуса.
	 *
	 * @param   string          $rspnsCls  Полное имя класса ответа API (например, UnauthorizedErrorResponseDto::class)
	 * @param   Exception|null  $previous  Предыдущее исключение в цепочке
	 *
	 * @throws \Error При неверном имени класса ответа
	 *
	 * @example
	 * ```php
	 * throw new KinopoiskResponseException(
	 *     UnauthorizedErrorResponseDto::class,
	 *     $previousException
	 * );
	 * ```
	 */
	public function __construct(
		string     $rspnsCls = '',
		?Exception $previous = NULL,
	) {
		if (!empty($rspnsCls)) {
			$response = new $rspnsCls();

			// Безопасный доступ к свойствам через reflection или приведение типа
			$error      = property_exists($response, 'error') ? $response->error : 'Unknown error';
			$message    = property_exists($response, 'message') ? $response->message : 'Unknown message';
			$statusCode = property_exists($response, 'statusCode') ? $response->statusCode : 0;

			parent::__construct("{$error}: {$message}", $statusCode, $previous);
		} else {
			parent::__construct('', 0, $previous);
		}
	}

}