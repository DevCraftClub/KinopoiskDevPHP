<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Enums\FilterField;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Models\MovieAward;
use KinopoiskDev\Responses\Api\MovieAwardDocsResponseDto;
use KinopoiskDev\Responses\Api\MovieDocsResponseDto;
use KinopoiskDev\Responses\Api\PossibleValueDto;
use KinopoiskDev\Responses\Api\SearchMovieResponseDto;
use KinopoiskDev\Utils\DataManager;

/**
 * Класс для API-запросов, связанных с фильмами
 *
 * Предоставляет полный набор методов для работы с фильмами через API Kinopoisk.dev.
 * Включает поиск фильмов, получение детальной информации, наград, случайных фильмов
 * и возможных значений для фильтрации. Поддерживает расширенную фильтрацию,
 * пагинацию и обработку ошибок.
 *
 * Основные возможности:
 * - Поиск фильмов по различным критериям
 * - Получение детальной информации о фильме
 * - Работа с наградами фильмов
 * - Получение случайных фильмов
 * - Получение возможных значений для фильтров
 * - Специализированные методы для популярных запросов
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Filter\MovieSearchFilter Для настройки фильтрации
 * @see     \KinopoiskDev\Models\Movie Модель фильма
 * @see     \KinopoiskDev\Models\MovieAward Модель награды фильма
 * @see     \KinopoiskDev\Responses\Api\MovieDocsResponseDto Ответ с фильмами
 * @see     \KinopoiskDev\Responses\Api\SearchMovieResponseDto Ответ поиска
 * @link    https://kinopoiskdev.readme.io/reference/
 *
 * @example
 * ```php
 * $movieRequests = new MovieRequests('your-api-token');
 *
 * // Получение фильма по ID
 * $movie = $movieRequests->getMovieById(123);
 *
 * // Поиск фильмов
 * $filter = new MovieSearchFilter();
 * $filter->year(2023)->rating(7.0);
 * $results = $movieRequests->searchMovies($filter, 1, 20);
 *
 * // Получение случайного фильма
 * $randomMovie = $movieRequests->getRandomMovie();
 *
 * // Получение наград
 * $awards = $movieRequests->getMovieAwards(null, 1, 50);
 * ```
 */
class MovieRequests extends Kinopoisk {

	/**
	 * Получает фильм по его ID
	 *
	 * Выполняет запрос к API для получения полной информации о фильме
	 * по его уникальному идентификатору. Возвращает объект Movie
	 * со всеми доступными данными: названием, годом, рейтингами,
	 * актерами, режиссерами, описанием и другими метаданными.
	 *
	 * @api     /v1.4/movie/{id}
	 * @since   1.0.0
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_findonev1_4
	 *
	 * @param   int  $movieId  Уникальный ID фильма в базе данных Kinopoisk
	 *
	 * @return Movie Объект фильма со всеми доступными данными
	 * @throws KinopoiskDevException При ошибках API или проблемах с сетью
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса (401, 403, 404)
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * $movie = $movieRequests->getMovieById(123);
	 * echo $movie->name; // Название фильма
	 * echo $movie->year; // Год выпуска
	 * ```
	 */
	public function getMovieById(int $movieId): Movie {
		$response = $this->makeRequest('GET', "movie/{$movieId}");
		$data     = $this->parseResponse($response);

		return Movie::fromArray($data);
	}

	/**
	 * Получает случайный фильм
	 *
	 * Возвращает случайно выбранный фильм из базы данных Kinopoisk.
	 * Поддерживает опциональную фильтрацию для получения случайного
	 * фильма, соответствующего определенным критериям (год, жанр,
	 * рейтинг и т.д.).
	 *
	 * @api     /v1.4/movie/random
	 * @since   1.0.0
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_getrandommoviev1_4
	 *
	 * @param   MovieSearchFilter|null  $filters  Опциональные фильтры для ограничения выбора случайного фильма
	 *
	 * @return Movie Случайный фильм, соответствующий переданным фильтрам
	 * @throws KinopoiskDevException При ошибках API или валидации
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Получить любой случайный фильм
	 * $randomMovie = $movieRequests->getRandomMovie();
	 *
	 * // Получить случайный фильм 2023 года с рейтингом выше 7.0
	 * $filter = new MovieSearchFilter();
	 * $filter->year(2023)->rating(7.0);
	 * $randomMovie = $movieRequests->getRandomMovie($filter);
	 * ```
	 */
	public function getRandomMovie(?MovieSearchFilter $filters = NULL): Movie {
		if (is_null($filters)) {
			$filters = new MovieSearchFilter();
		}
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', 'movie/random', $queryParams);
		$data     = $this->parseResponse($response);

		return Movie::fromArray($data);
	}

