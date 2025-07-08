<?php

namespace KinopoiskDev\Enums;

/**
 * Enum для HTTP статус кодов
 *
 * Предоставляет типизированные константы для основных HTTP статус кодов,
 * используемых в API Kinopoisk.dev
 */
enum HttpStatusCode: int {

	case OK                    = 200;
	case UNAUTHORIZED          = 401;
	case FORBIDDEN             = 403;
	case NOT_FOUND             = 404;
	case INTERNAL_SERVER_ERROR = 500;

	/**
	 * Возвращает описание статус кода на русском языке
	 */
	public function getDescription(): string {
		return match ($this) {
			self::OK                    => 'Успешный запрос',
			self::UNAUTHORIZED          => 'Неавторизован',
			self::FORBIDDEN             => 'Доступ запрещён',
			self::NOT_FOUND             => 'Не найдено',
			self::INTERNAL_SERVER_ERROR => 'Внутренняя ошибка сервера',
		};
	}

	/**
	 * Проверяет, является ли статус кодом ошибки
	 */
	public function isError(): bool {
		return $this->value >= 400;
	}

	/**
	 * Проверяет, является ли статус кодом успеха
	 */
	public function isSuccess(): bool {
		return $this->value >= 200 && $this->value < 300;
	}

}