<?php

declare(strict_types=1);

namespace KinopoiskDev\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Исключение для ошибок валидации данных
 *
 * Специализированное исключение для обработки ошибок валидации
 * с поддержкой множественных ошибок и детальной диагностики.
 *
 * @package KinopoiskDev\Exceptions
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
final class ValidationException extends RuntimeException {

	/**
	 * @param   string         $message    Основное сообщение об ошибке
	 * @param   array<string, string> $errors     Список ошибок валидации
	 * @param   string|null    $field      Поле, вызвавшее ошибку
	 * @param   mixed          $value      Значение, не прошедшее валидацию
	 * @param   int            $code       Код ошибки
	 * @param   Throwable|null $previous   Предыдущее исключение
	 */
	public function __construct(
		string $message = 'Ошибка валидации данных',
		private readonly array $errors = [],
		private readonly ?string $field = null,
		private readonly mixed $value = null,
		int $code = 0,
		?Throwable $previous = null,
	) {
		parent::__construct($message, $code, $previous);
	}

	/**
	 * Возвращает список всех ошибок валидации
	 *
	 * @return array<string, string> Массив ошибок в формате ['field' => 'error_message']
	 */
	public function getErrors(): array {
		return $this->errors;
	}

	/**
	 * Возвращает поле, вызвавшее ошибку
	 *
	 * @return string|null Название поля или null
	 */
	public function getField(): ?string {
		return $this->field;
	}

	/**
	 * Возвращает значение, не прошедшее валидацию
	 *
	 * @return mixed Проблемное значение
	 */
	public function getValue(): mixed {
		return $this->value;
	}

	/**
	 * Проверяет, есть ли ошибки валидации
	 *
	 * @return bool True если есть ошибки
	 */
	public function hasErrors(): bool {
		return !empty($this->errors);
	}

	/**
	 * Возвращает первую ошибку валидации
	 *
	 * @return string|null Текст первой ошибки или null
	 */
	public function getFirstError(): ?string {
		return $this->hasErrors() ? reset($this->errors) : null;
	}

	/**
	 * Создает исключение для конкретного поля
	 *
	 * @param   string $field   Название поля
	 * @param   string $message Сообщение об ошибке
	 * @param   mixed  $value   Значение поля
	 *
	 * @return self Экземпляр исключения
	 */
	public static function forField(string $field, string $message, mixed $value = null): self {
		return new self(
			message: "Ошибка валидации поля '{$field}': {$message}",
			errors: [$field => $message],
			field: $field,
			value: $value,
		);
	}

	/**
	 * Создает исключение для множественных ошибок
	 *
	 * @param   array<string, string> $errors Массив ошибок ['field' => 'message']
	 *
	 * @return self Экземпляр исключения
	 */
	public static function withErrors(array $errors): self {
		$count = count($errors);
		$message = match ($count) {
			0 => 'Неизвестная ошибка валидации',
			1 => 'Обнаружена 1 ошибка валидации',
			default => "Обнаружено {$count} ошибок валидации"
		};

		return new self(message: $message, errors: $errors);
	}
}