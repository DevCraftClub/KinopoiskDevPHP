<?php

declare(strict_types=1);

namespace KinopoiskDev\Exceptions;

use Exception;

/**
 * Базовое исключение для всех ошибок библиотеки KinopoiskDev
 *
 * Основной класс исключений, от которого наследуются все специфические
 * исключения библиотеки. Предоставляет единообразный интерфейс для
 * обработки ошибок с поддержкой цепочки исключений.
 *
 * @package KinopoiskDev\Exceptions
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Exceptions\ValidationException Для ошибок валидации
 * @see     \KinopoiskDev\Exceptions\KinopoiskResponseException Для ошибок API
 *
 * @example
 * ```php
 * try {
 *     $kinopoisk = new Kinopoisk();
 *     $movie = $kinopoisk->getMovieById(123);
 * } catch (KinopoiskDevException $e) {
 *     echo "Ошибка: " . $e->getMessage();
 *     echo "Код: " . $e->getCode();
 * }
 * ```
 */
class KinopoiskDevException extends Exception {

	/**
	 * Конструктор исключения
	 *
	 * Создает новый экземпляр исключения с указанным сообщением,
	 * кодом ошибки и предыдущим исключением для цепочки.
	 *
	 * @param   string          $message   Сообщение об ошибке
	 * @param   int             $code      Код ошибки (по умолчанию 0)
	 * @param   Exception|null  $previous  Предыдущее исключение в цепочке
	 *
	 * @example
	 * ```php
	 * throw new KinopoiskDevException(
	 *     'Ошибка подключения к API',
	 *     500,
	 *     $previousException
	 * );
	 * ```
	 */
	public function __construct(
		string     $message = '',
		int        $code = 0,
		?Exception $previous = NULL,
	) {
		parent::__construct($message, $code, $previous);
	}

}