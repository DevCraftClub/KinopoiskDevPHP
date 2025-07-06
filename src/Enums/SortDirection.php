<?php

namespace KinopoiskDev\Enums;

/**
 * Enum для направления сортировки результатов поиска
 *
 * Этот enum определяет возможные направления сортировки данных
 * при выполнении запросов к API Kinopoisk.dev
 *
 * @package KinopoiskDev\Enums
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
enum SortDirection: string {

	/**
	 * Сортировка по возрастанию (от меньшего к большему, от А до Я)
	 */
	case ASC = 'asc';

	/**
	 * Сортировка по убыванию (от большего к меньшему, от Я до А)
	 */
	case DESC = 'desc';

	/**
	 * Возвращает противоположное направление сортировки
	 *
	 * Полезно для переключения направления сортировки в пользовательских интерфейсах
	 * или для реализации логики "toggle" сортировки.
	 *
	 * @return SortDirection Противоположное направление сортировки
	 */
	public function reverse(): SortDirection {
		return match ($this) {
			self::ASC  => self::DESC,
			self::DESC => self::ASC,
		};
	}

	/**
	 * Возвращает символьное представление направления
	 *
	 * Предоставляет краткое символьное представление направления сортировки
	 * для использования в пользовательских интерфейсах.
	 *
	 * @return string Символ направления сортировки ('↑' для ASC, '↓' для DESC)
	 */
	public function getSymbol(): string {
		return match ($this) {
			self::ASC  => '↑',
			self::DESC => '↓',
		};
	}

	/**
	 * Возвращает описательное название направления на русском языке
	 *
	 * Предоставляет человекочитаемое описание направления сортировки
	 * для отображения в русскоязычных интерфейсах.
	 *
	 * @return string Описание направления сортировки на русском языке
	 */
	public function getDescription(): string {
		return match ($this) {
			self::ASC  => 'По возрастанию',
			self::DESC => 'По убыванию',
		};
	}

	/**
	 * Возвращает краткое описание направления
	 *
	 * Предоставляет сокращенное описание направления сортировки
	 * для использования в компактных интерфейсах.
	 *
	 * @return string Краткое описание направления
	 */
	public function getShortDescription(): string {
		return match ($this) {
			self::ASC  => 'А→Я',
			self::DESC => 'Я→А',
		};
	}

	/**
	 * Проверяет, является ли направление возрастающим
	 *
	 * @return bool true, если направление ASC, false в противном случае
	 */
	public function isAscending(): bool {
		return $this === self::ASC;
	}

	/**
	 * Проверяет, является ли направление убывающим
	 *
	 * @return bool true, если направление DESC, false в противном случае
	 */
	public function isDescending(): bool {
		return $this === self::DESC;
	}

	/**
	 * Создает направление из строкового значения с fallback
	 *
	 * Безопасно создает экземпляр SortDirection из строки с возможностью
	 * указания значения по умолчанию при неудачном преобразовании.
	 *
	 * @param string $value Строковое значение направления
	 * @param SortDirection|null $default Значение по умолчанию (ASC если не указано)
	 * @return SortDirection Экземпляр SortDirection
	 */
	public static function fromString(string $value, ?SortDirection $default = null): SortDirection {
		return self::tryFrom(strtolower($value)) ?? $default ?? self::ASC;
	}

	/**
	 * Возвращает все доступные направления сортировки
	 *
	 * Статический метод для получения всех возможных направлений сортировки.
	 * Используется для создания интерфейсов выбора направления.
	 *
	 * @return array Массив всех направлений SortDirection
	 */
	public static function getAllDirections(): array {
		return [self::ASC, self::DESC];
	}
}