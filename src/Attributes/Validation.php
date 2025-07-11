<?php

declare(strict_types=1);

namespace KinopoiskDev\Attributes;

use Attribute;

/**
 * Атрибут для валидации свойств модели
 *
 * Предоставляет декларативный способ задания правил валидации
 * для свойств моделей с использованием PHP 8.3 Attributes.
 * Поддерживает различные типы валидации: обязательные поля,
 * ограничения длины, диапазоны значений, регулярные выражения.
 *
 * @package KinopoiskDev\Attributes
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Services\ValidationService Сервис валидации
 * @see     \KinopoiskDev\Exceptions\ValidationException Исключения валидации
 *
 * @example
 * ```php
 * class Movie {
 *     #[Validation(required: true, minLength: 1, maxLength: 255)]
 *     public string $title;
 *
 *     #[Validation(min: 1900, max: 2030)]
 *     public int $year;
 *
 *     #[Validation(pattern: '/^[a-zA-Z0-9\s]+$/')]
 *     public string $genre;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Validation {

	/**
	 * Конструктор атрибута валидации
	 *
	 * Создает новый экземпляр атрибута валидации с указанными правилами.
	 * Все параметры являются опциональными и могут быть настроены
	 * в зависимости от требований к конкретному полю.
	 *
	 * @param   bool           $required       Обязательное ли поле (по умолчанию false)
	 * @param   int|null       $minLength      Минимальная длина строки (для строковых полей)
	 * @param   int|null       $maxLength      Максимальная длина строки (для строковых полей)
	 * @param   float|null     $min            Минимальное значение (для числовых полей)
	 * @param   float|null     $max            Максимальное значение (для числовых полей)
	 * @param   string|null    $pattern        Регулярное выражение для проверки формата
	 * @param   array<string>  $allowedValues  Допустимые значения (для enum-подобных полей)
	 * @param   string|null    $customMessage  Кастомное сообщение об ошибке валидации
	 *
	 * @example
	 * ```php
	 * // Обязательное поле с ограничением длины
	 * #[Validation(required: true, minLength: 1, maxLength: 100)]
	 * public string $name;
	 *
	 * // Числовое поле с диапазоном
	 * #[Validation(min: 0, max: 10)]
	 * public float $rating;
	 *
	 * // Поле с регулярным выражением
	 * #[Validation(pattern: '/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i')]
	 * public string $email;
	 *
	 * // Поле с допустимыми значениями
	 * #[Validation(allowedValues: ['movie', 'series', 'cartoon'])]
	 * public string $type;
	 * ```
	 */
	public function __construct(
		public bool    $required = FALSE,
		public ?int    $minLength = NULL,
		public ?int    $maxLength = NULL,
		public ?float   $min = NULL,
		public ?float   $max = NULL,
		public ?string $pattern = NULL,
		public array   $allowedValues = [],
		public ?string $customMessage = NULL,
	) {}

}