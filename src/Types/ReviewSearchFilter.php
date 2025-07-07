<?php

namespace KinopoiskDev\Types;

use KinopoiskDev\Utils\MovieFilter;
use KinopoiskDev\Utils\FilterTrait;

/**
 * Класс для фильтров при поиске отзывов
 *
 * @package KinopoiskDev\Types
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
class ReviewSearchFilter extends MovieFilter {

	use FilterTrait;

	/**
	 * Добавляет фильтр по автору
	 *
	 * @param   string  $author    Автор отзыва
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function author(string $author, string $operator = 'regex'): self {
		$this->addFilter('author', $author, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по тексту отзыва
	 *
	 * @param   string  $review    Текст отзыва
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function review(string $review, string $operator = 'regex'): self {
		$this->addFilter('review', $review, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по заголовку
	 *
	 * @param   string  $title     Заголовок отзыва
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function title(string $title, string $operator = 'regex'): self {
		$this->addFilter('title', $title, $operator);

		return $this;
	}

	/**
	 * Фильтр только для положительных отзывов
	 *
	 * @return $this
	 */
	public function onlyPositive(): self {
		return $this->type('Позитивный');
	}

	/**
	 * Фильтр только для отрицательных отзывов
	 *
	 * @return $this
	 */
	public function onlyNegative(): self {
		return $this->type('Негативный');
	}

	/**
	 * Фильтр только для нейтральных отзывов
	 *
	 * @return $this
	 */
	public function onlyNeutral(): self {
		return $this->type('Нейтральный');
	}

}
