<?php

declare(strict_types=1);

namespace KinopoiskDev\Responses;

/**
 * DTO для представления ответа с ошибкой доступа запрещен (HTTP 403)
 *
 * Этот класс наследует от BaseResponseDto и предоставляет специализированное
 * представление ошибки 403 Forbidden, которая возникает при превышении дневного
 * лимита запросов к API Kinopoisk.dev. Все свойства класса являются readonly
 * для обеспечения неизменности данных ответа.
 *
 * @package KinopoiskDev\Responses
 * @see     BaseResponseDto
 * @author Maxim Harder
 * @version 1.0.0
 * @since   1.0.0
 */
class ForbiddenErrorResponseDto extends BaseResponseDto {

	/**
	 * Конструктор для создания DTO ошибки доступа запрещен
	 *
	 * Инициализирует все свойства ответа об ошибке 403 Forbidden со значениями
	 * по умолчанию. Все параметры являются readonly для обеспечения неизменности
	 * данных после создания объекта.
	 *
	 * @param int    $statusCode HTTP статус код 403
	 * @param string $message    Сообщение об ошибке (по умолчанию: "Превышен дневной лимит!")
	 * @param string $error      Техническое описание ошибки (по умолчанию: "Forbidden")
	 */
	public function __construct(
		public readonly int $statusCode = 403,
		public readonly string $message = 'Превышен дневной лимит!',
		public readonly string $error = 'Forbidden',
	) {}

	/**
	 * {@inheritDoc}
	 *
	 * Создает экземпляр DTO ошибки 403 из массива данных API ответа.
	 * Использует значения по умолчанию для отсутствующих полей в массиве.
	 *
	 * @param array $data Ассоциативный массив с данными ошибки из API ответа
	 *
	 * @return static Экземпляр ForbiddenErrorResponseDto с данными ошибки
	 */
	public static function fromArray(array $data): static {
		return new static(
			statusCode: $data['statusCode'] ?? 403,
			message: $data['message'] ?? 'Превышен дневной лимит!',
			error: $data['error'] ?? 'Forbidden',
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * Преобразует DTO ошибки 403 в ассоциативный массив для сериализации.
	 * Структура возвращаемого массива соответствует формату API ответа.
	 *
	 * @return array Ассоциативный массив с полями statusCode, message и error
	 */
	public function toArray(): array {
		return [
			'statusCode' => $this->statusCode,
			'message' => $this->message,
			'error' => $this->error,
		];
	}
}
