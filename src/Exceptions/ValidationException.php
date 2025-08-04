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
 * Используется для валидации входных данных, параметров API
 * и моделей данных.
 *
 * @package KinopoiskDev\Exceptions
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Services\ValidationService Сервис валидации
 * @see     \KinopoiskDev\Attributes\Validation Атрибут валидации
 *
 * @example
 * ```php
 * try {
 *     $movie = Movie::fromArray($data);
 *     $movie->validate();
 * } catch (ValidationException $e) {
 *     foreach ($e->getErrors() as $field => $error) {
 *         echo "Поле {$field}: {$error}\n";
 *     }
 * }
 * ```
 */
final class ValidationException extends RuntimeException {

	/** @var array<string, string> Массив ошибок валидации в формате ['field' => 'message'] */
	private array $errors;

	/** @var string|null Название поля, вызвавшего ошибку */
	private ?string $field;

	/** @var mixed Значение, не прошедшее валидацию */
	private mixed $value;

	/**
	 * Конструктор исключения валидации
	 *
	 * Создает новый экземпляр исключения валидации с указанными параметрами.
	 * Поддерживает как одиночные ошибки для конкретного поля,
	 * так и множественные ошибки для нескольких полей.
	 *
	 * @param   string                 $message   Основное сообщение об ошибке
	 * @param   array<string, string>  $errors    Список ошибок валидации ['field' => 'message']
	 * @param   string|null            $field     Поле, вызвавшее ошибку
	 * @param   mixed                  $value     Значение, не прошедшее валидацию
	 * @param   int                    $code      Код ошибки (по умолчанию 0)
	 * @param   Throwable|null         $previous  Предыдущее исключение в цепочке
	 *
	 * @example
	 * ```php
	 * throw new ValidationException(
	 *     'Ошибка валидации фильма',
	 *     ['title' => 'Название обязательно', 'year' => 'Год должен быть положительным'],
	 *     'title',
	 *     null
	 * );
	 * ```
	 */
	public function __construct(
		string     $message = 'Ошибка валидации данных',
		array      $errors = [],
		?string    $field = NULL,
		mixed      $value = NULL,
		int        $code = 0,
		?Throwable $previous = NULL,
	) {
		parent::__construct($message, $code, $previous);
		$this->errors = $errors;
		$this->field  = $field;
		$this->value  = $value;
	}

	/**
	 * Создает исключение для конкретного поля
	 *
	 * Фабричный метод для создания исключения валидации
	 * для одного конкретного поля с указанным сообщением об ошибке.
	 *
	 * @param   string  $field    Название поля, вызвавшего ошибку
	 * @param   string  $message  Сообщение об ошибке валидации
	 * @param   mixed   $value    Значение поля, не прошедшее валидацию
	 *
	 * @return self Экземпляр исключения валидации
	 *
	 * @example
	 * ```php
	 * throw ValidationException::forField(
	 *     'email',
	 *     'Неверный формат email адреса',
	 *     'invalid-email'
	 * );
	 * ```
	 */
	public static function forField(string $field, string $message, mixed $value = NULL): self {
		return new self(
			message: "Ошибка валидации поля '{$field}': {$message}",
			errors : [$field => $message],
			field  : $field,
			value  : $value,
		);
	}

	/**
	 * Создает исключение для множественных ошибок
	 *
	 * Фабричный метод для создания исключения валидации
	 * с множественными ошибками для разных полей.
	 *
	 * @param   array<string, string>  $errors  Массив ошибок в формате ['field' => 'message']
	 *
	 * @return self Экземпляр исключения валидации
	 *
	 * @example
	 * ```php
	 * $errors = [
	 *     'title' => 'Название обязательно',
	 *     'year' => 'Год должен быть положительным',
	 *     'rating' => 'Рейтинг должен быть от 0 до 10'
	 * ];
	 * throw ValidationException::withErrors($errors);
	 * ```
	 */
	public static function withErrors(array $errors): self {
		$count   = count($errors);
		$message = match ($count) {
			0       => 'Неизвестная ошибка валидации',
			1       => 'Обнаружена 1 ошибка валидации',
			default => "Обнаружено {$count} ошибок валидации"
		};

		return new self(message: $message, errors: $errors);
	}

	/**
	 * Возвращает список всех ошибок валидации
	 *
	 * Возвращает ассоциативный массив, где ключи - названия полей,
	 * а значения - сообщения об ошибках валидации.
	 *
	 * @return array<string, string> Массив ошибок в формате ['field' => 'error_message']
	 *
	 * @example
	 * ```php
	 * $errors = $exception->getErrors();
	 * // Результат: ['title' => 'Название обязательно', 'year' => 'Год должен быть положительным']
	 * ```
	 */
	public function getErrors(): array {
		return $this->errors;
	}

	/**
	 * Возвращает поле, вызвавшее ошибку
	 *
	 * Возвращает название поля, которое не прошло валидацию.
	 * Может быть null, если ошибка не связана с конкретным полем.
	 *
	 * @return string|null Название поля или null
	 *
	 * @example
	 * ```php
	 * $field = $exception->getField();
	 * // Результат: 'title' или null
	 * ```
	 */
	public function getField(): ?string {
		return $this->field;
	}

	/**
	 * Возвращает значение, не прошедшее валидацию
	 *
	 * Возвращает значение, которое вызвало ошибку валидации.
	 * Полезно для диагностики и отладки проблем валидации.
	 *
	 * @return mixed Проблемное значение
	 *
	 * @example
	 * ```php
	 * $value = $exception->getValue();
	 * // Результат: null, пустая строка, отрицательное число и т.д.
	 * ```
	 */
	public function getValue(): mixed {
		return $this->value;
	}

	/**
	 * Возвращает первую ошибку валидации
	 *
	 * Возвращает текст первой ошибки из списка ошибок валидации.
	 * Полезно для быстрого отображения основной проблемы.
	 *
	 * @return string|null Текст первой ошибки или null, если ошибок нет
	 *
	 * @example
	 * ```php
	 * $firstError = $exception->getFirstError();
	 * // Результат: 'Название обязательно' или null
	 * ```
	 */
	public function getFirstError(): ?string {
		if ($this->hasErrors()) {
			$errors     = $this->errors;
			$firstValue = reset($errors);

			return is_string($firstValue) ? $firstValue : NULL;
		}

		return NULL;
	}

	/**
	 * Проверяет, есть ли ошибки валидации
	 *
	 * Удобный метод для проверки наличия ошибок валидации
	 * без необходимости проверки размера массива ошибок.
	 *
	 * @return bool True если есть ошибки, false если ошибок нет
	 *
	 * @example
	 * ```php
	 * if ($exception->hasErrors()) {
	 *     // Обработка ошибок
	 * }
	 * ```
	 */
	public function hasErrors(): bool {
		return !empty($this->errors);
	}

}