	/**
	 * Получает возможные значения для указанного поля фильтрации
	 *
	 * Возвращает список всех возможных значений для определенных полей
	 * фильтрации, таких как жанры, страны, типы фильмов и статусы.
	 * Полезно для создания выпадающих списков или автодополнения
	 * в пользовательских интерфейсах.
	 *
	 * @api     /v1.4/movie/possible-values-by-field
	 * @since   1.0.0
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_getpossiblevaluesbyfieldv1_4
	 *
	 * @param   string  $field  Поле для получения возможных значений (genres, countries, type, type_number, status)
	 *
	 * @return array<array<string, mixed>> Массив возможных значений с полями name и slug
	 * @throws KinopoiskDevException При передаче неподдерживаемого поля
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Получить все жанры
	 * $genres = $movieRequests->getPossibleValuesByField('genres');
	 *
	 * // Получить все страны
	 * $countries = $movieRequests->getPossibleValuesByField('countries');
	 *
	 * // Получить типы фильмов
	 * $types = $movieRequests->getPossibleValuesByField('type');
	 * ```
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

		return array_map(fn (array $value) => PossibleValueDto::fromArray($value)->toArray(), $data);
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
	 * @param   int                     $limit    Максимальное количество результатов на одной странице (по умолчанию 10, максимум ограничен API до
	 *                                            250)
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
	 * Выполняет поиск фильмов по названию с использованием
	 * встроенного поискового движка API. Поддерживает частичное
	 * совпадение и нечеткий поиск. Удобен для быстрого поиска
	 * по названию фильма без сложной фильтрации.
	 *
	 * @api     /v1.4/movie/search
	 * @since   1.0.0
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_searchmoviev1_4
	 *
	 * @param   string  $query  Поисковый запрос (название фильма)
	 * @param   int     $page   Номер страницы результатов (по умолчанию: 1)
	 * @param   int     $limit  Количество результатов на странице (по умолчанию: 10)
	 *
	 * @return SearchMovieResponseDto Результаты поиска с пагинацией
	 * @throws KinopoiskDevException При ошибках API или валидации
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Поиск фильма по названию
	 * $results = $movieRequests->searchByName('Матрица');
	 *
	 * // Поиск с пагинацией
	 * $results = $movieRequests->searchByName('Терминатор', 2, 20);
	 * ```
	 */
	public function searchByName(string $query, int $page = 1, int $limit = 10): SearchMovieResponseDto {
		$filters = new MovieSearchFilter();
		$filters->addFilter('query', $query);

		$response = $this->makeRequest('GET', 'movie/search', $filters->getFilters());
		$data     = $this->parseResponse($response);

		return new SearchMovieResponseDto(
			docs : array_map(fn ($movieData) => Movie::fromArray($movieData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * Получает последние фильмы
	 *
	 * Возвращает список последних фильмов, отсортированных по дате
	 * выхода. Поддерживает фильтрацию по году для получения
	 * фильмов конкретного года. Удобен для отображения новинок
	 * или актуального контента.
	 *
	 * @api     /v1.4/movie
	 * @since   1.0.0
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4
	 *
	 * @param   int|null  $year   Год для фильтрации (по умолчанию: текущий год)
	 * @param   int       $page   Номер страницы результатов (по умолчанию: 1)
	 * @param   int       $limit  Количество результатов на странице (по умолчанию: 10)
	 *
	 * @return MovieDocsResponseDto Результаты поиска с пагинацией
	 * @throws KinopoiskDevException При ошибках API или валидации
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Получить последние фильмы текущего года
	 * $latest = $movieRequests->getLatestMovies();
	 *
	 * // Получить фильмы 2023 года
	 * $movies2023 = $movieRequests->getLatestMovies(2023, 1, 50);
	 * ```
	 */
	public function getLatestMovies(?int $year = NULL, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);

		if ($year !== NULL) {
			$filters->addFilter('year', $year);
		}

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
	 * Получает фильмы по жанру
	 *
	 * Возвращает список фильмов определенного жанра или жанров,
	 * отсортированных по рейтингу Kinopoisk. Поддерживает как
	 * одиночный жанр, так и массив жанров для более точной
	 * фильтрации.
	 *
	 * @since   1.0.0
	 *
	 * @param   string|array<string>  $genres  Жанр или массив жанров для фильтрации
	 * @param   int                   $page    Номер страницы результатов (по умолчанию: 1)
	 * @param   int                   $limit   Количество результатов на странице (по умолчанию: 10)
	 *
	 * @return MovieDocsResponseDto Фильмы указанного жанра с пагинацией
	 * @throws KinopoiskDevException При ошибках API или валидации
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Получить боевики
	 * $actionMovies = $movieRequests->getMoviesByGenre('боевик');
	 *
	 * // Получить комедии и драмы
	 * $movies = $movieRequests->getMoviesByGenre(['комедия', 'драма'], 1, 30);
	 * ```
	 */
	public function getMoviesByGenre(string|array $genres, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters
			->withIncludedGenres($genres)
			->sortByKinopoiskRating();

		return $this->searchMovies($filters, $page, $limit);
	}

	/**
	 * Ищет фильмы по различным критериям
	 *
	 * Основной метод для поиска фильмов с использованием расширенной
	 * фильтрации. Поддерживает фильтрацию по году, жанру, стране,
	 * рейтингу, типу фильма и многим другим критериям. Включает
	 * валидацию параметров и автоматическую пагинацию.
	 *
	 * @api     /v1.4/movie
	 * @since   1.0.0
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4
	 *
	 * @param   MovieSearchFilter|null  $filters  Объект фильтра для настройки критериев поиска
	 * @param   int                     $page     Номер страницы результатов (по умолчанию: 1)
	 * @param   int                     $limit    Количество результатов на странице (по умолчанию: 10, максимум: 250)
	 *
	 * @return MovieDocsResponseDto Результаты поиска с пагинацией
	 * @throws KinopoiskDevException При ошибках валидации или превышении лимитов
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Простой поиск всех фильмов
	 * $results = $movieRequests->searchMovies();
	 *
	 * // Поиск с фильтрами
	 * $filter = new MovieSearchFilter();
	 * $filter->year(2023)->rating(7.0)->genre('боевик');
	 * $results = $movieRequests->searchMovies($filter, 1, 50);
	 * ```
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
	 * Получает фильмы по стране
	 *
	 * Возвращает список фильмов из определенной страны или стран,
	 * отсортированных по рейтингу Kinopoisk. Поддерживает как
	 * одиночную страну, так и массив стран для получения
	 * фильмов из нескольких стран одновременно.
	 *
	 * @since   1.0.0
	 *
	 * @param   string|array<string>  $countries  Страна или массив стран для фильтрации
	 * @param   int                   $page       Номер страницы результатов (по умолчанию: 1)
	 * @param   int                   $limit      Количество результатов на странице (по умолчанию: 10)
	 *
	 * @return MovieDocsResponseDto Фильмы из указанной страны с пагинацией
	 * @throws KinopoiskDevException При ошибках API или валидации
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Получить американские фильмы
	 * $usMovies = $movieRequests->getMoviesByCountry('США');
	 *
	 * // Получить фильмы из нескольких стран
	 * $movies = $movieRequests->getMoviesByCountry(['США', 'Великобритания'], 1, 25);
	 * ```
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
	 * Возвращает список фильмов, выпущенных в указанном диапазоне лет,
	 * отсортированных по году выпуска. Полезен для получения фильмов
	 * определенного периода или десятилетия.
	 *
	 * @since   1.0.0
	 *
	 * @param   int  $fromYear  Начальный год диапазона (включительно)
	 * @param   int  $toYear    Конечный год диапазона (включительно)
	 * @param   int  $page      Номер страницы результатов (по умолчанию: 1)
	 * @param   int  $limit     Количество результатов на странице (по умолчанию: 10)
	 *
	 * @return MovieDocsResponseDto Фильмы из указанного периода с пагинацией
	 * @throws KinopoiskDevException При ошибках API или валидации
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Получить фильмы 90-х годов
	 * $movies90s = $movieRequests->getMoviesByYearRange(1990, 1999);
	 *
	 * // Получить фильмы последнего десятилетия
	 * $recentMovies = $movieRequests->getMoviesByYearRange(2014, 2024, 1, 100);
	 * ```
	 */
	public function getMoviesByYearRange(int $fromYear, int $toYear, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters
			->withYearBetween($fromYear, $toYear)
			->sortByYear();

		return $this->searchMovies($filters, $page, $limit);
	}

}
