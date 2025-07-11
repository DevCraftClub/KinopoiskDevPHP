<?php

namespace KinopoiskDev\Filter;

use KinopoiskDev\Enums\SortDirection;
use KinopoiskDev\Enums\SortField;

/**
 * Класс для представления критериев сортировки
 *
 * Инкапсулирует информацию о поле сортировки и направлении,
 * предоставляя удобные методы для работы с параметрами сортировки.
 *
 * @package KinopoiskDev\Filter
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
class SortCriteria {

	/**
	 * Конструктор для создания критериев сортировки
	 *
	 * @param   SortField      $field      Поле для сортировки
	 * @param   SortDirection  $direction  Направление сортировки
	 */
	public function __construct(
		public  SortField     $field,
		public  SortDirection $direction,
	) {}

	/**
	 * Возвращает строковое представление критериев
	 *
	 * @return string Человекочитаемое описание критериев сортировки
	 */
	public function __toString(): string {
		return sprintf(
			'%s (%s)',
			$this->field->getDescription(),
			$this->direction->getDescription(),
		);
	}

	/**
	 * Создает критерии сортировки с автоматическим направлением по умолчанию
	 *
	 * Фабричный метод, который создает SortCriteria используя рекомендуемое
	 * направление сортировки для указанного поля.
	 *
	 * @param   SortField  $field  Поле для сортировки
	 *
	 * @return self Новый экземпляр SortCriteria с направлением по умолчанию
	 */
	public static function create(SortField $field): self {
		return new self($field, $field->getDefaultDirection());
	}

	/**
	 * Создает критерии сортировки по возрастанию
	 *
	 * @param   SortField  $field  Поле для сортировки
	 *
	 * @return self Новый экземпляр SortCriteria с направлением ASC
	 */
	public static function ascending(SortField $field): self {
		return new self($field, SortDirection::ASC);
	}

	/**
	 * Создает критерии сортировки по убыванию
	 *
	 * @param   SortField  $field  Поле для сортировки
	 *
	 * @return self Новый экземпляр SortCriteria с направлением DESC
	 */
	public static function descending(SortField $field): self {
		return new self($field, SortDirection::DESC);
	}

	/**
	 * Создает экземпляр SortCriteria из массива данных
	 *
	 * @param   array<string, mixed> $data Массив с данными для создания объекта
	 *
	 * @return self|null Новый экземпляр SortCriteria или null при некорректных данных
	 */
	public static function fromArray(array $data): ?self {
		if (!isset($data['field'])) {
			return NULL;
		}

		$field = $data['field'];
		if (!$field instanceof SortField) {
			// Try to convert from string if needed
			$field = SortField::tryFrom(is_string($field) ? $field : '');
			if (!$field) {
				return NULL;
			}
		}

		$direction = $data['direction'] ?? null;
		if ($direction === null) {
			$direction = $field->getDefaultDirection();
		} elseif (!$direction instanceof SortDirection) {
			// Try to convert from string if needed
			$direction = SortDirection::tryFrom(is_string($direction) ? $direction : '');
			if (!$direction) {
				$direction = $field->getDefaultDirection();
			}
		}

		return new self($field, $direction);
	}

	/**
	 * Создает экземпляр SortCriteria из строковых значений
	 *
	 * @param   string      $field     Строковое значение поля
	 * @param   string|null  $direction  Строковое значение направления (опционально)
	 *
	 * @return self|null Новый экземпляр SortCriteria или null при неудачном преобразовании
	 */
	public static function fromStrings(string $field, ?string $direction = NULL): ?self {
		$sortField = SortField::tryFrom($field);
		if (!$sortField) {
			return NULL;
		}

		$sortDirection = $direction
			? SortDirection::fromString($direction, $sortField->getDefaultDirection())
			: $sortField->getDefaultDirection();

		return new self($sortField, $sortDirection);
	}

	/**
	 * Преобразует критерии в массив
	 *
	 * @return array<string, string> Ассоциативный массив с ключами 'field' и 'direction'
	 */
	public function toArray(): array {
		return [
			'field'     => $this->field->value,
			'direction' => $this->direction->value,
		];
	}

	/**
	 * Преобразует критерии в строку для URL параметров API
	 *
	 * Формирует строковое представление критериев сортировки в формате,
	 * ожидаемом API Kinopoisk.dev (например: "-rating.kp" для убывания).
	 *
	 * @return string Строковое представление для API
	 */
	public function toApiString(): string {
		$prefix = $this->direction === SortDirection::DESC ? '-' : '';

		return $prefix . $this->field->value;
	}

	/**
	 * Возвращает противоположные критерии сортировки
	 *
	 * Создает новый экземпляр SortCriteria с тем же полем,
	 * но противоположным направлением сортировки.
	 *
	 * @return self Новый экземпляр с обращенным направлением
	 */
	public function reverse(): self {
		return new self($this->field, $this->direction->reverse());
	}

	/**
	 * Проверяет, совпадают ли критерии по полю
	 *
	 * @param   SortCriteria  $other  Другие критерии для сравнения
	 *
	 * @return bool true, если поля совпадают, false в противном случае
	 */
	public function hasSameField(SortCriteria $other): bool {
		return $this->field === $other->field;
	}

	/**
	 * Проверяет полное равенство критериев
	 *
	 * @param   SortCriteria  $other  Другие критерии для сравнения
	 *
	 * @return bool true, если поле и направление совпадают, false в противном случае
	 */
	public function equals(SortCriteria $other): bool {
		return $this->field === $other->field && $this->direction === $other->direction;
	}

	/**
	 * Возвращает краткое строковое представление
	 *
	 * @return string Краткое описание с символом направления
	 */
	public function toShortString(): string {
		return sprintf(
			'%s %s',
			$this->field->getDescription(),
			$this->direction->getSymbol(),
		);
	}

	/**
	 * Проверяет, является ли поле рейтинговым
	 *
	 * @return bool true, если поле сортировки является рейтинговым
	 */
	public function isRatingSort(): bool {
		return $this->field->isRatingField();
	}

	/**
	 * Проверяет, является ли поле полем голосов
	 *
	 * @return bool true, если поле сортировки является полем голосов
	 */
	public function isVotesSort(): bool {
		return $this->field->isVotesField();
	}

	/**
	 * Проверяет, является ли сортировка по дате
	 *
	 * @return bool true, если поле сортировки является полем даты
	 */
	public function isDateSort(): bool {
		return $this->field->isDateField();
	}

	/**
	 * Возвращает тип данных поля сортировки
	 *
	 * @return string Тип данных поля ('number', 'string', 'date')
	 */
	public function getFieldDataType(): string {
		return $this->field->getDataType();
	}

}