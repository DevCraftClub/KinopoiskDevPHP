<?php

namespace KinopoiskDev\Utils;

/**
 * Класс для создания фильтров при поиске фильмов
 *
 * Этот класс предоставляет методы для построения параметров фильтрации
 * при поиске фильмов через API Kinopoisk.dev
 *
 * @link https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4
 */
class MovieFilter {

	use SortManager;

	/**
	 * Массив параметров фильтрации
	 */
	/** @var array<string, mixed> */
	protected array $filters = [];

	/**
	 * Добавляет фильтр по ID фильма
	 *
	 * @param   int|array<int>  $id        ID фильма или массив ID
	 * @param   string     $operator  Оператор сравнения (eq, ne, in, nin)
	 *
	 * @return $this
	 */
	public function id(int|array $id, string $operator = 'eq'): self {
		$this->addFilter('id', $id, $operator);

		return $this;
	}

	/**
	 * Добавляет произвольный фильтр
	 *
	 * @param   string  $field     Поле для фильтрации
	 * @param   mixed   $value     Значение фильтра
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function addFilter(string $field, mixed $value, string $operator = 'eq'): self {
		// Оптимизированная обработка различных типов фильтров
		switch ($operator) {
			// Обработка диапазонов
			case 'range':
				if (is_array($value) && count($value) === 2) {
					$this->filters[$field] = $value[0] . '-' . $value[1];
				}
				break;

			// Обработка включения/исключения для жанров и стран
			case 'include':
			case 'exclude':
				// Быстрая проверка на жанры или страны
				$isGenreOrCountry = str_starts_with($field, 'genres.name') || str_starts_with($field, 'countries.name');
				if ($isGenreOrCountry) {
					$prefix = ($operator === 'include') ? '+' : '!';

					if (is_array($value)) {
						if (!isset($this->filters[$field])) {
							$this->filters[$field] = [];
						}

						// Предварительно выделяем память для массива
						$count = count($value);
						if ($count > 0) {
							// Используем array_map вместо цикла для лучшей производительности
							$prefixedValues        = array_map(fn ($item) => $prefix . $item, $value);
							$this->filters[$field] = array_merge($this->filters[$field], $prefixedValues);
						}
					} else {
						$this->filters[$field][] = $prefix . $value;
					}
					break;
				}
			// Если не жанр/страна, обрабатываем как обычный фильтр
			// Намеренно пропускаем break, чтобы перейти к default

			// Обработка обычных фильтров
			default:
				$this->filters[$field . '.' . $operator] = $value;
				break;
		}

		return $this;
	}

	/**
	 * Добавляет фильтр по внешнему ID фильма
	 *
	 * @param   array<string, string>  $externalId  Массив внешних ID (imdb, tmdb, kpHD)
	 *
	 * @return $this
	 */
	public function externalId(array $externalId): self {
		$this->filters['externalId'] = $externalId;

		return $this;
	}

