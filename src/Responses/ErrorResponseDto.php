<?php

declare(strict_types=1);

namespace KinopoiskDev\Responses;

use Lombok\Getter;
use Lombok\Setter;

/**
 * DTO для представления ответа об ошибке API
 *
 * Класс инкапсулирует информацию об ошибке, возвращаемой API Kinopoisk.dev,
 * включая HTTP статус код, сообщение об ошибке и тип ошибки.
 * Используется для унифицированной обработки ошибочных ответов API.
 *
 * @package KinopoiskDev\Responses
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     BaseResponseDto
 */
class ErrorResponseDto extends BaseResponseDto {

	/**
	 * Конструктор для создания DTO ошибки
	 *
	 * Инициализирует все обязательные поля ответа об ошибке.
	 * Все свойства являются readonly для обеспечения неизменности данных.
	 *
	 * @param   int     $statusCode  HTTP статус код ошибки (например, 400, 401, 403, 404, 500)
	 * @param   string  $message     Человекочитаемое сообщение об ошибке на русском языке
	 * @param   string  $error       Краткое техническое описание типа ошибки (например, "Bad Request", "Unauthorized")
	 */
	public function __construct(
		#[Getter] public readonly int    $statusCode,
		#[Getter] public readonly string $message,
		#[Getter] public readonly string $error,
	) {}

	/**
	 * {@inheritDoc}
	 *
	 * Создает экземпляр DTO ошибки из массива данных API ответа.
	 * Извлекает обязательные поля statusCode, message и error из массива.
	 *
	 * @param   array<string, mixed>  $data  Ассоциативный массив с данными ошибки из API ответа
	 *
	 * @return static Экземпляр ErrorResponseDto с данными ошибки
	 *
	 * @throws \InvalidArgumentException Если в массиве отсутствуют обязательные поля
	 */
	public static function fromArray(array $data): static {
		return new self(
			statusCode: $data['statusCode'],
			message   : $data['message'],
			error     : $data['error'],
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * Преобразует DTO ошибки в ассоциативный массив для сериализации.
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
