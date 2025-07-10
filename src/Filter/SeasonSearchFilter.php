<?php

namespace KinopoiskDev\Filter;

use KinopoiskDev\Utils\MovieFilter;
use KinopoiskDev\Utils\FilterTrait;

/**
 * Класс для фильтров при поиске сезонов
 *
 * @package KinopoiskDev\Filter
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
class SeasonSearchFilter extends MovieFilter {

	use FilterTrait;

	/**
	 * Добавляет фильтр по номеру сезона
	 *
	 * @param   int     $number    Номер сезона
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function number(int $number, string $operator = 'eq'): self {
		$this->addFilter('number', $number, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по количеству эпизодов
	 *
	 * @param   int     $episodesCount  Количество эпизодов
	 * @param   string  $operator       Оператор сравнения
	 *
	 * @return $this
	 */
	public function episodesCount(int $episodesCount, string $operator = 'eq'): self {
		$this->addFilter('episodesCount', $episodesCount, $operator);

		return $this;
	}

	/**
	 * Исключение записей с пустыми значениями в указанных полях
	 *
	 * @param   array  $fields  Массив названий полей
	 *
	 * @return $this
	 */
	public function notNullFields(array $fields): self {
		foreach ($fields as $field) {
			$this->addFilter($field, null, 'ne');
		}
		return $this;
	}

}
