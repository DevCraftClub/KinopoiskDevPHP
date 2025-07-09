<?php

namespace KinopoiskDev\Filter;

use KinopoiskDev\Utils\MovieFilter;
use KinopoiskDev\Utils\FilterTrait;

/**
 * Класс для создания фильтров при поиске фильмов
 *
 * Этот класс расширяет базовый MovieFilter и предоставляет
 * дополнительные методы для поиска фильмов
 *
 * @link https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4
 */
class MovieSearchFilter extends MovieFilter {

	use FilterTrait;

	/**
	 * Добавляет фильтр для поиска по альтернативному названию с использованием регулярного выражения
	 *
	 * @param   string  $query  Поисковый запрос
	 *
	 * @return $this
	 */
	public function searchByAlternativeName(string $query): self {
		$this->addFilter('alternativeName', $query, 'regex');

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска по всем названиям фильма
	 *
	 * @param   string  $query  Поисковый запрос
	 *
	 * @return $this
	 */
	public function searchByAllNames(string $query): self {
		$this->addFilter('names.name', $query, 'regex');

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска фильмов с количеством голосов выше указанного
	 *
	 * @param   int     $minVotes  Минимальное количество голосов
	 * @param   string  $field     Поле голосов (kp, imdb, tmdb, filmCritics, russianFilmCritics, await)
	 *
	 * @return $this
	 */
	public function withMinVotes(int $minVotes, string $field = 'kp'): self {
		$this->addFilter("votes.$field", $minVotes, 'gte');

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска фильмов в диапазоне голосов
	 *
	 * @param   int     $minVotes  Минимальное количество голосов
	 * @param   int     $maxVotes  Максимальное количество голосов
	 * @param   string  $field     Поле голосов (kp, imdb, tmdb, filmCritics, russianFilmCritics, await)
	 *
	 * @return $this
	 */
	public function withVotesBetween(int $minVotes, int $maxVotes, string $field = 'kp'): self {
		$this->votesRange($minVotes, $maxVotes, $field);

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска фильмов в диапазоне годов
	 *
	 * @param   int  $fromYear  Начальный год
	 * @param   int  $toYear    Конечный год
	 *
	 * @return $this
	 */
	public function withYearBetween(int $fromYear, int $toYear): self {
		// Используем новый метод yearRange вместо двух отдельных фильтров
		$this->yearRange($fromYear, $toYear);

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска фильмов по нескольким жанрам (И)
	 *
	 * @param   array  $genres  Массив жанров
	 *
	 * @return $this
	 */
	public function withAllGenres(array $genres): self {
		$this->addFilter('genres.name', $genres, 'all');

		return $this;
	}

	/**
	 * Добавляет фильтр для включения жанров (оператор +)
	 *
	 * @param   string|array  $genres  Жанр или массив жанров для включения
	 *
	 * @return $this
	 */
	public function withIncludedGenres(string|array $genres): self {
		$this->includeGenres($genres);

		return $this;
	}

	/**
	 * Добавляет фильтр для исключения жанров (оператор !)
	 *
	 * @param   string|array  $genres  Жанр или массив жанров для исключения
	 *
	 * @return $this
	 */
	public function withExcludedGenres(string|array $genres): self {
		$this->excludeGenres($genres);

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска фильмов по нескольким странам (И)
	 *
	 * @param   array  $countries  Массив стран
	 *
	 * @return $this
	 */
	public function withAllCountries(array $countries): self {
		$this->addFilter('countries.name', $countries, 'all');

		return $this;
	}

	/**
	 * Добавляет фильтр для включения стран (оператор +)
	 *
	 * @param   string|array  $countries  Страна или массив стран для включения
	 *
	 * @return $this
	 */
	public function withIncludedCountries(string|array $countries): self {
		$this->includeCountries($countries);

		return $this;
	}

	/**
	 * Добавляет фильтр для исключения стран (оператор !)
	 *
	 * @param   string|array  $countries  Страна или массив стран для исключения
	 *
	 * @return $this
	 */
	public function withExcludedCountries(string|array $countries): self {
		$this->excludeCountries($countries);

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска фильмов с участием указанного актера
	 *
	 * @param   string|int  $actor  Имя актера или его ID
	 *
	 * @return $this
	 */
	public function withActor(string|int $actor): self {
		if (is_int($actor)) {
			$this->filters['persons.id'] = $actor;
		} else {
			$this->addFilter('persons.name', $actor, 'regex');
		}
		$this->filters['persons.profession'] = 'актер';

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска фильмов указанного режиссера
	 *
	 * @param   string|int  $director  Имя режиссера или его ID
	 *
	 * @return $this
	 */
	public function withDirector(string|int $director): self {
		if (is_int($director)) {
			$this->filters['persons.id'] = $director;
		} else {
			$this->addFilter('persons.name', $director, 'regex');
		}
		$this->filters['persons.profession'] = 'режиссер';

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска только фильмов (не сериалов)
	 *
	 * @return $this
	 */
	public function onlyMovies(): self {
		$this->isSeries(FALSE);

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска только сериалов
	 *
	 * @return $this
	 */
	public function onlySeries(): self {
		$this->isSeries(TRUE);

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска фильмов из топ-250
	 *
	 * @return $this
	 */
	public function inTop250(): self {
		$this->addFilter('top250', 250, 'lte');

		return $this;
	}

	/**
	 * Добавляет фильтр для поиска фильмов из топ-10
	 *
	 * @return $this
	 */
	public function inTop10(): self {
		$this->addFilter('top10', 10, 'lte');

		return $this;
	}

	/**
	 * Добавляет фильтр по диапазону дат премьеры
	 *
	 * @param   string  $fromDate  Начальная дата в формате dd.mm.yyyy
	 * @param   string  $toDate    Конечная дата в формате dd.mm.yyyy
	 * @param   string  $country   Страна премьеры (russia, world, usa, ...)
	 *
	 * @return $this
	 */
	public function withPremiereRange(string $fromDate, string $toDate, string $country = 'world'): self {
		$this->premiereRange($fromDate, $toDate, $country);

		return $this;
	}

}
