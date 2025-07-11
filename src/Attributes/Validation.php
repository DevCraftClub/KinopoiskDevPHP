<?php

declare(strict_types=1);

namespace KinopoiskDev\Attributes;

use Attribute;

/**
 * Атрибут для валидации свойств модели
 *
 * Предоставляет декларативный способ задания правил валидации
 * для свойств моделей с использованием PHP 8.3 Attributes.
 *
 * @package KinopoiskDev\Attributes
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final  class Validation {

	/**
	 * Конструктор атрибута валидации
	 *
	 * @param   bool        $required      Обязательное ли поле
	 * @param   int|null    $minLength     Минимальная длина (для строк)
	 * @param   int|null    $maxLength     Максимальная длина (для строк)
	 * @param   float|null  $min           Минимальное значение (для чисел)
	 * @param   float|null  $max           Максимальное значение (для чисел)
	 * @param   string|null $pattern       Регулярное выражение
	 * @param   array<string> $allowedValues Допустимые значения
	 * @param   string|null $customMessage Кастомное сообщение об ошибке
	 */
	public function __construct(
		public bool $required = false,
		public ?int $minLength = null,
		public ?int $maxLength = null,
		public ?float $min = null,
		public ?float $max = null,
		public ?string $pattern = null,
		public array $allowedValues = [],
		public ?string $customMessage = null,
	) {}
}

/**
 * Атрибут для указания источника поля в API
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final  class ApiField {

	public function __construct(
		public ?string $name = null,
		public bool $nullable = true,
		public mixed $default = null,
	) {}
}

/**
 * Атрибут для конфиденциальных полей
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final  class Sensitive {

	public function __construct(
		public bool $hideInJson = true,
		public bool $hideInArray = false,
	) {}
}