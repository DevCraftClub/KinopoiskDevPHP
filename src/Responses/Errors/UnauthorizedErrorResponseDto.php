<?php

declare(strict_types=1);

namespace KinopoiskDev\Responses\Errors;

use KinopoiskDev\Responses\BaseResponseDto;

/**
 * DTO для представления ответа об ошибке авторизации API
 *
 * Специализированный класс для обработки ошибок авторизации (HTTP 401),
 * возникающих при отсутствии или недействительности токена доступа.
 * Наследуется от BaseResponseDto и предоставляет предустановленные
 * значения для типичных ошибок авторизации.
 *
 * @package KinopoiskDev\Responses\Errors
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Responses\BaseResponseDto
 * @see     \KinopoiskDev\Responses\ErrorResponseDto
 */
class UnauthorizedErrorResponseDto extends BaseResponseDto {

	/**
	 * Конструктор для создания DTO ошибки авторизации
	 *
	 * Инициализирует объект с предустановленными значениями для типичных
	 * ошибок авторизации. Все параметры имеют значения по умолчанию,
	 * соответствующие стандартному ответу об отсутствии токена.
	 * Все свойства являются readonly для обеспечения неизменности данных.
	 *
	 * @param   int     $statusCode  HTTP статус код авторизации (по умолчанию 401 - Unauthorized)
	 * @param   string  $message     Сообщение об ошибке на русском языке (по умолчанию "В запросе не указан токен!")
	 * @param   string  $error       Краткое техническое описание ошибки (по умолчанию "Unauthorized")
	 */
	public function __construct(
		public readonly int    $statusCode = 401,
		public readonly string $message = 'В запросе не указан токен!',
		public readonly string $error = 'Unauthorized',
	) {}

	/**
	 * {@inheritDoc}
	 *
	 * Создает экземпляр DTO ошибки авторизации из массива данных API ответа.
	 * Использует значения по умолчанию для отсутствующих полей, что обеспечивает
	 * корректное создание объекта даже при неполных данных от API.
	 *
	 * @param   array  $data  Ассоциативный массив с данными ошибки авторизации, может содержать:
	 *                        - statusCode: int - HTTP статус код (по умолчанию 401)
	 *                        - message: string - сообщение об ошибке (по умолчанию "В запросе не указан токен!")
	 *                        - error: string - тип ошибки (по умолчанию "Unauthorized")
	 *
	 * @return static Новый экземпляр UnauthorizedErrorResponseDto с данными ошибки авторизации
	 */
	public static function fromArray(array $data): static {
		return new static(
			statusCode: $data['statusCode'] ?? 401,
			message   : $data['message'] ?? 'В запросе не указан токен!',
			error     : $data['error'] ?? 'Unauthorized',
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * Преобразует DTO ошибки авторизации в ассоциативный массив для сериализации.
	 * Структура возвращаемого массива полностью соответствует формату API ответа
	 * и содержит все необходимые поля для обработки ошибки авторизации.
	 *
	 * @return array Ассоциативный массив с полями:
	 *               - statusCode: int - HTTP статус код ошибки
	 *               - message: string - человекочитаемое сообщение об ошибке
	 *               - error: string - техническое описание типа ошибки
	 */
	public function toArray(): array {
		return [
			'statusCode' => $this->statusCode,
			'message'    => $this->message,
			'error'      => $this->error,
		];
	}

}
