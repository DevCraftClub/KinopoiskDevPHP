<?php

namespace KinopoiskDev\Filter;

use KinopoiskDev\Utils\MovieFilter;
use KinopoiskDev\Utils\FilterTrait;

/**
 * Класс для фильтров при поиске персон
 *
 * Расширяет базовый фильтр методами, специфичными для персон
 *
 * @package KinopoiskDev\Filter
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @link    https://kinopoiskdev.readme.io/reference/personcontroller_findmanybyqueryv1_4
 */
class PersonSearchFilter extends MovieFilter {

	use FilterTrait;

	/**
	 * Добавляет фильтр по возрасту
	 *
	 * @param   int     $age       Возраст
	 * @param   string  $operator  Оператор сравнения (eq, gte, lte, и т.д.)
	 *
	 * @return $this
	 */
	public function age(int $age, string $operator = 'eq'): self {
		$this->addFilter('age', $age, $operator);

		return $this;
	}
	

	/**
	 * Добавляет фильтр по полу
	 *
	 * @param   string  $sex  Пол (male, female)
	 *
	 * @return $this
	 */
	public function sex(string $sex): self {
		$this->addFilter('sex', $sex);

		return $this;
	}

	/**
	 * Добавляет фильтр по месту рождения
	 *
	 * @param   string  $birthPlace  Место рождения
	 * @param   string  $operator    Оператор сравнения
	 *
	 * @return $this
	 */
	public function birthPlace(string $birthPlace, string $operator = 'regex'): self {
		$this->addFilter('birthPlace.value', $birthPlace, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по дате смерти
	 *
	 * @param   string  $death     Дата смерти
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function death(string $death, string $operator = 'eq'): self {
		$this->addFilter('death', $death, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по дате рождения
	 *
	 * @param   string  $birthday  Дата рождения
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function birthday(string $birthday, string $operator = 'eq'): self {
		$this->addFilter('birthday', $birthday, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по количеству наград
	 *
	 * @param   int     $countAwards  Количество наград
	 * @param   string  $operator     Оператор сравнения
	 *
	 * @return $this
	 */
	public function countAwards(int $countAwards, string $operator = 'gte'): self {
		$this->addFilter('countAwards', $countAwards, $operator);

		return $this;
	}

	/**
	 * Фильтр только для актеров
	 *
	 * @return $this
	 */
	public function onlyActors(): self {
		return $this->profession('актер');
	}

	/**
	 * Добавляет фильтр по профессии
	 *
	 * @param   string  $profession  Профессия (актер, режиссер, сценарист, и т.д.)
	 * @param   string  $operator    Оператор сравнения
	 *
	 * @return $this
	 */
	public function profession(string $profession, string $operator = 'eq'): self {
		$this->addFilter('profession', $profession, $operator);

		return $this;
	}

	/**
	 * Фильтр только для режиссеров
	 *
	 * @return $this
	 */
	public function onlyDirectors(): self {
		return $this->profession('режиссер');
	}

	/**
	 * Фильтр только для сценаристов
	 *
	 * @return $this
	 */
	public function onlyWriters(): self {
		return $this->profession('сценарист');
	}

	/**
	 * Фильтр только для живых персон
	 *
	 * @return $this
	 */
	public function onlyAlive(): self {
		$this->addFilter('death', null, 'eq');

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