	/**
	 * Добавляет фильтр по названию фильма
	 *
	 * @param   string  $name      Название фильма
	 * @param   string  $operator  Оператор сравнения (eq, ne, in, nin, regex)
	 *
	 * @return $this
	 */
	public function name(string $name, string $operator = 'eq'): self {
		$this->addFilter('name', $name, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по английскому названию фильма
	 *
	 * @param   string  $enName    Английское название фильма
	 * @param   string  $operator  Оператор сравнения (eq, ne, in, nin, regex)
	 *
	 * @return $this
	 */
	public function enName(string $enName, string $operator = 'eq'): self {
		$this->addFilter('enName', $enName, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по альтернативному названию фильма
	 *
	 * @param   string  $alternativeName  Альтернативное название фильма
	 * @param   string  $operator         Оператор сравнения (eq, ne, in, nin, regex)
	 *
	 * @return $this
	 */
	public function alternativeName(string $alternativeName, string $operator = 'eq'): self {
		$this->addFilter('alternativeName', $alternativeName, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по всем названиям фильма
	 *
	 * @param   string|array<string>  $names     Название или массив названий
	 * @param   string        $operator  Оператор сравнения (eq, ne, in, nin, regex)
	 *
	 * @return $this
	 */
	public function names(string|array $names, string $operator = 'eq'): self {
		$this->addFilter('names.name', $names, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по описанию фильма
	 *
	 * @param   string  $description  Описание фильма
	 * @param   string  $operator     Оператор сравнения (eq, ne, regex)
	 *
	 * @return $this
	 */
	public function description(string $description, string $operator = 'regex'): self {
		$this->addFilter('description', $description, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по краткому описанию фильма
	 *
	 * @param   string  $shortDescription  Краткое описание фильма
	 * @param   string  $operator          Оператор сравнения (eq, ne, regex)
	 *
	 * @return $this
	 */
	public function shortDescription(string $shortDescription, string $operator = 'regex'): self {
		$this->addFilter('shortDescription', $shortDescription, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по слогану фильма
	 *
	 * @param   string  $slogan    Слоган фильма
	 * @param   string  $operator  Оператор сравнения (eq, ne, regex)
	 *
	 * @return $this
	 */
	public function slogan(string $slogan, string $operator = 'regex'): self {
		$this->addFilter('slogan', $slogan, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по типу фильма
	 *
	 * @param   string  $type      Тип фильма (movie, tv-series, cartoon, anime, animated-series, tv-show)
	 * @param   string  $operator  Оператор сравнения (eq, ne, in, nin)
	 *
	 * @return $this
	 */
	public function type(string $type, string $operator = 'eq'): self {
		$this->addFilter('type', $type, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по номеру типа фильма
	 *
	 * @param   int     $typeNumber  Номер типа фильма (1-6)
	 * @param   string  $operator    Оператор сравнения (eq, ne, in, nin, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function typeNumber(int $typeNumber, string $operator = 'eq'): self {
		$this->addFilter('typeNumber', $typeNumber, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по признаку сериала
	 *
	 * @param   bool  $isSeries  Является ли фильм сериалом
	 *
	 * @return $this
	 */
	public function isSeries(bool $isSeries): self {
		$this->filters['isSeries'] = $isSeries;

		return $this;
	}

	/**
	 * Добавляет фильтр по статусу фильма
	 *
	 * @param   string  $status    Статус фильма (filming, pre-production, completed, announced, post-production)
	 * @param   string  $operator  Оператор сравнения (eq, ne, in, nin)
	 *
	 * @return $this
	 */
	public function status(string $status, string $operator = 'eq'): self {
		$this->addFilter('status', $status, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по году выпуска
	 *
	 * @param   int     $year      Год выпуска
	 * @param   string  $operator  Оператор сравнения (eq, ne, in, nin, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function year(int $year, string $operator = 'eq'): self {
		$this->addFilter('year', $year, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по диапазону годов выпуска
	 *
	 * @param   int  $fromYear  Начальный год
	 * @param   int  $toYear    Конечный год
	 *
	 * @return $this
	 */
	public function yearRange(int $fromYear, int $toYear): self {
		$this->addFilter('year', [$fromYear, $toYear], 'range');

		return $this;
	}

	/**
	 * Добавляет фильтр по годам релиза
	 *
	 * @param   array<int>  $releaseYears  Массив годов релиза
	 *
	 * @return $this
	 */
	public function releaseYears(array $releaseYears): self {
		$this->filters['releaseYears'] = $releaseYears;

		return $this;
	}

	/**
	 * Добавляет фильтр по рейтингу
	 *
	 * @param   float|array<float>  $rating    Рейтинг или массив с параметрами рейтинга
	 * @param   string       $field     Поле рейтинга (kp, imdb, tmdb, filmCritics, russianFilmCritics, await)
	 * @param   string       $operator  Оператор сравнения (eq, ne, in, nin, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function rating(float|array $rating, string $field = 'kp', string $operator = 'gte'): self {
		if (is_array($rating)) {
			$this->filters['rating'] = $rating;
		} else {
			$this->addFilter("rating.$field", $rating, $operator);
		}

		return $this;
	}

	/**
	 * Добавляет фильтр по диапазону рейтинга
	 *
	 * @param   float   $minRating  Минимальный рейтинг
	 * @param   float   $maxRating  Максимальный рейтинг
	 * @param   string  $field      Поле рейтинга (kp, imdb, tmdb, filmCritics, russianFilmCritics, await)
	 *
	 * @return $this
	 */
	public function ratingRange(float $minRating, float $maxRating, string $field = 'kp'): self {
		$this->addFilter("rating.$field", [$minRating, $maxRating], 'range');

		return $this;
	}

	/**
	 * Добавляет фильтр по рейтингу MPAA
	 *
	 * @param   string  $ratingMpaa  Рейтинг MPAA (g, pg, pg-13, r, nc-17)
	 * @param   string  $operator    Оператор сравнения (eq, ne, in, nin)
	 *
	 * @return $this
	 */
	public function ratingMpaa(string $ratingMpaa, string $operator = 'eq'): self {
		$this->addFilter('ratingMpaa', $ratingMpaa, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по возрастному рейтингу
	 *
	 * @param   int     $ageRating  Возрастной рейтинг
	 * @param   string  $operator   Оператор сравнения (eq, ne, in, nin, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function ageRating(int $ageRating, string $operator = 'eq'): self {
		$this->addFilter('ageRating', $ageRating, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по голосам
	 *
	 * @param   int|array<int>  $votes     Количество голосов или массив с параметрами голосов
	 * @param   string     $field     Поле голосов (kp, imdb, tmdb, filmCritics, russianFilmCritics, await)
	 * @param   string     $operator  Оператор сравнения (eq, ne, in, nin, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function votes(int|array $votes, string $field = 'kp', string $operator = 'gte'): self {
		if (is_array($votes)) {
			$this->filters['votes'] = $votes;
		} else {
			$this->addFilter("votes.$field", $votes, $operator);
		}

		return $this;
	}

	/**
	 * Добавляет фильтр по диапазону голосов
	 *
	 * @param   int     $minVotes  Минимальное количество голосов
	 * @param   int     $maxVotes  Максимальное количество голосов
	 * @param   string  $field     Поле голосов (kp, imdb, tmdb, filmCritics, russianFilmCritics, await)
	 *
	 * @return $this
	 */
	public function votesRange(int $minVotes, int $maxVotes, string $field = 'kp'): self {
		$this->addFilter("votes.$field", [$minVotes, $maxVotes], 'range');

		return $this;
	}

	/**
	 * Добавляет фильтр по информации о сезонах
	 *
	 * @param   array  $seasonsInfo  Информация о сезонах
	 *
	 * @return $this
	 */
	public function seasonsInfo(array $seasonsInfo): self {
		$this->filters['seasonsInfo'] = $seasonsInfo;

		return $this;
	}

	/**
	 * Добавляет фильтр по бюджету
	 *
	 * @param   array  $budget  Информация о бюджете
	 *
	 * @return $this
	 */
	public function budget(array $budget): self {
		$this->filters['budget'] = $budget;

		return $this;
	}

	/**
	 * Добавляет фильтр по аудитории
	 *
	 * @param   array  $audience  Информация об аудитории
	 *
	 * @return $this
	 */
	public function audience(array $audience): self {
		$this->filters['audience'] = $audience;

		return $this;
	}

	/**
	 * Добавляет фильтр по длительности фильма
	 *
	 * @param   int     $movieLength  Длительность фильма в минутах
	 * @param   string  $operator     Оператор сравнения (eq, ne, in, nin, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function movieLength(int $movieLength, string $operator = 'eq'): self {
		$this->addFilter('movieLength', $movieLength, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по длительности серии
	 *
	 * @param   int     $seriesLength  Длительность серии в минутах
	 * @param   string  $operator      Оператор сравнения (eq, ne, in, nin, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function seriesLength(int $seriesLength, string $operator = 'eq'): self {
		$this->addFilter('seriesLength', $seriesLength, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по общей длительности сериала
	 *
	 * @param   int     $totalSeriesLength  Общая длительность сериала в минутах
	 * @param   string  $operator           Оператор сравнения (eq, ne, in, nin, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function totalSeriesLength(int $totalSeriesLength, string $operator = 'eq'): self {
		$this->addFilter('totalSeriesLength', $totalSeriesLength, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по жанрам
	 *
	 * @param   string|array<string>  $genres    Жанр или массив жанров
	 * @param   string        $operator  Оператор сравнения (eq, ne, in, nin)
	 *
	 * @return $this
	 */
	public function genres(string|array $genres, string $operator = 'in'): self {
		$this->addFilter('genres.name', $genres, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр для включения жанров (оператор +)
	 *
	 * @param   string|array<string>  $genres  Жанр или массив жанров для включения
	 *
	 * @return $this
	 */
	public function includeGenres(string|array $genres): self {
		$this->addFilter('genres.name', $genres, 'include');

		return $this;
	}

	/**
	 * Добавляет фильтр для исключения жанров (оператор !)
	 *
	 * @param   string|array<string>  $genres  Жанр или массив жанров для исключения
	 *
	 * @return $this
	 */
	public function excludeGenres(string|array $genres): self {
		$this->addFilter('genres.name', $genres, 'exclude');

		return $this;
	}

	/**
	 * Добавляет фильтр по странам
	 *
	 * @param   string|array<string>  $countries  Страна или массив стран
	 * @param   string        $operator   Оператор сравнения (eq, ne, in, nin)
	 *
	 * @return $this
	 */
	public function countries(string|array $countries, string $operator = 'in'): self {
		$this->addFilter('countries.name', $countries, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр для включения стран (оператор +)
	 *
	 * @param   string|array<string>  $countries  Страна или массив стран для включения
	 *
	 * @return $this
	 */
	public function includeCountries(string|array $countries): self {
		$this->addFilter('countries.name', $countries, 'include');

		return $this;
	}

	/**
	 * Добавляет фильтр для исключения стран (оператор !)
	 *
	 * @param   string|array<string>  $countries  Страна или массив стран для исключения
	 *
	 * @return $this
	 */
	public function excludeCountries(string|array $countries): self {
		$this->addFilter('countries.name', $countries, 'exclude');

		return $this;
	}

	/**
	 * Добавляет фильтр по постеру
	 *
	 * @param   array  $poster  Информация о постере
	 *
	 * @return $this
	 */
	public function poster(array $poster): self {
		$this->filters['poster'] = $poster;

		return $this;
	}

	/**
	 * Добавляет фильтр по фоновому изображению
	 *
	 * @param   array  $backdrop  Информация о фоновом изображении
	 *
	 * @return $this
	 */
	public function backdrop(array $backdrop): self {
		$this->filters['backdrop'] = $backdrop;

		return $this;
	}

	/**
	 * Добавляет фильтр по логотипу
	 *
	 * @param   array  $logo  Информация о логотипе
	 *
	 * @return $this
	 */
	public function logo(array $logo): self {
		$this->filters['logo'] = $logo;

		return $this;
	}

	/**
	 * Добавляет фильтр по наличию билетов в продаже
	 *
	 * @param   bool  $ticketsOnSale  Наличие билетов в продаже
	 *
	 * @return $this
	 */
	public function ticketsOnSale(bool $ticketsOnSale): self {
		$this->filters['ticketsOnSale'] = $ticketsOnSale;

		return $this;
	}

	/**
	 * Добавляет фильтр по видео
	 *
	 * @param   array  $videos  Информация о видео
	 *
	 * @return $this
	 */
	public function videos(array $videos): self {
		$this->filters['videos'] = $videos;

		return $this;
	}

	/**
	 * Добавляет фильтр по сетям
	 *
	 * @param   array  $networks  Информация о сетях
	 *
	 * @return $this
	 */
	public function networks(array $networks): self {
		$this->filters['networks'] = $networks;

		return $this;
	}

	/**
	 * Добавляет фильтр по персонам
	 *
	 * @param   array  $persons  Информация о персонах
	 *
	 * @return $this
	 */
	public function persons(array $persons): self {
		$this->filters['persons'] = $persons;

		return $this;
	}

	/**
	 * Добавляет фильтр по фактам
	 *
	 * @param   array  $facts  Информация о фактах
	 *
	 * @return $this
	 */
	public function facts(array $facts): self {
		$this->filters['facts'] = $facts;

		return $this;
	}

	/**
	 * Добавляет фильтр по сборам
	 *
	 * @param   array  $fees  Информация о сборах
	 *
	 * @return $this
	 */
	public function fees(array $fees): self {
		$this->filters['fees'] = $fees;

		return $this;
	}

	/**
	 * Добавляет фильтр по премьере
	 *
	 * @param   array  $premiere  Информация о премьере
	 *
	 * @return $this
	 */
	public function premiere(array $premiere): self {
		$this->filters['premiere'] = $premiere;

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
	public function premiereRange(string $fromDate, string $toDate, string $country = 'world'): self {
		$this->addFilter("premiere.$country", [$fromDate, $toDate], 'range');

		return $this;
	}

	/**
	 * Добавляет фильтр по похожим фильмам
	 *
	 * @param   array  $similarMovies  Информация о похожих фильмах
	 *
	 * @return $this
	 */
	public function similarMovies(array $similarMovies): self {
		$this->filters['similarMovies'] = $similarMovies;

		return $this;
	}

	/**
	 * Добавляет фильтр по сиквелам и приквелам
	 *
	 * @param   array  $sequelsAndPrequels  Информация о сиквелах и приквелах
	 *
	 * @return $this
	 */
	public function sequelsAndPrequels(array $sequelsAndPrequels): self {
		$this->filters['sequelsAndPrequels'] = $sequelsAndPrequels;

		return $this;
	}

	/**
	 * Добавляет фильтр по доступности просмотра
	 *
	 * @param   array  $watchability  Информация о доступности просмотра
	 *
	 * @return $this
	 */
	public function watchability(array $watchability): self {
		$this->filters['watchability'] = $watchability;

		return $this;
	}

	/**
	 * Добавляет фильтр по спискам
	 *
	 * @param   array  $lists  Информация о списках
	 *
	 * @return $this
	 */
	public function lists(array $lists): self {
		$this->filters['lists'] = $lists;

		return $this;
	}

	/**
	 * Добавляет фильтр по топ-10
	 *
	 * @param   int     $top10     Позиция в топ-10
	 * @param   string  $operator  Оператор сравнения (eq, ne, in, nin, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function top10(int $top10, string $operator = 'eq'): self {
		$this->addFilter('top10', $top10, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по топ-250
	 *
	 * @param   int     $top250    Позиция в топ-250
	 * @param   string  $operator  Оператор сравнения (eq, ne, in, nin, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function top250(int $top250, string $operator = 'eq'): self {
		$this->addFilter('top250', $top250, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по дате обновления
	 *
	 * @param   string  $updatedAt  Дата обновления
	 * @param   string  $operator   Оператор сравнения (eq, ne, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function updatedAt(string $updatedAt, string $operator = 'eq'): self {
		$this->addFilter('updatedAt', $updatedAt, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по дате создания
	 *
	 * @param   string  $createdAt  Дата создания
	 * @param   string  $operator   Оператор сравнения (eq, ne, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function createdAt(string $createdAt, string $operator = 'eq'): self {
		$this->addFilter('createdAt', $createdAt, $operator);

		return $this;
	}

	/**
	 * Возвращает массив фильтров
	 *
	 * @return array<string, mixed>
	 */
	public function getFilters(): array {
		$filters    = $this->filters;
		$sortString = $this->getSortString();
		if ($sortString !== NULL) {
			$filters['sort'] = $sortString;
		}

		return $filters;
	}

	/**
	 * Исключение записей с пустыми значениями в указанных полях
	 *
	 * @param   array<string>  $fields  Массив названий полей
	 *
	 * @return $this
	 */
	public function notNullFields(array $fields): self {
		foreach ($fields as $field) {
			$this->addFilter($field, null, 'ne');
		}
		return $this;
	}

	/**
	 * Сбрасывает все фильтры
	 *
	 * @return $this
	 */
	public function reset(): self {
		$this->filters = [];
		$this->clearSort();

		return $this;
	}

}
