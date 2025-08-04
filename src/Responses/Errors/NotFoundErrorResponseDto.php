<?php

declare(strict_types=1);

namespace KinopoiskDev\Responses\Errors;

use KinopoiskDev\Responses\BaseResponseDto;

/**
 * DTO для представления ответа с ошибкой "не найдено" (HTTP 404)
 *
 * Этот класс наследует от BaseResponseDto и предоставляет специализированное
 * представление ошибки 404 Not Found, которая возникает когда запрошенный
 * ресурс не найден или лимит запросов к API Kinopoisk.dev был превышен.
 * Все свойства класса являются  для обеспечения неизменности данных ответа.
 *
 * @package KinopoiskDev\Responses
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     BaseResponseDto Базовый класс для всех DTO ответов
 */
class NotFoundErrorResponseDto extends BaseResponseDto {

	/**
	 * Конструктор для создания DTO ошибки "не найдено"
	 *
	 * Инициализирует все свойства ответа об ошибке 404 Not Found со значениями
	 * по умолчанию. Все параметры являются  для обеспечения неизменности
	 * данных после создания объекта.
	 *
	 * @param   int     $statusCode  HTTP статус код 404
	 * @param   string  $message     Сообщение об ошибке (по умолчанию: сообщение о превышении лимита)
	 * @param   string  $error       Техническое описание ошибки (по умолчанию: "Not Found")
	 */
	public function __construct(
		public  int    $statusCode = 404,
		public  string $message = 'Запрошенный метод не дал никаких результатов, либо лимит запросов на сегодня был превышен!',
		public  string $error = 'Not Found',
	) {}

	/**
	 * {@inheritDoc}
	 *
	 * Создает экземпляр DTO ошибки 404 из массива данных API ответа.
	 * Использует значения по умолчанию для отсутствующих полей в массиве.
	 *
	 * @param   array<string, mixed>  $data  Ассоциативный массив с данными ошибки из API ответа, содержащий ключи:
	 *                        - statusCode: int - HTTP статус код (по умолчанию 404)
	 *                        - message: string - сообщение об ошибке
	 *                        - error: string - техническое описание ошибки
	 *
	 * @return static Экземпляр NotFoundErrorResponseDto с данными ошибки
	 */
	public static function fromArray(array $data): static {
		return new self(
			statusCode: $data['statusCode'] ?? 404,
			message   : $data['message'] ?? 'Запрошенный метод не дал никаких результатов, либо лимит запросов на сегодня был превышен!',
			error     : $data['error'] ?? 'Not Found',
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * Преобразует DTO ошибки 404 в ассоциативный массив для сериализации.
	 * Структура возвращаемого массива соответствует формату API ответа.
	 *
	 * @return array Ассоциативный массив с полями statusCode, message и error
	 */
	public function toArray(): array {
		return [
			'statusCode' => $this->statusCode,
			'message'    => $this->message,
			'error'      => $this->error,
		];
	}

}
