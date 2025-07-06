<?php

namespace KinopoiskDev\Enums;

/**
 * Enum для операторов фильтрации
 *
 * Этот enum содержит все возможные операторы, которые можно использовать
 * при фильтрации данных через API Kinopoisk.dev
 */
enum FilterOperator: string {

	// Операторы сравнения
	case EQUALS              = 'eq';            // Равно
	case NOT_EQUALS          = 'ne';            // Не равно
	case GREATER_THAN        = 'gt';            // Больше
	case GREATER_THAN_EQUALS = 'gte';           // Больше или равно
	case LESS_THAN           = 'lt';            // Меньше
	case LESS_THAN_EQUALS    = 'lte';           // Меньше или равно

	// Операторы для массивов
	case IN     = 'in';                         // Значение содержится в массиве
	case NOT_IN = 'nin';                        // Значение не содержится в массиве
	case ALL    = 'all';                        // Все значения должны присутствовать

	// Операторы для строк
	case REGEX = 'regex';                       // Регулярное выражение

	// Специальные операторы
	case RANGE   = 'range';                     // Диапазон значений
	case INCLUDE = 'include';                   // Включить (для жанров и стран)
	case EXCLUDE = 'exclude';                   // Исключить (для жанров и стран)


	/**
	 * Возвращает оператор по умолчанию для указанного типа поля
	 */
	public static function getDefaultForFieldType(string $fieldType): self {
		return match ($fieldType) {
			'array'   => self::IN,
			'text'    => self::REGEX,
			default   => self::EQUALS
		};
	}

	/**
	 * Возвращает префикс для операторов включения/исключения
	 */
	public function getPrefix(): ?string {
		return match ($this) {
			self::INCLUDE => '+',
			self::EXCLUDE => '!',
			default       => NULL
		};
	}

	/**
	 * Проверяет, является ли оператор оператором диапазона
	 */
	public function isRangeOperator(): bool {
		return $this === self::RANGE;
	}

	/**
	 * Проверяет, является ли оператор оператором включения/исключения
	 */
	public function isIncludeExcludeOperator(): bool {
		return in_array($this, [self::INCLUDE, self::EXCLUDE]);
	}

}