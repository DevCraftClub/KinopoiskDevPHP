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
		static $cache = [];

		if (!isset($cache[$fieldType])) {
			$cache[$fieldType] = match ($fieldType) {
				'array'   => self::IN,
				'text'    => self::REGEX,
				'include_exclude' => self::IN,
				default   => self::EQUALS
			};
		}

		return $cache[$fieldType];
	}

	/**
	 * Возвращает префикс для операторов включения/исключения
	 */
	public function getPrefix(): ?string {
		static $cache = [];

		if (!array_key_exists($this->value, $cache)) {
			$cache[$this->value] = match ($this) {
				self::INCLUDE => '+',
				self::EXCLUDE => '!',
				default       => NULL
			};
		}

		return $cache[$this->value];
	}

	/**
	 * Проверяет, является ли оператор оператором диапазона
	 */
	public function isRangeOperator(): bool {
		static $cache = [];

		if (!isset($cache[$this->value])) {
			$cache[$this->value] = $this === self::RANGE;
		}

		return $cache[$this->value];
	}

	/**
	 * Проверяет, является ли оператор оператором включения/исключения
	 */
	public function isIncludeExcludeOperator(): bool {
		static $cache = [];

		if (!isset($cache[$this->value])) {
			$cache[$this->value] = $this === self::INCLUDE || $this === self::EXCLUDE;
		}

		return $cache[$this->value];
	}

}
