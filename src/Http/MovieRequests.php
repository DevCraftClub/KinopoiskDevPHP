<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Responses\MovieDocsResponseDto;
use KinopoiskDev\Responses\SearchMovieResponseDto;
use KinopoiskDev\Types\MovieSearchFilter;

/**
 * Класс для API-запросов, связанных с фильмами
 *
 * Этот класс расширяет базовый класс Kinopoisk и предоставляет специализированные
 * методы для всех конечных точек фильмов API Kinopoisk.dev.
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @link    https://kinopoiskdev.readme.io/reference/
 */
class MovieRequests extends Kinopoisk {

	/**
	 * Получает фильм по его ID
	 *
	 * @api  /v1.4/movie/{id}
	 * @link https://kinopoiskdev.readme.io/reference/moviecontroller_findonev1_4
	 *
	 * @param   int  $movieId  Уникальный ID фильма
	 *
	 * @return Movie Фильм со всеми доступными данными
	 * @throws KinopoiskDevException При ошибках API или проблемах с сетью
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getMovieById(int $movieId): Movie {
		$response = $this->makeRequest('GET', "/movie/{$movieId}");
		$data     = $this->parseResponse($response);

		return Movie::fromArray($data);
	}

	/**
	 * Получает случайный фильм
	 *
	 * @api    /v1.4/movie/random
	 * @link   https://kinopoiskdev.readme.io/reference/moviecontroller_getrandommoviev1_4
	 *
	 * @param   MovieSearchFilter|null  $filters  Опциональные фильтры для случайного фильма
	 *
	 * @return Movie Случайный фильм, соответствующий фильтрам
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getRandomMovie(?MovieSearchFilter $filters = NULL): Movie {
		if (is_null($filters)) {
			$filters = new MovieSearchFilter();
		}
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', '/movie/random', $queryParams);
		$data     = $this->parseResponse($response);

		return Movie::fromArray($data);
	}

	/**
	 * ДОБАВЛЕНО: Получает возможные значения для определенного поля
	 *
	 * @api    /v1.4/movie/possible-values-by-field
	 * @link   https://kinopoiskdev.readme.io/reference/moviecontroller_getpossiblevaluesbyfieldname
	 *
	 * @param   string  $field  Поле, для которого нужно получить возможные значения
	 *
	 * @return array Массив с возможными значениями для поля
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getPossibleValuesByField(string $field): array {
		$queryParams = ['field' => $field];

		$response = $this->makeRequest('GET', '/movie/possible-values-by-field', $queryParams);
		$data     = $this->parseResponse($response);

		return $data;
	}

	/**
	 * ИЗМЕНЕНО: Исправленный метод наград
	 * Получает награды фильмов
	 *
	 * @api    /v1.4/movie/awards
	 * @link   https://kinopoiskdev.readme.io/reference/moviecontroller_findawardsv1_4
	 *
	 * @param   MovieSearchFilter|null  $filters  Фильтры для поиска наград
	 * @param   int                     $page     Номер страницы
	 * @param   int                     $limit    Результатов на странице
	 *
	 * @return MovieDocsResponseDto Фильмы с наградами
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getMovieAwards(?MovieSearchFilter $filters = NULL, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		if (is_null($filters)) {
			$filters = new MovieSearchFilter();
		}

		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		// KORRIGIERT: Verwendung des korrekten Endpunkts
		$response = $this->makeRequest('GET', '/movie/awards', $queryParams);
		$data     = $this->parseResponse($response);

		return new MovieDocsResponseDto(
			docs : array_map(fn ($movieData) => Movie::fromArray($movieData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * ДОБАВЛЕНО: Ищет фильмы только по названию (упрощенный поиск)
	 *
	 * @api  /v1.4/movie/search
	 * @link https://kinopoiskdev.readme.io/reference/moviecontroller_searchmoviev1_4
	 *
	 * @param   int     $limit  Результатов на странице
	 * @param   string  $query  Поисковый запрос
	 * @param   int     $page   Номер страницы
	 *
	 * @return SearchMovieResponseDto Результаты поиска
	 * @throws KinopoiskDevException При ошибках API
	 */
	public function searchByName(string $query, int $page = 1, int $limit = 10): SearchMovieResponseDto {
		$filters = new MovieSearchFilter();
		$filters->addFilter('query', $query);

		$response = $this->makeRequest('GET', '/movie/search', $filters->getFilters());

		return new SearchMovieResponseDto(
			docs : array_map(fn ($movieData) => Movie::fromArray($movieData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * Получает фильмы с высоким рейтингом на Кинопоиске
	 *
	 * @param   float  $minRating  Минимальный рейтинг (по умолчанию: 7.0)
	 * @param   int    $page       Номер страницы
	 * @param   int    $limit      Результатов на странице
	 *
	 * @return MovieDocsResponseDto Фильмы с высоким рейтингом
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getTopRatedMovies(float $minRating = 7.0, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters
			->withMinRating($minRating, 'kp')
			->withMinVotes(1000, 'kp')
			->sortByKinopoiskRating();

		return $this->searchMovies($filters, $page, $limit);
	}

	/**
	 * Ищет фильмы по различным критериям
	 *
	 * @api    /v1.4/movie
	 * @link   https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4
	 *
	 * @param   MovieSearchFilter|null  $filters  Объект фильтра для поиска
	 * @param   int                     $page     Номер страницы (по умолчанию: 1)
	 * @param   int                     $limit    Количество результатов на странице (по умолчанию: 10, макс: 250)
	 *
	 * @return MovieDocsResponseDto Результаты поиска с пагинацией
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function searchMovies(?MovieSearchFilter $filters = NULL, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		if ($limit > 250) {
			throw new KinopoiskDevException('Лимит не должен превышать 250');
		}
		if ($page < 1) {
			throw new KinopoiskDevException('Номер страницы не должен быть меньше 1');
		}

		if (is_null($filters)) {
			$filters = new MovieSearchFilter();
		}

		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', '/movie', $queryParams);
		$data     = $this->parseResponse($response);

		return new MovieDocsResponseDto(
			docs : array_map(fn ($movieData) => Movie::fromArray($movieData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * ДОБАВЛЕНО: Получает новейшие фильмы
	 *
	 * @param   int|null  $year   Год (по умолчанию: текущий год)
	 * @param   int       $page   Номер страницы
	 * @param   int       $limit  Результатов на странице
	 *
	 * @return MovieDocsResponseDto Новейшие фильмы
	 * @throws \JsonException При ошибках парсинга JSON
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException При ошибках API
	 */
	public function getLatestMovies(?int $year = NULL, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$year = $year ?? date('Y');

		$filters = new MovieSearchFilter();
		$filters
			->year($year)
			->sortByCreated();

		return $this->searchMovies($filters, $page, $limit);
	}

	/**
	 * Получает фильмы по жанру
	 *
	 * @param   string|array  $genres  Жанр(ы)
	 * @param   int           $page    Номер страницы
	 * @param   int           $limit   Результатов на странице
	 *
	 * @return MovieDocsResponseDto Фильмы указанного жанра
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getMoviesByGenre(string|array $genres, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters
			->withIncludedGenres($genres)
			->sortByKinopoiskRating();

		return $this->searchMovies($filters, $page, $limit);
	}

	/**
	 * Получает фильмы по стране
	 *
	 * @param   string|array  $countries  Страна/Страны
	 * @param   int           $page       Номер страницы
	 * @param   int           $limit      Результатов на странице
	 *
	 * @return MovieDocsResponseDto Фильмы из указанной страны
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getMoviesByCountry(string|array $countries, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters
			->withIncludedCountries($countries)
			->sortByKinopoiskRating();

		return $this->searchMovies($filters, $page, $limit);
	}

	/**
	 * Получает фильмы по диапазону лет
	 *
	 * @param   int  $fromYear  Начальный год
	 * @param   int  $toYear    Конечный год
	 * @param   int  $page      Номер страницы
	 * @param   int  $limit     Результатов на странице
	 *
	 * @return MovieDocsResponseDto Фильмы из указанного периода
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getMoviesByYearRange(int $fromYear, int $toYear, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters
			->withYearBetween($fromYear, $toYear)
			->sortByYear();

		return $this->searchMovies($filters, $page, $limit);
	}

}
