<?php

namespace KinopoiskDev\Utils;

use KinopoiskDev\Enums\ReviewType;

/**
 * Трейт для общих методов фильтрации
 *
 * Этот трейт предоставляет общие методы фильтрации, которые могут использоваться
 * в различных классах фильтров. Он следует принципу DRY (Don't Repeat Yourself),
 * централизуя общую логику фильтрации.
 *
 * @package KinopoiskDev\Utils
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
trait FilterTrait {

	/**
	 * Добавляет фильтр по ID фильма
	 *
	 * @param   int  $movieId  ID фильма
	 *
	 * @return $this
	 */
	public function movieId(int $movieId): self {
		$this->addFilter('movieId', $movieId);

		return $this;
	}

	/**
	 * Добавляет фильтр по названию
	 *
	 * @param   string  $name      Название
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function name(string $name, string $operator = 'eq'): self {
		$this->addFilter('name', $name, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по английскому названию
	 *
	 * @param   string  $enName    Английское название
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function enName(string $enName, string $operator = 'eq'): self {
		$this->addFilter('enName', $enName, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по типу
	 *
	 * @param   string|\KinopoiskDev\Enums\ReviewType  $type      Тип
	 * @param   string                                 $operator  Оператор сравнения
	 *
	 * @return \KinopoiskDev\Filter\MovieSearchFilter|\KinopoiskDev\Filter\ImageSearchFilter|\KinopoiskDev\Filter\KeywordSearchFilter|\KinopoiskDev\Filter\PersonSearchFilter|\KinopoiskDev\Filter\ReviewSearchFilter|\KinopoiskDev\Filter\SeasonSearchFilter|\KinopoiskDev\Filter\StudioSearchFilter|\KinopoiskDev\Utils\FilterTrait
	 */
	public function type(string|ReviewType $type, string $operator = 'eq'): self {
		$type = is_string($type)? $type : $type->value;
		$this->addFilter('type', $type, $operator);

		return $this;
	}

	/**
	 * Добавляет поисковый фильтр по названию с использованием регулярных выражений
	 *
	 * @param   string  $query  Поисковый запрос
	 *
	 * @return $this
	 */
	public function searchByName(string $query): self {
		$this->addFilter('name', $query, 'regex');

		return $this;
	}

	/**
	 * Добавляет поисковый фильтр по английскому названию с использованием регулярных выражений
	 *
	 * @param   string  $query  Поисковый запрос
	 *
	 * @return $this
	 */
	public function searchByEnName(string $query): self {
		$this->addFilter('enName', $query, 'regex');

		return $this;
	}

	/**
	 * Добавляет поисковый фильтр по описанию с использованием регулярных выражений
	 *
	 * @param   string  $query  Поисковый запрос
	 *
	 * @return $this
	 */
	public function searchByDescription(string $query): self {
		$this->addFilter('description', $query, 'regex');

		return $this;
	}

	/**
	 * Добавляет фильтр по минимальному рейтингу
	 *
	 * @param   float   $minRating  Минимальный рейтинг
	 * @param   string  $field      Поле рейтинга (kp, imdb и т.д.)
	 *
	 * @return $this
	 */
	public function withMinRating(float $minRating, string $field = 'kp'): self {
		$this->addFilter("rating.$field", $minRating, 'gte');

		return $this;
	}

	/**
	 * Добавляет фильтр по максимальному рейтингу
	 *
	 * @param   float   $maxRating  Максимальный рейтинг
	 * @param   string  $field      Поле рейтинга (kp, imdb и т.д.)
	 *
	 * @return $this
	 */
	public function withMaxRating(float $maxRating, string $field = 'kp'): self {
		$this->addFilter("rating.$field", $maxRating, 'lte');

		return $this;
	}

	/**
	 * Добавляет фильтр по диапазону рейтинга
	 *
	 * @param   float   $minRating  Минимальный рейтинг
	 * @param   float   $maxRating  Максимальный рейтинг
	 * @param   string  $field      Поле рейтинга (kp, imdb и т.д.)
	 *
	 * @return $this
	 */
	public function withRatingBetween(float $minRating, float $maxRating, string $field = 'kp'): self {
		$this->addFilter("rating.$field", [$minRating, $maxRating], 'range');

		return $this;
	}

	/**
	 * Добавляет фильтр по диапазону
	 *
	 * @param   string  $field     Имя поля
	 * @param   int     $minValue  Минимальное значение
	 * @param   int     $maxValue  Максимальное значение
	 *
	 * @return $this
	 */
	protected function addRangeFilter(string $field, int $minValue, int $maxValue): self {
		$this->addFilter($field, [$minValue, $maxValue], 'range');

		return $this;
	}

	/**
	 * Добавляет фильтр по диапазону сезонов
	 *
	 * @param   int  $fromSeason  Начальный сезон
	 * @param   int  $toSeason    Конечный сезон
	 *
	 * @return $this
	 */
	public function seasonRange(int $fromSeason, int $toSeason): self {
		return $this->addRangeFilter('number', $fromSeason, $toSeason);
	}

	/**
	 * Добавляет фильтр по возрастному диапазону
	 *
	 * @param   int  $minAge  Минимальный возраст
	 * @param   int  $maxAge  Максимальный возраст
	 *
	 * @return $this
	 */
	public function ageRange(int $minAge, int $maxAge): self {
		return $this->addRangeFilter('age', $minAge, $maxAge);
	}
}
