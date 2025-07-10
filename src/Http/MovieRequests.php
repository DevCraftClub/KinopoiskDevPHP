<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Enums\FilterField;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Models\MovieAward;
use KinopoiskDev\Responses\Api\MovieAwardDocsResponseDto;
use KinopoiskDev\Responses\Api\MovieDocsResponseDto;
use KinopoiskDev\Responses\Api\PossibleValueDto;
use KinopoiskDev\Responses\Api\SearchMovieResponseDto;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Utils\DataManager;

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
	 * Получает возможные значения для указанного поля фильтрации
	 *
	 * @api  /v1.4/movie/possible-values-by-field
	 * @link https://kinopoiskdev.readme.io/reference/moviecontroller_getpossiblevaluesbyfieldv1_4
	 *
	 * @param   string  $field  Поле для получения возможных значений
	 *
	 * @return array<array<string, mixed>> Массив возможных значений
	 * @throws KinopoiskDevException При неподдерживаемом поле
	 * @throws \\JsonException При ошибках парсинга JSON
	 */
	public function getPossibleValuesByField(string $field): array {
		$allowedFields = [
			FilterField::GENRES->value,
			FilterField::COUNTRIES->value,
			FilterField::TYPE->value,
			FilterField::TYPE_NUMBER->value,
			FilterField::STATUS->value,
		];

		if (!in_array($field, $allowedFields, TRUE)) {
			$fieldNames = implode(', ', $allowedFields);
			throw new KinopoiskDevException('Лишь следующие поля поддерживаются для этого запроса: ' . $fieldNames);
		}

		$queryParams = ['field' => $field];

		$response = $this->makeRequest('GET', 'movie/possible-values-by-field', $queryParams, 'v1');
		$data     = $this->parseResponse($response);

		return array_map(fn (PossibleValueDto $value) => $value->toArray(), $data);
	}

	/**
	 * Получает награды фильмов с возможностью фильтрации и пагинации
	 *
	 * Выполняет запрос к API Kinopoisk.dev для получения списка наград фильмов
	 * с поддержкой расширенной фильтрации и постраничной навигации.
	 * Автоматически создает объект фильтра при отсутствии переданного параметра.
	 *
	 * @api     /v1.4/movie/awards
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     \KinopoiskDev\Filter\MovieSearchFilter Класс для настройки фильтрации наград
	 * @see     \KinopoiskDev\Responses\Api\MovieAwardDocsResponseDto Структура ответа API
	 * @see     \KinopoiskDev\Models\MovieAward Модель отдельной награды фильма
	 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_findawardsv1_4
	 *
	 * @param   MovieSearchFilter|null  $filters  Объект фильтрации для поиска наград по различным критериям (жанры, страны, годы, рейтинги и т.д.).
	 *                                            При значении null создается новый экземпляр MovieSearchFilter без фильтров
	 * @param   int                     $page     Номер запрашиваемой страницы результатов, начиная с 1 (по умолчанию 1)
	 * @param   int                     $limit    Максимальное количество результатов на одной странице (по умолчанию 10, максимум ограничен API до 250)
	 *
	 * @return MovieAwardDocsResponseDto Объект ответа, содержащий массив наград фильмов и метаданные пагинации (общее количество,
	 *                                   количество страниц, текущая страница)
	 *
	 * @throws KinopoiskDevException     При ошибках валидации данных, неправильных параметрах запроса или проблемах с инициализацией объектов
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса к API (401, 403, 404)
	 * @throws \JsonException            При ошибках парсинга JSON-ответа от API, некорректном формате данных или повреждении ответа
	 */
	public function getMovieAwards(?MovieSearchFilter $filters = NULL, int $page = 1, int $limit = 10): MovieAwardDocsResponseDto {
		if (is_null($filters)) {
			$filters = new MovieSearchFilter();
		}

		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', 'movie/awards', $queryParams);
		$data     = $this->parseResponse($response);

		return new MovieAwardDocsResponseDto(
			docs : DataManager::parseObjectArray($data, 'docs', MovieAward::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * Ищет фильмы только по названию (упрощенный поиск)
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
		$data = $this->parseResponse($response);

		return new SearchMovieResponseDto(
			docs : array_map(fn ($movieData) => Movie::fromArray($movieData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
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
	 * @throws KinopoiskDevException|KinopoiskResponseException При ошибках API
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

		$response = $this->makeRequest('GET', 'movie', $queryParams);
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
	 *  Получает новейшие фильмы
	 *
	 * @param   int|null  $year   Год (по умолчанию: текущий год)
	 * @param   int       $page   Номер страницы
	 * @param   int       $limit  Результатов на странице
	 *
	 * @return MovieDocsResponseDto Новейшие фильмы
	 * @throws \JsonException При ошибках парсинга JSON
	 * @throws KinopoiskDevException|KinopoiskResponseException При ошибках API
	 */
	public function getLatestMovies(?int $year = NULL, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$year = $year ?? (int) date('Y');

		$filters = new MovieSearchFilter();
		$filters
			->year($year)
			->sortByCreated();

		return $this->searchMovies($filters, $page, $limit);
	}

	/**
	 * Получает фильмы по жанру
	 *
	 * @param   string|array<string>  $genres  Жанр(ы)
	 * @param   int           $page    Номер страницы
	 * @param   int           $limit   Результатов на странице
	 *
	 * @return MovieDocsResponseDto Фильмы указанного жанра
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException
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
	 * @param   string|array<string>  $countries  Страна/Страны
	 * @param   int           $page       Номер страницы
	 * @param   int           $limit      Результатов на странице
	 *
	 * @return MovieDocsResponseDto Фильмы из указанной страны
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException
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
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException
	 */
	public function getMoviesByYearRange(int $fromYear, int $toYear, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters
			->withYearBetween($fromYear, $toYear)
			->sortByYear();

		return $this->searchMovies($filters, $page, $limit);
	}

}
